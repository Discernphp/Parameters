<?php namespace Discern\Test\Parameter\Template;

use Discern\Parameter\Template\Contract\TemplatedClassInterface;
use Discern\Parameter\Template\TemplatedClassTrait;
use Discern\Parameter\Struct\Contract\Accessor\StructAccessorInterface;
use Discern\Parameter\Struct\Contract\Accessor\StructAccessorTrait;
use Discern\Parameter\Template\Contract\Accessor\ClassTemplateAccessorInterface;
use Discern\Parameter\Template\Contract\Accessor\ClassTemplateAccessorTrait;

class Home implements TemplatedClassInterface, StructAccessorInterface, ClassTemplateAccessorInterface {
  use TemplatedClassTrait;
  use StructAccessorTrait;
  use ClassTemplateAccessorTrait;

  public $owner_name;

  public $pet_name;

  public $address;
}