<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\InjectionFactoryInterface;

interface InjectionFactoryAccessorInterface {
  public function getParameterInjectionFactory();

  public function setParameterInjectionFactory(InjectionFactoryInterface $factory);
}
