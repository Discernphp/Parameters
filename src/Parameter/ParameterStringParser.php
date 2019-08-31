<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigCollectionFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Contract\ParameterStringParserInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Contract\ParameterInjectionFactoryInterface;
use Discern\Parameter\Object\Contract\ObjectAccessorInterface;
use Discern\Parameter\Contract\ParameterConfigChildFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigChildInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;

class ParameterStringParser implements ParameterStringParserInterface {
  public function extractParameterDefinitions($string)
  {
    if (!is_string($string)) {
      throw new \InvalidArgumentException(
        sprintf(
          "%s::extractParameterDefinitions() expected string as parameter 1, received %s",
          get_class($this),
          gettype($string)
        )
      );
    }

    $paramProperties=[];
    $matches=[];
    preg_match_all('/{(.*?)}/', $string, $matches);
    return $matches[1];
  }

  // converts parameter string into object
  public function parseParameterString($string, ParameterConfigCollectionInterface $env = null)
  {
    $parts = explode(':', $string, 2);
    $is_optional = null;
    $default_args = null;
    $output_method = null;

    $id = $parts[0];

    if (strpos($id, '?') === strlen($id)-1) {
      $id = substr($id, 0, -1);
      $is_optional = true;
    }

    if (strpos($id, '.') !== false) {
      list($id, $output_method) = explode('.', $id, 2);
      if (!$env || !$env->exists($id)) {
        throw new InvalidParameterConfigException("Parameter `{$id}` not found");
      }
      $parent_config = $env->get($id);
      return $this->getParameterConfigChildFactory()->make($parent_config, $output_method);
    }

    if (isset($parts[1])) {
      $type = $parts[1];

      if (strpos($type, '|') !== false) {
        list($type, $default_args) = explode('|', $type, 2);
        $default_args = json_decode($default_args) ?: $default_args;
      }

      if (strpos($type, '.')) {
        list($type, $output_method) = explode('.', $type, 2);
      }
    }

    return $this->getParameterConfigFactory()->make($id, [
      'type' => $type,
      'is_optional' => $is_optional,
      'default_arguments' => $default_args,
      'output_method' => $output_method,
    ]);
  }

  public function parseParameterArray(array $param_strings)
  {
    $collection = $this->getParameterConfigCollectionFactory()->make();

    foreach ($param_strings as $subject) {
      $params = $this->extractParameterDefinitions($subject);

      foreach ($params as $param_string) {
        $param = $this->parseParameterString($param_string);
        $collection->add($param);
      }
    }

    return $collection;
  }

  public function arrayInjectParameters(array $subject, array $arguments, ParameterConfigCollectionInterface $env = null)
  {
    $output = [];
    $appended = [];
    $objects = [];
    $dirty = [];
    $injections = $this->getParameterInjectionFactory()->make($subject);

    $keys = array_keys($subject);
    $i = 0;

    while (isset($keys[$i])) {
      $key = $keys[$i];
      $string = $subject[$key];
      $injection = is_array($string) ?
        $this->arrayInjectParameters($string, $arguments, $env) :
        $this->injectParameters($string, $arguments, $env);

      $output[$key] = $injection->getOutput();
      $objects = array_merge($objects, $injection->getObjects());
      $arguments = array_merge($arguments, $objects);

      // there are still some unparsed definitions in the injection
      if (!$injection->isClean() && !isset($dirty[$key])) {
        $dirty[$key] = $string;
        $keys[] = $key;
        $i++;
        continue;
      }

      // string was rendered successfully
      unset($dirty[$key]);
      $i++;
    }

    $injections->setIsClean(!count($dirty));
    $injections->setObjects($objects);
    $injections->setOutput($output);
    return $injections;
  }

