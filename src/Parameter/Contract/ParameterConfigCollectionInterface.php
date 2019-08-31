<?php namespace Discern\Parameter\Contract;

interface ParameterConfigCollectionInterface {
  public function add(ParameterConfigInterface $parameter);

  public function get($id);

  public function exists($id);

  public function all();
  //public function append(ParameterConfigCollectionInterface $parameter_collection);
}
