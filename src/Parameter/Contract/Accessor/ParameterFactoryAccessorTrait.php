<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterFactoryInterface;

trait ParameterFactoryAccessorTrait {
  public function getParameterFactory()
  {
    if (!isset($this->parameter_factory)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\ParameterFactoryInterface', 
        $class,
        __METHOD__,
        'setParameterFactory' 
      );
    }
      	
  	return $this->parameter_factory;
  }

  public function setParameterFactory(ParameterFactoryInterface $factory)
  {
  	$this->parameter_factory = $factory;
  	return $this;
  }
}
