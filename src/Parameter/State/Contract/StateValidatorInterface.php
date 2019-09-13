<?php namespace Discern\Parameter\State\Contract;

interface StateValidatorInterface {
  public function getId();

  public function getStateDescription();
}