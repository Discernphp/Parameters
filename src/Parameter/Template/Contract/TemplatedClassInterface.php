<?php namespace Discern\Parameter\Template\Contract;

use Discern\Parameter\Struct\Contract\ParameterStructInterface;

interface TemplatedClassInterface {
  public function setParameterStruct(ParameterStructInterface $struct);

  public function getParameterStruct();

  public function setClassTemplate(ClassTemplateInterface $template);

  public function getClassTemplate();

  public function with(array $arguments);
}