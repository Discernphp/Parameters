<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterConfigCollectionFactoryInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Contract\ParameterStringParserInterface;
use Discern\Parameter\Struct\Contract\ParameterStructInterface;
use Discern\Parameter\Struct\Contract\ParameterStructFactoryInterface;
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
      if (!$options['env'] instanceof ParameterConfigCollectionInterface) {
        throw new \InvalidArgumentException(
          'expected type `ParameterConfigCollectionInterface` for options["env"], but received '.gettype($options['env'])
        );
      }
      $env = $options['env'];
    }

    $filters = isset($options['filters']) ? $options['filters'] : [];

    $properties = $this->getTemplateProperties($filters);
    $env = isset($env) ? $env : $this->getParameterConfigCollectionFactory()->make();
    $injection = $this->getParser()->arrayInjectParameters($properties, $arguments, $env);
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
   * @return Discern\Parameter\Struct\Contract\ParameterStructFactoryInterface
   */

  public function getParameterStructFactory()
  {
    return $this->struct_factory;
  }

  /**
   * @param Discern\Parameter\Struct\Contract\ParameterStructFactoryInterface $factory
   * @return self
   */

  public function setParameterStructFactory(ParameterStructFactoryInterface $factory)
  {
    $this->struct_factory = $factory;
    return $this;
  }

  /**
   * [getParser description]
   * @return [type] [description]
   */
  public function getParser()
  {
    return $this->parser;
  }

  /**
   * [setParser description]
   * @param ParameterStringParserInterface $parser [description]
   */
  public function setParser(ParameterStringParserInterface $parser)
  {
    $this->parser = $parser;
    return $this;
  }

  /**
   * [getParameterConfigCollectionFactory description]
   * @return [type] [description]
   */
  public function getParameterConfigCollectionFactory()
  {
    return $this->param_collection_factory;
  }

  /**
   * [getParameterFactoryCollection description]
   * @return [type] [description]
   */
  public function getParameterFactoryCollection()
  {
    return $this->param_factory_collection;
  }

  /**
   * [setParameterFactoryCollection description]
   * @param ParameterFactoryCollectionInterface $factories [description]
   */
  public function setParameterFactoryCollection(ParameterFactoryCollectionInterface $factories)
  {
    $this->param_factory_collection = $factories;
    return $this;
  }

  /**
   * [setParameterConfigCollectionFactory description]
   */
  public function setParameterConfigCollectionFactory(ParameterConfigCollectionFactoryInterface $factory)
  {
    $this->param_collection_factory = $factory;
    return $this;
  }

  /**
   * @param Discern\Parameter\Contract\ParameterConfigCollectionInterface $env
   * @param Discern\Parameter\Contract\ParameterFactoryCollectionInterface $factory
   * @return Discern\Parameter\Struct\Contract\ParameterStructInterface
   */

  protected function makeParameterStruct(ParameterConfigCollectionInterface $env, ParameterFactoryCollectionInterface $factories = null)
  {
    $factories = $factories ?: $this->getParameterFactoryCollection();
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
   * @param  Discern\Parameter\Struct\Contract\ParameterStructInterface $struct - array 
   */

  protected function populateInstance(TemplatedClassInterface $instance, array $properties, ParameterStructInterface $struct = null)
  {
    foreach ($properties as $id => $value) {
      $instance->{$id} = $value;
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
    $parser = $this->getParser();
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