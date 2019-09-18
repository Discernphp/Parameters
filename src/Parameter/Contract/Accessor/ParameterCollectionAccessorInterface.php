<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\ParameterCollectionInterface;

interface ParameterCollectionAccessorInterface {
  public function getParameterCollection();

  public function setParameterCollection(ParameterCollectionInterface $params);
}
