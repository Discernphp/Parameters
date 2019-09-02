<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Template\Contract\TemplatedClassInterface;
use Discern\Parameter\Template\TemplatedClassTrait;

final class TemplatedClass implements TemplatedClassInterface {
  use TemplatedClassTrait;

  public function __set($property, $value)
  {
  	return $this->{$property} = $value;
  }
}