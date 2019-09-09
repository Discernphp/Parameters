<?php namespace Discern\Parameter\State\Contract;

interface ActionResultContextInterface {
  public function getId();

  public function apply($action_result);
}