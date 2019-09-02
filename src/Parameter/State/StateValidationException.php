<?php namespace Discern\Parameter\State;

class StateValidationException extends \Exception {
  protected $errors = [];

  public function setErrors(array $errors)
  {
    $this->errors = $errors;
    return $this;
  }

  public function getErrors()
  {
    return $this->errors;
  }
}
