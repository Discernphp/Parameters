<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\TypeFactoryCollectionInterface;

trait TypeFactoryCollectionAccessorTrait {
  public function getParameterTypeFactoryCollection()
  {
    if (!isset($this->parameter_type_factory_collection)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\TypeFactoryCollectionInterface', 
        $class,
        __METHOD__,
        'setParameterTypeFactoryCollection' 
      );
    }
    
  	return $this->parameter_type_factory_collection;
  }

  public function setParameterTypeFactoryCollection(TypeFactoryCollectionInterface $collection)
  {
  	$this->parameter_type_factory_collection = $collection;
  	return $this;
  }
}
