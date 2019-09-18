<?php namespace Discern\Parameter\Struct\Contract;

interface StructInterface {
  public function with(array $properties);
  
  public function getProperty($id);

  public function setProperty($id, $args);

  public function setProperties(array $properties);
}