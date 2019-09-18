<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterChildFactoryInterface;

interface ParameterChildFactoryAccessorInterface {
  public function getParameterChildFactory();

  public function setParameterChildFactory(ParameterChildFactoryInterface $factory);
}
