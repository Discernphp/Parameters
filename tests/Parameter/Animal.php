<?php namespace Discern\Test\Parameter;

class Animal{
  public $id;
  
  public function __construct($id, array $params = [])
  {
  	$this->id = $id;

    $this->name = isset($params['name']) ? $params['name'] : 'Rover';
    $this->type = isset($params['type']) ? $params['type'] : 'dog';
  }

  public function getType()
  {
  	return $this->type;
  }

  public function getName()
  {
  	return $this->name;
  }
}
