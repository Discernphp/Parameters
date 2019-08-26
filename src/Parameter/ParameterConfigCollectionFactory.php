<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigCollectionFactoryInterface;

class ParameterConfigCollectionFactory implements ParameterConfigCollectionFactoryInterface {
  public function make(array $properties = [])
  {
    return new ParameterConfigCollection($properties);
  }
}
