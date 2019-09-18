<?php namespace Discern\Parameter\Template\Contract\Accessor;

use Discern\Parameter\Template\Contract\ClassTemplateInterface;

trait ClassTemplateAccessorTrait {

  public function getClassTemplate()
  {
    if (!isset($this->class_template)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Template\Contract\ClassTemplateInterface', 
        $class,
        __METHOD__,
        'setClassTemplate' 
      );
    }

    return $this->class_template;
  }

  public function setClassTemplate(ClassTemplateInterface $template)
  {
  	$this->class_template = $template;
  	return $this;
  }
}
