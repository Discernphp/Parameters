<?php namespace Discern\Parameter\Template\Contract;

use Discern\Parameter\Contract\ParameterCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Contract\ParameterStringParserInterface;
use Discern\Parameter\Struct\Contract\StructInterface;
use Discern\Parameter\Struct\Contract\StructFactoryInterface;
use Discern\Parameter\Template\Contract\TemplatedClassInterface;

trait ClassTemplateTrait {
  public function __invoke()
  {
    return call_user_func_array([$this, 'populate'], func_get_args());
  }
  /**
   * gets the name of the templated class.
   * @return string name of class being templated
   */
  public function getTemplatedClass()
  {
    return $this->templated_class;
  }

  /**
   * gets properties of the class being templated
   * @param  array  $filters filter options for template properties being returned
   * @return array assoc array of class property and values
   */
  public function getTemplateProperties(array $filters = [])
  {
    $properties = [];

    foreach ($this->template_properties as $id) {
      $properties[$id] = $this->{$id};
    }

    if (isset($filters['properties'])) {
      $properties = array_intersect_key(
        $filter['properties'], $properties
      );
    }

    if (isset($filters['contains_params'])) {
      if (!is_array($filters['contains_params'])) {
        throw new \InvalidArgumentException(
          'expected type `Array` of template_properties for filter["contains_params"], 
           but received '.gettype($filters['contains_params'])
        );
      }
      $properties = $this->filterContainingParamDefinition(
        $properties,
        $filters['contains_params']
      );
    }

    return $properties;
  }

  /**
   * @param  array $arguments assoc array of class template parameters
   * @param  array $options array of render options
   * @return  object - return value of `getTemplatedClass`
   */

  public function populate(array $arguments, array $options = [])
  {
    if (isset($options['env'])) {
      if (!$options['env'] instanceof ParameterCollectionInterface) {
        throw new \InvalidArgumentException(
          'expected type `ParameterCollectionInterface` for options["env"], but received '.gettype($options['env'])
        );
      }
      $env = $options['env'];
    }

    $filters = isset($options['filters']) ? $options['filters'] : [];

    $properties = $this->getTemplateProperties($filters);
    $env = isset($env) ? $env : $this->getParameterCollectionFactory()->make();
    $injection = $this->getParameterStringParser()->arrayInjectParameters($properties, $arguments, $env);
    if (!$injection->isClean()) {
      $exception = new ParameterInjectionException();
      $exception->setParameterInjection($injection);
      throw $exception;
    }

    $instance = isset($options['instance']) ? $options['instance'] : $this->makeTemplatedClassInstance();
    $struct = $this->makeParameterStruct($env->freeze())->setProperties($injection->getObjects());
    return $this->populateInstance($instance, $injection->getOutput(), $struct);
  }

  /**
   * @param Discern\Parameter\Contract\ParameterCollectionInterface $env
   * @param Discern\Parameter\Contract\TypeFactoryCollectionInterface $factory
   * @return Discern\Parameter\Struct\Contract\StructInterface
   */

  protected function makeParameterStruct(ParameterCollectionInterface $env, TypeFactoryCollectionInterface $factories = null)
  {
    $factories = $factories ?: $this->getParameterTypeFactoryCollection();
    return $this->getParameterStructFactory()->make($env, $factories);
  }

  /**
   * @return object - return value of `getTemplatedClass`
   */

  protected function makeTemplatedClassInstance()
  {
    $class = $this->getTemplatedClass();
    return new $class;
  }

  /**
   * @param  Discern\Parameter\Template\Contract\TemplatedClassInterface $instance
   * @param  array $properties - assoc array of class properties and values
   * @param  Discern\Parameter\Struct\Contract\StructInterface $struct - array 
   */

  protected function populateInstance(TemplatedClassInterface $instance, array $properties, StructInterface $struct = null)
  {
    foreach ($properties as $id => $value) {
      $this->getObjectAccessor()->set($instance, $id, $value);
    }

    if ($struct) {
      $instance->setParameterStruct($struct);
    }

    return $instance->setClassTemplate($this);
  }

  /**
   * returns properties containing given parameter ids 
   * @param  array $properties 
   * @param  array $param_ids
   * @return array properties containing given param ids
   */
  protected function filterContainingParamDefinition(array $properties, array $param_ids)
  {
    $properties_containing_param = [];
    $parser = $this->getParameterStringParser();
    foreach ($properties as $key => $value) {
      if (is_array($value)) {
        $nested_properties = $this->filterContainingParamDefinition([$key => $value], $param_ids);
        if (count($nested_properties)) {
          $properties_containing_param[$key] = $nested_properties;
        }
        continue;
      }

      if ($parser->stringContainsParameterDefinition($value, $param_ids)) {
        $properties_containing_param[$key] = $value;
      }
    }
    return $properties_containing_param;
  }
}