<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Template\TemplatedClass;
use Discern\Parameter\Template\ClassTemplateTrait;
use Discern\Parameter\Template\Contract\BlankClassTemplateInterface;

final class ClassTemplate implements BlankClassTemplateInterface {
  use ClassTemplateTrait;

  protected $templated_class = TemplatedClass::class;

  protected $template_properties = [];

  public function with(array $properties, $class_template = null)
  {
    $instance = clone($this);
    if ($class_template) {
      $instance->setTemplatedClassName($class_template);
    }
    return $instance->setTemplateProperties($properties);
  }

  private function setTemplatedClassName($class_name)
  {
    $this->templated_class = $class_name;
    return $this;
  }

  private function setTemplateProperties(array $template_properties)
  {
    foreach ($template_properties as $key => $value) {
      if ($key === 'templated_name') {
        continue;
      }
      $this->template_properties[] = $key;
      $this->{$key} = $value;
    }
    return $this;
  }
}