<?php namespace Discern\Parameter\State;

use Discern\Parameter\State\Contract\StateValidationProviderInterface;
use Discern\Parameter\State\Contract\StateValidatorCollectionInterface;

class StateValidationProvider implements StateValidationProviderInterface {
  public function validateState($instance_id, $instance, array $states)
  {
    $valid = true;
    $errors = [];
    for ($i = 0; isset($states[$i]); $i++) {
      $state = $states[$i];
      if (is_array($state)) {
        try {
          return $this->validateState($instance_id, $instance, $state);
        } catch (StateValidationException $e) {
          $valid = false;
          $errors = array_merge(
            $errors,
            $e->getErrors()
          );
        }
        continue;
      }

      // convert instance_id to positive if negation flag found
      $validator = $this->getStateValidatorCollection()->get($instance_id, $state->getId());
      $valid = $state->apply(
        $validator->isValid($instance)
      );

      if (!$valid) {
        $errors = array_merge(
          $errors,
          $validator->getErrors()
        );
      }
    }
    if ($valid) return $valid;

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
