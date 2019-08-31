<?php namespace Discern\Parameter\Struct;

use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\Contract\ParameterFactoryCollectionInterface;
use Discern\Parameter\Struct\Contract\ParameterStructInterface;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\ParameterConfigException;

trait FreezableTrait {
  private $is_frozen = false;

  public function unfreeze()
  {
    $this->is_frozen = false;
    return $this;
  }

  public function freeze()
  {
    $this->is_frozen = true;
    return $this;
  }

  public function isFrozen()
  {
    return $this->is_frozen;
  }

  private function preventActionWhenFrozen($exception_message)
  {
    if ($this->isFrozen()) {
      throw new \BadMethodCallException($exception_message);
    }
  }

  protected static function getPreventActionMessage($object_id, $property)
  {
    return (
      "Cannot set value of `{$object_id}::{$property}` when `{$object_id}` is `frozen`.
      If you need to alter the `{$object_id}`, call `{$object_id}::unfreeze()` first."
    );
  }
}