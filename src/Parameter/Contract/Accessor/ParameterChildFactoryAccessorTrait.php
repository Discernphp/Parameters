<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterChildFactoryInterface;

trait ParameterChildFactoryAccessorTrait {
  public function getParameterChildFactory()
  {
    if (!isset($this->parameter_child_factory)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\ParameterChildFactoryInterface', 
        $class,
        __METHOD__,
        'setParameterChildFactory' 
      );
    }

    return $this->parameter_child_factory;
  }

  public function setParameterChildFactory(ParameterChildFactoryInterface $factory)
  {
  	$this->parameter_child_factory = $factory;
  	return $this;
  }
}
