<?php namespace Discern\Test\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorInterface;

class UserHasPetState implements StateValidatorInterface {
  protected $errors = [];

  public function getId()
  {
    return 'has pet';
  }

  public function isValid($user)
  {
    if (!isset($user->getPet()->id)) {
      $this->errors[] = sprintf('user id: `%s`, has no pet', $user->getId());
      return false;
    }

    return true;
  }

  public function getErrors()
  {
    return $this->errors;
  }
}