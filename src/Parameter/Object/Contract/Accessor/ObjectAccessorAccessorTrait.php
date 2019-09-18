<?php namespace Discern\Parameter\Object\Contract\Accessor;

use Discern\Parameter\Object\Contract\ObjectAccessorInterface;

trait ObjectAccessorAccessorTrait {
  public function getObjectAccessor()
  {
  	return $this->object_accessor;
  }

  public function setObjectAccessor(ObjectAccessorInterface $accessor)
  {
  	$this->object_accessor = $accessor;
  	return $this;
  }
}
