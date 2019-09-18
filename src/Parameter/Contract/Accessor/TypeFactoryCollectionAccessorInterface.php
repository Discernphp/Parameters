<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\TypeFactoryCollectionInterface;

interface TypeFactoryCollectionAccessorInterface {
  public function getParameterTypeFactoryCollection();

  public function setParameterTypeFactoryCollection(TypeFactoryCollectionInterface $collection);
}
