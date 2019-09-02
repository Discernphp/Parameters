<?php namespace Discern\Parameter\State\Contract;

interface StateValidatorCollectionInterface {
  public function add($instance_id, StateValidatorInterface $validator);

  public function get($instance_id, $state_id);
}
