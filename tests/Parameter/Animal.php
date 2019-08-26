<?php namespace Discern\Test\Parameter;

class Animal{
  public $id;
  
  public function __construct($id)
  {
  	$this->id = $id;
  }

  public function getType()
  {
  	return 'dog';
  }

  public function getName()
  {
  	return 'Rover';
  }
}
