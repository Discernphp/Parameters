<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterCollectionInterface;

trait ParameterCollectionAccessorTrait {
  public function getParameterCollection()
  {
    if (!isset($this->parameter_collection)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\ParameterCollectionInterface', 
        $class,
        __METHOD__,
        'setParameterCollection' 
      );
    }

    return $this->parameter_collection;
  }

  public function setParameterCollection(ParameterCollectionInterface $params)
  {
  	$this->parameter_collection = $params;
  	return $this;
  }
}
