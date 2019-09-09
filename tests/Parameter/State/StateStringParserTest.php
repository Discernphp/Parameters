<?php namespace Discern\Test\Parameter\State;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\State\StateStringParser;

final class StateStringParserTest extends TestCase {
  public function __construct()
  {
    $this->parser = new StateStringParser();
  }

  public function testParsesStateExpressionIntoArray()
  {
    $states = $this->parser->parseStateString(
      'update with %p, log, notify earner or track, refresh and send 200'
    );

    $states = $this->parser->parseStateString(
      "(not admin and isn't super admin) or ((anonymous and (first login or last login)) or something)"
    );

    $admin = $states[0][0][0];
    $super_admin = $states[0][0][1];

    $this->assertEquals($admin->getId(), 'admin');
    $this->assertEquals($admin->apply(true), false);
    $this->assertEquals($super_admin->getId(), 'is super admin');
    $this->assertEquals($super_admin->apply(false), true);
    $this->assertEquals($states[0][1][0][0][0]->getId(), 'first login');
    $this->assertEquals($states[0][1][0][1][0]->getId(), 'last login');
    $this->assertEquals($states[0][1][2][0]->getId(), 'something');
  }
}