<?php namespace Discern\Parameter\Struct;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Struct\Contract\ParameterStructFactoryInterface;

class ParameterStructFactory implements ParameterStructFactoryInterface {
  public function make(ParameterConfigCollectionInterface $param_configs, ParameterFactoryCollectionInterface $factory)
  {
  	$struct = new ParameterStruct();
  	$struct->setParameterConfigCollection($param_configs);
  	$struct->setParameterFactoryCollection($factory);
  	return $struct;
  }
}