<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigChildFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigInterface;

class ParameterConfigChildFactory implements ParameterConfigChildFactoryInterface {
  public function make(ParameterConfigInterface $parent, $output_method)
  {
    return new ParameterConfigChild($parent, $output_method);
  }
}
