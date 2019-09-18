<?php namespace Discern\Parameter\Contract;

interface ParameterCollectionFactoryInterface {
  public function make(array $params = []);
}
