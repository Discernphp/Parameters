<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterFactoryInterface;

interface ParameterFactoryAccessorInterface {
  public function getParameterFactory();

  public function setParameterFactory(ParameterFactoryInterface $factory);
}
