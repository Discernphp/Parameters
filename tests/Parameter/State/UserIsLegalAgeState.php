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
    $this->state = ($user->getAge() >= 18);
    return $this->state;
  }

  public function getStateDescription()
  {
    return [
      sprintf(
        'user is %s the age of 18',
        $this->state ? 'under' : 'atleast'
      )
    ];
  }
}