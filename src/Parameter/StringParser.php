<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\Accessor\TypeFactoryCollectionAccessorInterface;
use Discern\Parameter\Contract\Accessor\TypeFactoryCollectionAccessorTrait;
use Discern\Parameter\Contract\Accessor\ParameterCollectionFactoryAccessorInterface;
use Discern\Parameter\Contract\Accessor\ParameterCollectionFactoryAccessorTrait;
use Discern\Parameter\Contract\ParameterCollectionInterface;
use Discern\Parameter\Contract\ParameterInterface;
use Discern\Parameter\Contract\StringParserInterface;
use Discern\Parameter\Contract\Accessor\InjectionFactoryAccessorInterface;
use Discern\Parameter\Contract\Accessor\InjectionFactoryAccessorTrait;
use Discern\Parameter\Object\Contract\Accessor\ObjectAccessorAccessorInterface;
use Discern\Parameter\Object\Contract\Accessor\ObjectAccessorAccessorTrait;
use Discern\Parameter\Contract\Accessor\ParameterFactoryAccessorInterface;
use Discern\Parameter\Contract\Accessor\ParameterFactoryAccessorTrait;
use Discern\Parameter\Contract\Accessor\ParameterChildFactoryAccessorInterface;
use Discern\Parameter\Contract\Accessor\ParameterChildFactoryAccessorTrait;
use Discern\Parameter\Contract\ParameterChildInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;

class StringParser implements 
  StringParserInterface,
  TypeFactoryCollectionAccessorInterface,
  ParameterCollectionFactoryAccessorInterface,
  InjectionFactoryAccessorInterface,
  ObjectAccessorAccessorInterface,
  ParameterFactoryAccessorInterface {
  use 
    ParameterCollectionFactoryAccessorTrait,
    TypeFactoryCollectionAccessorTrait,
    InjectionFactoryAccessorTrait,
    ParameterChildFactoryAccessorTrait,
    ObjectAccessorAccessorTrait,
    ParameterFactoryAccessorTrait;

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

    $matches=[];
    preg_match_all('/{(.*?)}/', $string, $matches);

    // don't allow spaces between parameter definitions
    array_map(function ($match) {
      if (strpos($match, '{ ') !== false || strpos($match, ' }') !== false) {
        throw new \InvalidArgumentException(
          sprintf(
            "%s::extractParameterDefinitions() parse error,
            space between brackets in parameter definition is not allowed: %s",
            get_class($this),
            $match
          )
        );
      }
    }, $matches[0]);

    return $matches[1];
  }

  // converts parameter string into object
  public function parseParameterString($string, ParameterCollectionInterface $env = null)
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
        throw new InvalidParameterException("Parameter `{$id}` not found");
      }
      $parent_config = $env->get($id);
      return $this->getParameterChildFactory()->make($parent_config, $output_method);
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

    return $this->getParameterFactory()->make($id, [
      'type' => $type,
      'is_optional' => $is_optional,
      'default_arguments' => $default_args,
      'output_method' => $output_method,
    ]);
  }

  public function parseParameterArray(array $param_strings)
  {
    $collection = $this->getParameterCollectionFactory()->make();

    foreach ($param_strings as $subject) {
      $params = $this->extractParameterDefinitions($subject);

      foreach ($params as $param_string) {
        $param = $this->parseParameterString($param_string);
        $collection->add($param);
      }
    }

    return $collection;
  }

  public function arrayInjectParameters(array $subject, array $arguments, ParameterCollectionInterface $env = null)
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
  public function injectParameters($subject, array $arguments = [], ParameterCollectionInterface $env = null)
  {
    $params = $this->extractParameterDefinitions($subject);
    $factory_collection = $this->getParameterTypeFactoryCollection();
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
      } catch(InvalidParameterException $e) {
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

      $is_child = $param instanceof ParameterChildInterface;

      $param_id = $is_child ? $param->getParent()->getId() : $param->getId();

      // if the argument is an instance of the invoked parameter use that instead
      // TODO: remove this manual check and iject service that does the comparison
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

  public function injectParameterString($param_string, $output_string, $subject)
  {
    $wrapped_parameter_string = $this->wrapParameterString($param_string);
    return str_replace($wrapped_parameter_string, $output_string, $subject);
  }

  protected function requireParameterArgument(ParameterInterface $param, array $arguments)
  {
    $param_id = $param instanceof ParameterChildInterface ? $param->getParent()->getId() : $param->getId();

    if (!isset($arguments[$param_id]) && !$param->getDefaultArguments() && !$param->isOptional()) {
      throw $param->makeMissingParameterException();
    }

    return isset($arguments[$param_id]) ? $arguments[$param_id] : $param->getDefaultArguments();
  }
}
