<?php namespace Discern\Parameter\State\Contract;

interface StateValidationProviderInterface {
  public function validateState($instance_id, $instance, array $states);
}
