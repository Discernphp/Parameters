<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\TypeFactoryInterface;

trait TypeFactoryAccessorTrait {
  public function getParameterTypeFactory()
  {
    if (!isset($this->parameter_type_factory)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\TypeFactoryInterface', 
        $class,
        __METHOD__,
        'setParameterTypeFactory' 
      );
    }

  	return $this->parameter_type_factory;
  }

  public function setParameterTypeFactory(TypeFactoryInterface $factory)
  {
  	$this->parameter_type_factory = $factory;
  	return $this;
  }
}
