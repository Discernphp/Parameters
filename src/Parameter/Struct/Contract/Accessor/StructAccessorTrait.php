<?php namespace Discern\Parameter\Struct\Contract\Accessor;

use Discern\Parameter\Struct\Contract\StructInterface;

trait StructAccessorTrait {

  public function getParameterStruct()
  {
  	return $this->parameter_struct;
  }

  public function setParameterStruct(StructInterface $struct)
  {
  	$this->parameter_struct = $struct;
  	return $this;
  }
}
