<?php namespace Discern\Parameter\Contract;

interface ParameterFactoryCollectionInterface {
  public function add($id, ParameterFactoryInterface $factory);

  public function get($id);
}