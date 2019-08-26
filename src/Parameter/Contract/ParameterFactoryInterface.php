<?php namespace Discern\Parameter\Contract;

interface ParameterFactoryInterface {
  public function invokeParameter(ParameterConfigInterface $config, array $params);
}
