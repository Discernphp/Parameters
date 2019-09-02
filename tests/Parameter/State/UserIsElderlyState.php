<?php namespace Discern\Test\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorInterface;

class UserIsElderlyState implements StateValidatorInterface {
  protected $errors = [];

  public function getId()
  {
    return 'is elderly';
  }

  public function isValid($user)
  {
    if ($user->getAge() < 59) {
      $this->errors[] = sprintf('user id: `%s` is under the age of 59', $user->getId());
      return false;
    }

    return true;
  }

  public function getErrors()
  {
    return $this->errors;
  }
}