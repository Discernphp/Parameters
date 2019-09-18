<?php namespace Discern\Parameter\Contract;

interface ParameterChildInterface extends ParameterInterface {
  public function getParent();
}
