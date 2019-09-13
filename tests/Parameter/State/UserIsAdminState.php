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
    $this->state = !!$user->isAdmin();
    return $this->state;
  }

  public function getStateDescription()
  {
    return [
      sprintf(
        'user %s an admin',
        $this->state ? 'is' : 'is not'
      )
    ];
  }
}