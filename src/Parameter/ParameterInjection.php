<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInjectionInterface;

class ParameterInjection implements ParameterInjectionInterface {
  protected $input;

  protected $objects;

  protected $output;

  protected $is_clean = true;

  public function __construct($input)
  {
    $this->setInput($input);
  }

  public function setObjects(array $objects)
  {
    $this->objects = $objects;
    return $this;
  }

  public function getObjects()
  {
    return $this->objects;
  }

  public function setOutput($output)
  {
    $this->output = $output;
    return $this;
  }

  public function getOutput()
  {
    return $this->output;
  }

  public function setInput($parameter_definition)
  {
    $this->input = $parameter_definition;
    return $this;
  }

  public function getInput()
  {
    return $this->input;
  }

  public function isClean()
  {
    return $this->is_clean;
  }

  public function setIsClean($boolean)
  {
    $this->is_clean = !!$boolean;
    return $this;
  }
}