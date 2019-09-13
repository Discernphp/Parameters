<?php namespace Discern\Test\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorInterface;

class UserHasPetState implements StateValidatorInterface {
  public function getId()
  {
    return 'has pet';
  }

  public function isValid($user)
  {
    $this->state = !!isset($user->getPet()->id);
    return $this->state;
  }

  public function getStateDescription()
  {
    return [
      sprintf(
        'user %s a pet',
        $this->state ? 'does not have' : 'has'
      )
    ];
  }
}