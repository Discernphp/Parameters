<?php namespace Discern\Parameter\Contract;

interface ParameterChildFactoryInterface{
  public function make(ParameterInterface $param, $output_method);
}
