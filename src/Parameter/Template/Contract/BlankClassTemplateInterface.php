<?php namespace Discern\Parameter\Template\Contract;

interface BlankClassTemplateInterface extends ClassTemplateInterface {
  public function with(array $properties, $class_template = null);
}