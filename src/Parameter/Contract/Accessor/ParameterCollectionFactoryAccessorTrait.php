<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterCollectionFactoryInterface;

trait ParameterCollectionFactoryAccessorTrait {
  public function getParameterCollectionFactory()
  {
    if (!isset($this->parameter_collection_factory)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\ParameterCollectionFactoryInterface', 
        $class,
        __METHOD__,
        'setParameterCollectionFactory' 
      );
    }

    return $this->parameter_collection_factory;
  }

  public function setParameterCollectionFactory(ParameterCollectionFactoryInterface $factory)
  {
    $this->parameter_collection_factory = $factory;
    return $this;
  }
}