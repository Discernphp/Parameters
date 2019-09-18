<?php namespace Discern\Parameter\Struct;

trait StructTrait {
  public function __set($id, $value)
  {
    return $this->setProperty($id, $value);
  }

  public function __get($id)
  {
    return $this->getProperty($id);
  }

  public function __call($id, array $arguments)
  {
    return $this->setProperty($id, $arguments);
  }
}
