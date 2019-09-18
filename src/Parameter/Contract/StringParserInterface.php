<?php namespace Discern\Parameter\Contract;

use Discern\Parameter\Contract\ParameterConfigChildFactoryInterface;
use Discern\Parameter\Object\Contract\ObjectAccessorInterface;

interface StringParserInterface {
  //extracts parameters from string
  public function extractParameterDefinitions($string);

  // converts parameter string into object
  public function parseParameterString($string);

  // inserts normalized parameters into subject string
  public function injectParameters($subject, array $arguments = [], ParameterCollectionInterface $env = null);

  // insert normalized parameters into array of subject string
  public function arrayInjectParameters(array $subject, array $arguments, ParameterCollectionInterface $env = null);
}
