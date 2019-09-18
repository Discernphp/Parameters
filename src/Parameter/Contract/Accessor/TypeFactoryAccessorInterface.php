<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\TypeFactoryInterface;

interface TypeFactoryAccessorInterface {
  public function getParameterTypeFactory();

  public function setParameterTypeFactory(TypeFactoryInterface $factory);
}
