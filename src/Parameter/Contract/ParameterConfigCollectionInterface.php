<?php namespace Discern\Parameter\Contract;

interface ParameterConfigCollectionInterface {
  public function add(ParameterConfigInterface $parameter);

  public function get($id);

  public function exists($id);

  public function isFrozen();

  public function freeze();

  public function unfreeze();
  //public function append(ParameterConfigCollectionInterface $parameter_collection);
}
