<?php namespace Discern\Parameter\Contract;

interface ParameterCollectionInterface {
  public function add(ParameterInterface $parameter);

  public function get($id);

  public function exists($id);

  public function all();
  //public function append(ParameterConfigCollectionInterface $parameter_collection);
}
