<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterFactoryInterface;
use Discern\Parameter\Contract\Accessor\TypeValidatorAccessorInterface;
use Discern\Parameter\Contract\Accessor\TypeValidatorAccessorTrait;

class ParameterFactory implements ParameterFactoryInterface, TypeValidatorAccessorInterface {
  use TypeValidatorAccessorTrait;

  public function make($id, array $properties, TypeValidatorInterface $validator = null)
  {
  	return new Parameter(
  	  $id,
  	  $properties,
  	  $validator ?: $this->getParameterTypeValidator()
  	);
  }
}
