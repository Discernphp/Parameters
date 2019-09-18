<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\StringParserInterface;

interface StringParserAccessorInterface {
  public function getParameterStringParser();

  public function setParameterStringParser(StringParserInterface $parser);
}
