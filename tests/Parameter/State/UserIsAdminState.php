<?php namespace Discern\Test\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorInterface;

class UserIsAdminState implements StateValidatorInterface {
  protected $errors = [];

  public function getId()
  {
    return 'is admin';
  }

  public function isValid($user)
  {
    if (!$user->isAdmin()) {
      $this->errors[] = sprintf('user id `%s` is not an administrator', $user->getId());
      return false;
    }

    return true;
  }

  public function getErrors()
  {
    return $this->errors;
  }
}