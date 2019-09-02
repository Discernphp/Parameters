<?php namespace Discern\Parameter\State;

use Discern\Parameter\State\Contract\StateValidationProviderInterface;
use Discern\Parameter\State\Contract\StateValidatorCollectionInterface;

class StateValidationProvider implements StateValidationProviderInterface {
  public function validateState($instance_id, $instance, array $states)
  {
    for ($i = 0; isset($states[$i]); $i++) {
      $valid = true;
      $errors = [];
      for ($j = 0; isset($states[$i][$j]); $j++) {
        $state_id = $states[$i][$j];
        $validator = $this->getStateValidatorCollection()->get($instance_id, $state_id);
        $valid = $validator->isValid($instance);

        if (!$valid) {
          $errors = array_merge(
            $errors,
            $validator->getErrors()
          );
          break;
        }
      }
      if ($valid) return $valid;
    }

    $exception = new StateValidationException(
      sprintf(
        'state of type `%s` using instance `%s` is invalid,
         see `%s:getErrors()` for more information',
        $instance_id,
        is_object($instance) ? get_class($instance) : $instance,
        StateValidationException::class
      )
    );

    $exception->setErrors($errors);
    throw $exception;
  }

  public function getStateValidatorCollection()
  {
    return $this->state_validators;
  }

  public function setStateValidatorCollection(StateValidatorCollectionInterface $validators)
  {
    $this->state_validators = $validators;
    return $this;
  }
}
