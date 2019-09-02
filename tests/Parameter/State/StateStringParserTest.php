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
      '(admin and super_admin) or ((anonymous? and first_login) or something)'
    );

    $this->assertEquals($states[0][0], 'admin');
    $this->assertEquals($states[0][1], 'super_admin');
    $this->assertEquals($states[1][0], 'anonymous?');
    $this->assertEquals($states[1][1], 'first_login');
    $this->assertEquals($states[2][0], 'something');
  }
}