<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\TypeValidatorInterface;

interface TypeValidatorAccessorInterface {
  public function getParameterTypeValidator();

  public function setParameterTypeValidator(TypeValidatorInterface $validator);
}
