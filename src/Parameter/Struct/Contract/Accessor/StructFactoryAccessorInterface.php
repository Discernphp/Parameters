<?php namespace Discern\Parameter\Struct\Contract\Accessor;

use Discern\Parameter\Struct\Contract\StructFactoryInterface;

interface StructFactoryAccessorInterface {

  public function getParameterStructFactory();

  public function setParameterStructFactory(StructFactoryInterface $factory);
}
