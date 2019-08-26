<?php namespace Discern\Parameter\Contract;

interface ParameterConfigChildInterface extends ParameterConfigInterface {
  public function getParent();
}
