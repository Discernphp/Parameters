<?php namespace Discern\Parameter\State;

use Discern\Parameter\State\Contract\ActionResultContextInterface;

class ActionResultContext implements ActionResultContextInterface {
  private $id;

  private $options = [
    'negate' => false,
    'params' => [],
  ];

  public function __toString()
  {
    return $this->id;
  }

  public function __construct($id, array $options = [])
  {
    $this->id = $id;
    $this->options = array_intersect_key($options, $this->options);
  }

  public function getId()
  {
    return $this->id;
  }

  public function expects()
  {
    return $this->options['params'];
  }

  public function apply($result)
  {
    return $this->options['negate'] ? !$result : $result;
  }
}