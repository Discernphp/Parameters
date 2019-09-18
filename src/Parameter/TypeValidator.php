<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\TypeValidatorInterface;

class TypeValidator implements TypeValidatorInterface {
  public function exists($type)
  {
  	return class_exists($type);
  }
}