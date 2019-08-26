<?php namespace Discern\Parameter\Contract;

interface ParameterConfigChildFactoryInterface{
  public function make(ParameterConfigInterface $param, $output_method);
}
