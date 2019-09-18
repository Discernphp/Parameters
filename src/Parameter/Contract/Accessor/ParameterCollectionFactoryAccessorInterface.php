<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterCollectionFactoryInterface;

interface ParameterCollectionFactoryAccessorInterface {
  public function getParameterCollectionFactory();

  public function setParameterCollectionFactory(ParameterCollectionFactoryInterface $factory);
}
