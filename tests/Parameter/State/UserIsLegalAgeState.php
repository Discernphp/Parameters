<?php namespace Discern\Test\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorInterface;

class UserIsLegalAgeState implements StateValidatorInterface {
  protected $errors = [];

  public function getId()
  {
    return 'is legal';
  }

  public function isValid($user)
  {
    if ($user->getAge() < 18) {
      $this->errors[] = sprintf('user id: `%s` is under the age 18', $user->getId());
      return false;
    }

    return true;
  }

  public function getErrors()
  {
    return $this->errors;
  }
}