<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInterface;

class ParameterException extends \Exception {
  protected $param;

  public function setParameter(ParameterInterface $param)
  {
    $this->param = $param;
    return $this;
  }

  public function getParameter()
  {
    return $this->param;
  }
}
