<?php namespace Discern\Parameter;

class UninitializedDependencyException extends \RuntimeException {
  public static function make($dependency, $class, $getter, $setter)
  {
    return new static(
      "`$class` has a dependency on `$dependency` that is uninitialized when `$getter` is called. Try calling `$class::$setter` first."
    );
  }
}