<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInjectionFactoryInterface;

class ParameterInjectionFactory implements ParameterInjectionFactoryInterface {
  public function make($input)
  {
  	return new ParameterInjection($input);
  }
}
