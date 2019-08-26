<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;

class InvalidParameterConfigException extends \InvalidArgumentException {
  protected $invalid_parameter;

  public function setInvalidParameterConfig(ParameterConfigInterface $param)
  {
    $this->invalid_parameter = $param;

    return $this;
  }

  public function getInvalidParameterConfig()
  {
    return $this->invalid_parameter;
  }
}
