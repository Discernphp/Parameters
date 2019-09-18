<?php namespace Discern\Parameter\Contract;

interface ParameterFactoryInterface {
  public function make($id, array $properties);
}
