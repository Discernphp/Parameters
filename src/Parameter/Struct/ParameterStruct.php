<?php namespace Discern\Parameter\Struct;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Struct\Contract\ParameterStructInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;
use Discern\Parameter\ParameterConfigException;

class ParameterStruct implements ParameterStructInterface, FreezableInterface {
  use ParameterStructTrait;
  use FreezableTrait;

  protected $properties;

  protected $param_factory;

  protected $param_configs;

  public function getProperty($id)
  {
    $param = $this->getParameterConfigCollection()->get($id);

    return $this->properties[$param->getId()];
  }

  public function setProperty($id, $args)
  {
    $this->preventActionWhenFrozen(
      sprintf(
        'Cannot set value of `ParameterStruct::%s` when struct is `frozen`.
        Avoid altering ParameterStruct properties directly. 
        Instead use `ParameterStruct::with([\'%s\'=>[...arguments]])`',
        $id,
        $id
      )
    );

    $param = $this->getParameterConfigCollection()->get($id);

    if (is_object($args) && is_a($args, $param->getType())) {
      $this->properties[$id] = $args;
      return $this;
    }

    if (!is_array($args)) {
      $exception = new ParameterConfigException(
        sprintf(
          "Invalid agrument type: `%s`, value: `%s` given for Parameter `%s`, expected type: `%s` or Array",
          gettype($args),
          is_object($args) ? get_class($args) : json_encode($args),
          $id,
          $param->getType()
        )
      );

      throw $exception->setParameterConfig($param);
    }

    $this->properties[$id] = $this->getParameterFactoryCollection()
      ->get($param)
      ->invokeParameter($param, $args);

    return $this;
  }

  public function setProperties(array $properties)
  {
    foreach ($properties as $id => $args) {
      $this->setProperty($id, $args);
    }
    return $this;
  }

  public function getParameterFactoryCollection()
  {
    return $this->param_factory;
  }

  public function setParameterFactoryCollection(ParameterFactoryCollectionInterface $factory)
  {
    $this->param_factory = $factory;
    return $this;
  }

  public function setParameterConfigCollection(ParameterConfigCollectionInterface $param_configs)
  {
    $this->param_configs = $param_configs;
    return $this;
  }

  public function getParameterConfigCollection()
  {
    return $this->param_configs;
  }

  public function with(array $properties)
  {
    $struct = clone($this);
    $struct
      ->unfreeze()
      ->setProperties($properties);

    return $this->isFrozen() ? $struct->freeze() : $struct;
  }
}