  // inserts normalized parameters into subject
  public function injectParameters($subject, array $arguments = [], ParameterConfigCollectionInterface $env = null)
  {
    $params = $this->extractParameterDefinitions($subject);
    $factory_collection = $this->getParameterFactoryCollection();
    $injection = $this->getParameterInjectionFactory()->make($subject);
    $accessor = $this->getObjectAccessor();
    $result = $subject;
    $objects = [];
    $injected = 0;
    $appended = [];
    $params_count = count($params);
    $i = 0;

    while (isset($params[$i])) {
      $param_string = $params[$i];

      try {
        $param = $this->parseParameterString($param_string, $env);
      } catch(InvalidParameterConfigException $e) {
        // skip parameter definition if we can't parse it yet
        // append to end of param array
        if (!in_array($param_string, $appended)) {
          $appended[] = $param_string;
          $params[] = $param_string;
        }

        $i++;
        continue;
      }

      $args = $this->requireParameterArgument($param, $arguments);
      
      // if the argument is not included, and is not required
      // we'll replace the parameter definition with an empty string  
      if (!$args) {
        $result = $this->injectParameterString($param_string, '', $result);
        $injected++;
        $i++;
        continue;
      }

      $is_child = $param instanceof ParameterConfigChildInterface;

      $param_id = $is_child ? $param->getParent()->getId() : $param->getId();

      // if the argument is an instance of the invoked parameter use that instead
      if (is_object($args) && is_a($args, $param->getType())) {
        $instance = $arguments[$param_id];
      }

      // if the instance wasn't provided in arguments, but was extracted in previous injection
      // use that instance
      if (!isset($instance) && isset($objects[$param_id])) {
        $instance = $objects[$param_id];
      }

      // if the instance was not invoked previously, invoke it
      if (!isset($instance)) {
        $instance = $factory_collection->get($param)->invokeParameter($param, $args);
        $objects = array_merge($objects, [$param->getId() => $instance]);
      }

      // render the instance output
      $output = $accessor->get(
        $instance,
        $param->getOutputMethod()
      );

      // add parameter to environment
      if ($env && !$is_child) {
        $not_frozen = !($env instanceof FreezableInterface) || !$env->isFrozen();
        if ($not_frozen) {
          $env->add($param);
        }
      }

      // if the parameter string is the same length
      // as the subject, return literal output, instead of
      // converting to string
      if (strlen($this->wrapParameterString($param_string)) === strlen($subject)) {
        $injection->setOutput($output);
        $injection->setObjects($objects);
        $injection->setIsClean(true);
        return $injection;
      }

      $result = $this->injectParameterString($param_string, (string) $output, $result);
      $injected++;
      $i++;
    }

    $injection->setObjects($objects);
    $injection->setOutput($result);
    $injection->setIsClean($injected === $params_count);
    return $injection;
  }

  public function stringContainsParameterDefinition($string, array $params)
  {
    foreach ($params as $param_id) {
      $contains_ref = strpos($string, '{'.$param_id.':') !== false;
      $contains_child_ref = strpos($string, '{'.$param_id.'.') !== false;

      if ($contains_ref || $contains_child_ref) {
        return true;
      }
    }
  }

  public function splitParameterString($param_strings)
  {
    return explode(':', $param_strings);
  }

  public function wrapParameterString($param_string)
  {
    return '{'.$param_string.'}';
  }

  public function getParameterConfigFactory()
  {
    return $this->parameter_config_factory;
  }

  public function setParameterConfigFactory(ParameterConfigFactoryInterface $factory)
  {
    $this->parameter_config_factory = $factory;
    return $this;
  }

  public function getParameterConfigCollectionFactory()
  {
    return $this->parameter_config_collection_factory;
  }

  public function setParameterConfigCollectionFactory(ParameterConfigCollectionFactoryInterface $params)
  {
    $this->parameter_config_collection_factory = $params;
    return $this;
  }

  public function getParameterFactoryCollection()
  {
    return $this->parameter_factory_collection;
  }

  public function setParameterFactoryCollection(ParameterFactoryCollectionInterface $factory)
  {
    $this->parameter_factory_collection = $factory;
    return $this;
  }

  public function getParameterInjectionFactory()
  {
    return $this->parameter_injection_factory;
  }

  public function setParameterInjectionFactory(ParameterInjectionFactoryInterface $factory)
  {
    $this->parameter_injection_factory = $factory;
    return $this;
  }

  public function setObjectAccessor(ObjectAccessorInterface $accessor)
  {
    $this->accessor = $accessor;
    return $this; 
  }

  public function getObjectAccessor()
  {
    return $this->accessor;
  }

  public function setParameterConfigChildFactory(ParameterConfigChildFactoryInterface $factory)
  {
    $this->param_config_child_factory = $factory;
    return $this; 
  }

  public function getParameterConfigChildFactory()
  {
    return $this->param_config_child_factory;
  }

  public function injectParameterString($param_string, $output_string, $subject)
  {
    $wrapped_parameter_string = $this->wrapParameterString($param_string);
    return str_replace($wrapped_parameter_string, $output_string, $subject);
  }

  protected function requireParameterArgument(ParameterConfigInterface $param, array $arguments)
  {
    $param_id = $param instanceof ParameterConfigChildInterface ? $param->getParent()->getId() : $param->getId();

    if (!isset($arguments[$param_id]) && !$param->getDefaultArguments() && !$param->isOptional()) {
      throw $param->makeMissingParameterException();
    }

    return isset($arguments[$param_id]) ? $arguments[$param_id] : $param->getDefaultArguments();
  }
}
