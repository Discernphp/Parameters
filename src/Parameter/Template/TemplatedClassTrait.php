<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Template\Contract\ClassTemplateInterface;

trait TemplatedClassTrait {
  public function params()
  {
    return $this->getParameterStruct();
  }

  public function with(array $arguments)
  {
    $env = $this->getParameterStruct()->getParameterCollection();
    return $this->getClassTemplate()->populate($arguments, [
      'instance' => clone($this),
      'env' => $this->getParameterStruct()->getParameterCollection(),
      'filters' => [
        'contains_params' => array_keys($arguments)
      ]
    ]);
  }
}
