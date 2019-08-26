<?php namespace Discern\Parameter\Contract;

interface ParameterConfigFactoryInterface {
  public function make($id, array $properties);
}
