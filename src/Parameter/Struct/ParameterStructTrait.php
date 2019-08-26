<?php namespace Discern\Parameter\Struct;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Struct\Contract\ParameterStructInterface;

trait ParameterStructTrait {
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
