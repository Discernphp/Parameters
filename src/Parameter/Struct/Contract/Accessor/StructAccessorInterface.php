<?php namespace Discern\Parameter\Struct\Contract\Accessor;

use Discern\Parameter\Struct\Contract\StructInterface;

interface StructAccessorInterface {

  public function getParameterStruct();

  public function setParameterStruct(StructInterface $struct);
}
