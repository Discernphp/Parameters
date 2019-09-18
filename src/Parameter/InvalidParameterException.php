<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInterface;

class InvalidParameterException extends \InvalidArgumentException {
  protected $invalid_parameter;

  public function setInvalidParameter(ParameterInterface $param)
  {
    $this->invalid_parameter = $param;

    return $this;
  }

  public function getInvalidParameter()
  {
    return $this->invalid_parameter;
  }
}
