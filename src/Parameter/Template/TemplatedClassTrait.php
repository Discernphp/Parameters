<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Struct\Contract\ParameterStructInterface;
use Discern\Parameter\Template\Contract\ClassTemplateInterface;

trait TemplatedClassTrait {
  protected $params;

  protected $template;

  public function setParameterStruct(ParameterStructInterface $params)
  {
    $this->params = $params;
    return $this;
  }

  public function getParameterStruct()
  {
    return $this->params;
  }

  public function params()
  {
    return $this->params;
  }

  public function setClassTemplate(ClassTemplateInterface $template)
  {
    $this->template = $template;
    return $this;
  }

  public function getClassTemplate()
  {
    return $this->template;
  }

  public function with(array $arguments)
  {
    $env = $this->getParameterStruct()->getParameterConfigCollection();
    return $this->getClassTemplate()->populate($arguments, [
      'instance' => clone($this),
      'env' => $this->getParameterStruct()->getParameterConfigCollection(),
      'filters' => [
        'contains_params' => array_keys($arguments)
      ]
    ]);
  }
}
