<?php namespace Discern\Parameter\Template\Contract;

interface ClassTemplateInterface {
  public function getTemplatedClass();

  public function populate(array $arguments, array $options = []);

  public function getTemplateProperties(array $filters = []);
}