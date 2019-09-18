<?php namespace Discern\Parameter\Struct;

use Discern\Parameter\Contract\Accessor\TypeFactoryCollectionAccessorInterface;
use Discern\Parameter\Contract\Accessor\TypeFactoryCollectionAccessorTrait;
use Discern\Parameter\Contract\Accessor\ParameterCollectionAccessorInterface;
use Discern\Parameter\Contract\Accessor\ParameterCollectionAccessorTrait;
use Discern\Parameter\Contract\ParameterCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Struct\Contract\StructInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;
use Discern\Parameter\ParameterException;

class Struct implements 
  StructInterface,
  TypeFactoryCollectionAccessorInterface,
  FreezableInterface,
  ParameterCollectionAccessorInterface {
  use 
    StructTrait,
    FreezableTrait,
    TypeFactoryCollectionAccessorTrait,
    ParameterCollectionAccessorTrait;

  protected $properties;

  protected $param_factory;

  protected $param_configs;

  protected $parameter_collection;

  protected $parameter_type_factory_collection;

  public function getProperty($id)
  {
    $param = $this->getParameterCollection()->get($id);

    return $this->properties[$param->getId()];
  }

  public function setProperty($id, $args)
  {
    $this->preventActionWhenFrozen(
      sprintf(
        'Cannot set value of `Struct::%s` when struct is `frozen`.
        Avoid altering Struct properties directly. 
        Instead use `Struct::with([\'%s\'=>[...arguments]])`',
        $id,
        $id
      )
    );

    $param = $this->getParameterCollection()->get($id);

    if (is_object($args) && is_a($args, $param->getType())) {
      $this->properties[$id] = $args;
      return $this;
    }

    if (!is_array($args)) {
      $exception = new ParameterException(
        sprintf(
          "Invalid agrument type: `%s`, value: `%s` given for Parameter `%s`, expected type: `%s` or Array",
          gettype($args),
          is_object($args) ? get_class($args) : json_encode($args),
          $id,
          $param->getType()
        )
      );

      throw $exception->setParameter($param);
    }

    $this->properties[$id] = $this->getParameterTypeFactoryCollection()
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

  public function with(array $properties)
  {
    $struct = clone($this);
    $struct
      ->unfreeze()
      ->setProperties($properties);

    return $this->isFrozen() ? $struct->freeze() : $struct;
  }
}