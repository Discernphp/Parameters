<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterCollectionFactoryInterface;

class ParameterCollectionFactory implements ParameterCollectionFactoryInterface {
  public function make(array $properties = [])
  {
    return new ParameterCollection($properties);
  }
}
