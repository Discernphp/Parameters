<?php namespace Discern\Parameter\Object\Contract;

interface ObjectAccessorInterface {
  public function get($instance, $property);

  public function set($instance, $property, $value);
}
