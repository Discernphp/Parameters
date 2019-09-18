<?php namespace Discern\Parameter\Contract;

interface ParameterInterface {
  public function getId();

  public function getType();

  public function getDefaultArguments();

  public function getOutputMethod();

  public function isOptional();

  public function makeMissingParameterException();
}
