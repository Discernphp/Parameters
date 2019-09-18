<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInterface;
use Discern\Parameter\Contract\ParameterChildInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;

class ParameterChild implements ParameterChildInterface, FreezableInterface {
  use FreezableTrait;

  private $parent;

  private $output_method;

  public function __construct(ParameterInterface $parent, $output_method)
  {
    $this->setParent($parent);
    $this->setOutputMethod($output_method);
    $this->freeze();
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
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage('Parameter', 'output_method')
    );

    $this->output_method = $method;
    return $this;
  }

  private function setParent(ParameterInterface $parent)
  {
    $this->parent = $parent;
    return $this;
  }
}