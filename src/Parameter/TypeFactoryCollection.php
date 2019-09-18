<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\TypeFactoryCollectionInterface;
use Discern\Parameter\Contract\TypeFactoryInterface;
use Discern\Parameter\Contract\ParameterInterface;

class TypeFactoryCollection implements TypeFactoryCollectionInterface {
  protected $factory_array = [];

  public static $DEFAULT_FACTORY_ID = '__default';

  public function add($id, TypeFactoryInterface $factory)
  {
    $this->factory_array[$id] = $factory;
    return $this;
  }

  public function get($id)
  {
    if ($id instanceof ParameterInterface) {
      $param_id = $id->getId();
      $id = isset($this->factory_array[$param_id]) ? $param_id : $id->getType();
    }

    $default = static::$DEFAULT_FACTORY_ID;
    $id =  isset($this->factory_array[$id]) ? $id : $default;
    return $this->factory_array[$id];
  }
}