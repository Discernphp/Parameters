<?php namespace Discern\Parameter\Contract;

interface ParameterInjectionInterface {
  public function setObjects(array $objects);

  public function getObjects();

  public function setOutput($output);

  public function getOutput();

  public function setInput($parameter_definition);

  public function getInput();

  public function isClean();

  public function setIsClean($boolean);
}
