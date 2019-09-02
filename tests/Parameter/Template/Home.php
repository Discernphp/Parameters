<?php namespace Discern\Test\Parameter\Template;

use Discern\Parameter\Template\Contract\TemplatedClassInterface;
use Discern\Parameter\Template\TemplatedClassTrait;

class Home implements TemplatedClassInterface {
  use TemplatedClassTrait;

  public $owner_name;

  public $pet_name;

  public $address;
}