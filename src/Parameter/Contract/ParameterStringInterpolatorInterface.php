<?php namespace Discern\Parameter\Contract;

interface ParameterStringInterpolationInterface extends ParamertConfigInterface{
  public function setParamertConfig(ParamertConfigInterface $parameter);

  public function getParameterConfig();
}
