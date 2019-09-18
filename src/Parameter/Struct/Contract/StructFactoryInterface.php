<?php namespace Discern\Parameter\Struct\Contract;

use Discern\Parameter\Contract\ParameterCollectionInterface;
use Discern\Parameter\Contract\TypeFactoryCollectionInterface;

interface StructFactoryInterface {
  public function make(ParameterCollectionInterface $param_configs, TypeFactoryCollectionInterface $factory);
}