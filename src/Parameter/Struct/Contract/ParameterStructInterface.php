<?php namespace Discern\Parameter\Struct\Contract;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;

interface ParameterStructInterface {
  public function with(array $properties);
  
  public function getProperty($id);

  public function setProperty($id, $args);

  public function setProperties(array $properties);

  public function setParameterFactoryCollection(ParameterFactoryCollectionInterface $factory);

  public function getParameterFactoryCollection();

  public function setParameterConfigCollection(ParameterConfigCollectionInterface $param_configs);

  public function getParameterConfigCollection();
}