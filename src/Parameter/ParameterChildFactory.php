<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterChildFactoryInterface;
use Discern\Parameter\Contract\ParameterInterface;

class ParameterChildFactory implements ParameterChildFactoryInterface {
  public function make(ParameterInterface $parent, $output_method)
  {
    return new ParameterChild($parent, $output_method);
  }
}
