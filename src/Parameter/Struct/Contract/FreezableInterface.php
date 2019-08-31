<?php namespace Discern\Parameter\Struct\Contract;

interface FreezableInterface {
  public function freeze();

  public function unfreeze();

  public function isFrozen();
}
