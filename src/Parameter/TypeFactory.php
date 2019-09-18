<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\TypeFactoryInterface;
use Discern\Parameter\Contract\ParameterInterface;

class TypeFactory implements TypeFactoryInterface {
  public function invokeParameter(ParameterInterface $config, array $params)
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
