<?php namespace Discern\Parameter\Struct;

use Discern\Parameter\Contract\ParameterCollectionInterface;
use Discern\Parameter\Contract\TypeFactoryCollectionInterface;
use Discern\Parameter\Struct\Contract\StructFactoryInterface;

class StructFactory implements StructFactoryInterface {
  public function make(ParameterCollectionInterface $params, TypeFactoryCollectionInterface $factory)
  {
  	$struct = new Struct();
  	$struct->setParameterCollection($params);
  	$struct->setParameterTypeFactoryCollection($factory);
  	return $struct;
  }
}