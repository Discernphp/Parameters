<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;

class ParameterConfigException extends \Exception {
  protected $param_config;

  public function setParameterConfig(ParameterConfigInterface $param)
  {
    $this->param_config = $param;
    return $this;
  }

  public function getParameterConfig()
  {
    return $this->param_config;
  }
}
