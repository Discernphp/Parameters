<?php namespace Discern\Test\Parameter\State;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\State\StateStringParser;
use Discern\Parameter\State\StateValidatorCollection;
use Discern\Parameter\State\StateValidationProvider;
use Discern\Parameter\State\StateValidationException;
use Discern\Test\Parameter\State\UserHasPetState;
use Discern\Test\Parameter\State\UserIsElderlyState;
use Discern\Test\Parameter\State\UserIsLegalAgeState;
use Discern\Test\Parameter\State\UserIsAdminState;
use Discern\Test\Parameter\State\UserIsOlderThanState;
use Discern\Test\Parameter\User;

final class StateValidationTest extends TestCase {
  public function __construct()
  {
    $states = new StateValidatorCollection();
    $states->add(
      User::class,
      new UserHasPetState()
    );

    $states->add(
      User::class,
      new UserIsElderlyState()
    );

    $states->add(
      User::class,
      new UserIsLegalAgeState()
    );

    $states->add(
      User::class,
      new UserIsAdminState()
    );

    $states->add(
      User::class,
      new UserIsOlderThanState()
    );

    $this->parser = new StateStringParser();
    $this->provider = new StateValidationProvider();
    $this->provider->setStateValidatorCollection($states);
  }

  public function testCallsStateWithCorrectParameters()
  {
    $states = $this->parser->parseStateString(
      'is older than :age'
    );

    $user = new User(1, [
      'age' => 59
    ]);

    $result = $this->provider->validateState(
      User::class,
      $user,
      $states,
      ['age' => 58]
    );

    $this->assertEquals($result, true);
  }

  public function testCanNegateStateWithCorrectParameters()
  {
    $states = $this->parser->parseStateString(
      'is not older than :age'
    );

    $user = new User(1, [
      'age' => 59
    ]);

    $this->expectException(
      StateValidationException::class
    );

    $result = $this->provider->validateState(
      User::class,
      $user,
      $states,
      ['age' => 58]
    );
  }

  public function testThrowsExceptionWhenInstanceInWrongState()
  {
    $states = $this->parser->parseStateString(
      'isn\'t elderly and is admin'
    );

    $user = new User(1, [
      'is_admin' => false,
      'age' => 21
    ]);

    $this->expectException(
      StateValidationException::class
    );

    $this->provider->validateState(
      User::class,
      $user,
      $states
    );
  }
}