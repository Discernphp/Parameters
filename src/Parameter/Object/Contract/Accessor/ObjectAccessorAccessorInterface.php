<?php namespace Discern\Parameter\Object\Contract\Accessor;

use Discern\Parameter\Object\Contract\ObjectAccessorInterface;

interface ObjectAccessorAccessorInterface {
  public function getObjectAccessor();

  public function setObjectAccessor(ObjectAccessorInterface $accessor);
}
