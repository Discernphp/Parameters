<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Template\TemplatedClass;
use Discern\Parameter\Template\Contract\ClassTemplateTrait;
use Discern\Parameter\Template\Contract\BlankClassTemplateInterface;
use Discern\Parameter\Object\Contract\Accessor\ObjectAccessorAccessorInterface;
use Discern\Parameter\Object\Contract\Accessor\ObjectAccessorAccessorTrait;
use Discern\Parameter\Contract\Accessor\ParameterCollectionFactoryAccessorInterface;
use Discern\Parameter\Contract\Accessor\ParameterCollectionFactoryAccessorTrait;
use Discern\Parameter\Contract\Accessor\TypeFactoryCollectionAccessorInterface;
use Discern\Parameter\Contract\Accessor\TypeFactoryCollectionAccessorTrait;
use Discern\Parameter\Contract\Accessor\StringParserAccessorInterface;
use Discern\Parameter\Contract\Accessor\StringParserAccessorTrait;
use Discern\Parameter\Struct\Contract\Accessor\StructFactoryAccessorInterface;
use Discern\Parameter\Struct\Contract\Accessor\StructFactoryAccessorTrait;


class ClassTemplate implements 
  BlankClassTemplateInterface,
  ObjectAccessorAccessorInterface,
  ParameterCollectionFactoryAccessorInterface,
  TypeFactoryCollectionAccessorInterface,
  StringParserAccessorInterface,
  StructFactoryAccessorInterface {
  use
    ClassTemplateTrait,
    ObjectAccessorAccessorTrait,
    ParameterCollectionFactoryAccessorTrait,
    TypeFactoryCollectionAccessorTrait,
    StringParserAccessorTrait,
    StructFactoryAccessorTrait;

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