<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\InjectionFactoryInterface;

trait InjectionFactoryAccessorTrait {
  public function getParameterInjectionFactory()
  {
    if (!isset($this->parameter_injection_factory)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\InjectionFactoryInterface', 
        $class,
        __METHOD__,
        'setParameterInjectionFactory' 
      );
    }

    return $this->parameter_injection_factory;
  }

  public function setParameterInjectionFactory(InjectionFactoryInterface $factory)
  {
  	$this->parameter_injection_factory = $factory;
  	return $this;
  }
}
