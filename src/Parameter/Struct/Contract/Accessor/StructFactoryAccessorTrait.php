<?php namespace Discern\Parameter\Struct\Contract\Accessor;

use Discern\Parameter\Struct\Contract\StructFactoryInterface;

trait StructFactoryAccessorTrait {

  public function getParameterStructFactory()
  {
  	return $this->parameter_struct;
  }

  public function setParameterStructFactory(StructFactoryInterface $factory)
  {
  	$this->parameter_struct = $factory;
  	return $this;
  }
}
