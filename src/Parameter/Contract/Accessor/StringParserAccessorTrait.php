<?php namespace Discern\Parameter\Contract\Accessor;

use Discern\Parameter\Contract\StringParserInterface;

trait StringParserAccessorTrait {
  public function getParameterStringParser()
  {
    if (!isset($this->parameter_string_parser)) {
      $class = get_class();
      throw UninitializedDependencyException::make(
        'Discern\Parameter\Contract\StringParserInterface', 
        $class,
        __METHOD__,
        'setParameterStringParser' 
      );
    }
      	
  	return $this->parameter_string_parser;
  }

  public function setParameterStringParser(StringParserInterface $parser)
  {
  	$this->parameter_string_parser = $parser;
  	return $this;
  }
}
