<?php namespace Discern\Parameter\Contract;

use Discern\Parameter\Contract\ParameterConfigChildFactoryInterface;
use Discern\Parameter\Object\Contract\ObjectAccessorInterface;

interface ParameterStringParserInterface {
  //extracts parameters from string
  public function extractParameterDefinitions($string);

  // converts parameter string into object
  public function parseParameterString($string);

  // inserts normalized parameters into subject
  public function injectParameters($subject, array $arguments = [], ParameterConfigCollectionInterface $env = null);

  public function wrapParameterString($parameterString);

  public function getParameterConfigFactory();

  public function setParameterConfigFactory(ParameterConfigFactoryInterface $factory);

  public function getObjectAccessor();

  public function setObjectAccessor(ObjectAccessorInterface $accessor);

  public function getParameterConfigCollectionFactory();

  public function setParameterConfigCollectionFactory(ParameterConfigCollectionFactoryInterface $factory);

  public function getParameterFactoryCollection();

  public function setParameterFactoryCollection(ParameterFactoryCollectionInterface $factory);

  public function getParameterInjectionFactory();

  public function setParameterInjectionFactory(ParameterInjectionFactoryInterface $factory);

  public function injectParameterString($param_string, $output_string, $subject);

  public function getParameterConfigChildFactory();

  public function setParameterConfigChildFactory(ParameterConfigChildFactoryInterface $factory);
}
