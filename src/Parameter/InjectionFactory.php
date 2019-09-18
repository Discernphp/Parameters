<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\InjectionFactoryInterface;

class InjectionFactory implements InjectionFactoryInterface {
  public function make($input)
  {
  	return new Injection($input);
  }
}
