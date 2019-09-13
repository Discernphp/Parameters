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
    $this->state = ($user->getAge() >= 59);
    return $this->state;
  }

  public function getStateDescription()
  {
    return [
      sprintf(
        'user is %s age 59',
        $this->state ? 'under' : 'atleast'
      )
    ];
  }
}