<?php namespace Discern\Parameter\Contract;

interface TypeFactoryInterface {
  public function invokeParameter(ParameterInterface $config, array $params);
}
