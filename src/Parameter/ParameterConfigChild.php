<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Contract\ParameterConfigChildInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;

class ParameterConfigChild implements ParameterConfigChildInterface, FreezableInterface {
  use FreezableTrait;

  private $parent;

  private $output_method;

  public function __construct(ParameterConfigInterface $parent, $output_method)
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
      $this->getPreventActionMessage('ParameterConfig', 'output_method')
    );

    $this->output_method = $method;
    return $this;
  }

  private function setParent(ParameterConfigInterface $parent)
  {
    $this->parent = $parent;
    return $this;
  }
}