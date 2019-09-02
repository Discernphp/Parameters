<?php namespace Discern\Parameter\State;

use Discern\Parameter\State\Contract\StateValidatorCollectionInterface;
use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\State\Contract\StateValidatorInterface;

class StateValidatorCollection implements StateValidatorCollectionInterface {
  protected $validators_array = [];

  public static $DEFAULT_STATE_VALIDATOR_ID = '__default';

  public function add($instance_id, StateValidatorInterface $validator)
  {
    $state_id = $validator->getId();
    
    if (!isset($this->validators_array[$instance_id])) {
      $this->validators_array[$instance_id] = [];
    }

    if (!isset($this->validators_array[$instance_id][$state_id])) {
      $this->validators_array[$instance_id][$state_id] = [];
    }

    $this->validators_array[$instance_id][$state_id][] = $validator;
    return $this;
  }

  public function get($instance_id, $state_id)
  {
    $validators = $this->validators_array;

    if ($instance_id instanceof ParameterConfigInterface) {
      $param_id = $instance_id->getId();
      $instance_id = isset($validators[$param_id][$state_id]) ? $param_id : $id->getType();
    }

    $default = static::$DEFAULT_STATE_VALIDATOR_ID;
    $id =  isset($validators[$instance_id][$state_id]) ? $instance_id : $default;

    if (!isset($validators[$id][$state_id])) {
      throw new \InvalidArgumentException(
        sprintf(
          'Attempted to get undefined `StateValidator` using `id=%s` for `state=%s`',
          $id,
          $state_id
        )
      );
    }

    $last_element = count($validators[$id][$state_id]) - 1;

    return $validators[$id][$state_id][$last_element];
  }
}
