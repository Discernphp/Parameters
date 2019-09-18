<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\TypeValidatorInterface;
use Discern\Parameter\UninitializedDependencyException;

trait TypeValidatorAccessorTrait {
  public function getParameterTypeValidator()
  {
    if (!isset($this->parameter_type_validator)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\TypeValidatorInterface', 
        $class,
        __METHOD__,
        'setParameterTypeValidator' 
      );
    }

    return $this->parameter_type_validator;
  }

  public function setParameterTypeValidator(TypeValidatorInterface $validator)
  {
    $this->parameter_type_validator = $validator;
    return $this;
  }
}
