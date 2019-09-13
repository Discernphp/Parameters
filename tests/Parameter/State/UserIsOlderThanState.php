<?php namespace Discern\Test\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorInterface;

class UserIsOlderThanState implements StateValidatorInterface {
  protected $errors = [];

  public function getId()
  {
    return 'is older than %p';
  }

  public function isValid($user, $age)
  {
    $this->age = (int) $age;
    $this->state = ($user->getAge() > $this->age);
    return $this->state;
  }

  public function getStateDescription()
  {
    return [
      sprintf(
        'user is %s the age of %d',
        $this->state ? 'older than' : 'not older than',
        $this->age
      )
    ];
  }
}