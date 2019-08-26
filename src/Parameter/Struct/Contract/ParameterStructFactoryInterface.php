<?php namespace Discern\Parameter\Struct\Contract;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;

interface ParameterStructFactoryInterface {
  public function make(ParameterConfigCollectionInterface $param_configs, ParameterFactoryCollectionInterface $factory);
}