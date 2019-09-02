<?php namespace Discern\Test\Parameter;

class User{
  public $id;
  
  public function __construct($id, array $info = [])
  {
  	$this->id = $id;
    $this->pet = new Animal($id);
    $this->first_name = isset($info['first_name']) ? $info['first_name'] : 'Richard';
    $this->last_name = isset($info['last_name']) ? $info['last_name'] : 'Lee';
    $this->age = isset($info['age']) ? $info['age'] : 32;
    $this->address = isset($info['address']) ? $info['address'] : '333 25th lane';
    $this->is_admin = isset($info['is_admin']) ? $info['is_admin'] : false;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getFirstName()
  {
    return $this->first_name;
  }

  public function getLastName()
  {
    return $this->last_name;
  }

  public function getAge()
  {
  	return $this->age;
  }

  public function getPet()
  {
    return $this->pet;
  }

  public function getAddress()
  {
    return $this->address;
  }

  public function isAdmin()
  {
    return $this->is_admin;
  }
}
