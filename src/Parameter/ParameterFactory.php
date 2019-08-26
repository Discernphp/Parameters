<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterFactoryInterface;
use Discern\Parameter\Contract\ParameterConfigInterface;

class ParameterFactory implements ParameterFactoryInterface {
  public function invokeParameter(ParameterConfigInterface $config, array $params)
  {
    $type = $config->getType();

    if (class_exists($type)) {
      $reflect  = new \ReflectionClass($type);
      return $reflect->newInstanceArgs($params);
    }

    $param = $params[0];
    settype($param, $type);
    return $param;
  }
}
