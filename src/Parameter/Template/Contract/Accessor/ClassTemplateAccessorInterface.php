<?php namespace Discern\Parameter\Template\Contract\Accessor;

use Discern\Parameter\Template\Contract\ClassTemplateInterface;

interface ClassTemplateAccessorInterface {

  public function getClassTemplate();

  public function setClassTemplate(ClassTemplateInterface $template);
}
