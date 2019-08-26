<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigFactoryInterface;

class ParameterConfigFactory implements ParameterConfigFactoryInterface {
  public function make($id, array $properties)
  {
  	return new ParameterConfig($id, $properties);
  }
}
