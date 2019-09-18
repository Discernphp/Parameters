<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Struct\Contract\Accessor\StructAccessorInterface;
use Discern\Parameter\Struct\Contract\Accessor\StructAccessorTrait;
use Discern\Parameter\Template\Contract\Accessor\ClassTemplateAccessorInterface;
use Discern\Parameter\Template\Contract\Accessor\ClassTemplateAccessorTrait;
use Discern\Parameter\Template\Contract\TemplatedClassInterface;
use Discern\Parameter\Template\TemplatedClassTrait;

final class TemplatedClass implements
  TemplatedClassInterface,
  StructAccessorInterface,
  ClassTemplateAccessorInterface {
  use 
    TemplatedClassTrait,
  	StructAccessorTrait,
  	ClassTemplateAccessorTrait;

  public function __set($property, $value)
  {
  	return $this->{$property} = $value;
  }
}