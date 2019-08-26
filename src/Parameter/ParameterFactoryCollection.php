<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigInterface;

class ParameterFactoryCollection implements ParameterFactoryCollectionInterface {
  protected $factory_array = [];

  public static $DEFAULT_FACTORY_ID = '__default';

  public function add($id, ParameterFactoryInterface $factory)
  {
    $this->factory_array[$id] = $factory;
    return $this;
  }

  public function get($id)
  {
    if ($id instanceof ParameterConfigInterface) {
      $param_id = $id->getId();
      $id = isset($this->factory_array[$param_id]) ? $param_id : $id->getType();
    }

    $default = static::$DEFAULT_FACTORY_ID;
    $id =  isset($this->factory_array[$id]) ? $id : $default;
    return $this->factory_array[$id];
  }
}