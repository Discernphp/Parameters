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
      "(not admin and isn't super admin) or ((anonymous and (first login or last login)) or something)"
    );

    $admin = $states[0][0];
    $super_admin = $states[0][1];

    $this->assertEquals($admin->getId(), 'admin');
    $this->assertEquals($admin->apply(true), false);
    $this->assertEquals($super_admin->getId(), 'is super admin');
    $this->assertEquals($super_admin->apply(false), true);
    $this->assertEquals($states[1][0][0][0]->getId(), 'first login');
    $this->assertEquals($states[1][0][1][0]->getId(), 'last login');
    $this->assertEquals($states[1][2][0]->getId(), 'something');
  }

  public function testParsesStateExpressionParameters()
  {
    $states = $this->parser->parseStateString(
      'customers, that spent over :amount and that purchased before :date or something'
    );

    $this->assertEquals(
      $states[0][0]->getId(),
      'customers'
    );

    $this->assertEquals(
      $states[0][1]->getId(),
      'that spent over %p'
    );

    $this->assertSame(
      $states[0][1]->expects(),
      ['amount']
    );

    $this->assertEquals(
      $states[0][2]->getId(),
      'that purchased before %p'
    );

    $this->assertSame(
      $states[0][2]->expects(),
      ['date']
    );

    $this->assertEquals(
      $states[1][0]->getId(),
      'something'
    );
  }
}