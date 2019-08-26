<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Contract\ParameterConfigChildInterface;

class ParameterConfigChild implements ParameterConfigChildInterface {
  private $parent;

  private $output_method;

  public function __construct(ParameterConfigInterface $parent, $output_method)
  {
    $this->setParent($parent);
    $this->setOutputMethod($output_method);
  }

  public function getId()
  {
    return $this->getParent()->getId().'.'.$this->getOutputMethod();
  }

  public function getType()
  {
    return $this->getParent()->getType();
  }

  public function getDefaultArguments()
  {
    return $this->getParent()->getDefaultArguments();
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function getOutputMethod()
  {
    return $this->output_method;
  }

  public function isOptional()
  {
    return $this->getParent()->isOptional();
  }

  public function makeMissingParameterException()
  {
    return $this->getParent()->makeMissingParameterException();
  }

  private function setOutputMethod($method)
  {
    $this->output_method = $method;
    return $this;
  }

  private function setParent(ParameterConfigInterface $parent)
  {
    $this->parent = $parent;
    return $this;
  }
}