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

    $this->parser = new StateStringParser();
    $this->provider = new StateValidationProvider();
    $this->provider->setStateValidatorCollection($states);
  }

  public function testCanValidateStateWithProvider()
  {
    $states = $this->parser->parseStateString(
      'is admin or is elderly'
    );

    $user = new User(1, [
      'is_admin' => true,
      'age' => 21
    ]);

    $result = $this->provider->validateState(
      User::class,
      $user,
      $states
    );

    $this->assertEquals(
      $result,
      true
    );
  }

  public function testThrowsExceptionWhenInstanceInWrongState()
  {
    $states = $this->parser->parseStateString(
      'is elderly and has pet'
    );

    $user = new User(1, [
      'is_admin' => true,
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