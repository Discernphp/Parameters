<?php namespace Discern\Parameter\Contract;

interface ParameterConfigCollectionFactoryInterface {
  public function make(array $params = []);
}
