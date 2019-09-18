<?php namespace Discern\Parameter\Contract;

interface TypeFactoryCollectionInterface {
  public function add($id, TypeFactoryInterface $factory);

  public function get($id);
}