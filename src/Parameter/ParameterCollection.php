<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInterface;
use Discern\Parameter\Contract\ParameterCollectionInterface;
use Discern\Parameter\InvalidParameterException;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;

class ParameterCollection implements ParameterCollectionInterface, FreezableInterface {
  use FreezableTrait;
  /**
   * @var array
   */
  protected $properties;

  /**
   * @param array Discern\Parameter\Contract\ParameterInterface $parameters
   */
  public function __construct(array $parameters = [])
  {
    $this->addArray($parameters);
  }

  /**
   * Adds property to property list if the key doesn't exist. Fails otherwise.
   * @param Discern\Parameter\Contract\ParameterInterface $param
   */
  public function add(ParameterInterface $param)
  {
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage('ParameterCollection:add', "Paramter(`{$param->getId()}`)")
    );

    $this->validateParameter($param);

    if ($this->exists($param->getId())) {
      throw $this->createException(
        sprintf('Parameter `%s` already exists, replacing parameters is forbidden', $param->getId()),
        $param
      );
    }

    $this->properties[$param->getId()] = $param;

    return $this;
  }

  /**
   * @param array Discern\Parameter\Contract\ParameterInterface $parameters
   * @return   [<description>]
   */
  public function addArray(array $parameters)
  {
    array_map(function($parameter){
      $this->add($parameter);
    }, $parameters);

    return $this;
  }

  /**
   * @param  string $id            id of the property to get
   * @return Discern\Parameter\Contract\ParameterInterface
   */
  
  public function get($id) {
    if (!$this->exists($id)) {
      throw $this->createException("Cannot get undefined parameter with id {$id}");
      return;
    }

    return $this->properties[$id];
  }

  public function all()
  {
    return $this->properties;
  }

  /**
   * @param  mixed $id
   * @return boolean
   */
  
  public function exists($id)
  {
    return isset($this->properties[$id]);
  }

  /**
   * @return Discern\Parameters\ParameterCollection - instance
   */
  public function freeze()
  {
    $this->is_frozen = true;

    array_map(function($param){
      return $param->freeze();
    }, $this->all());

    return $this;
  }

  /**
   * @return Discern\Parameters\ParameterCollection - instance
   */
  public function unfreeze()
  {
    $this->is_frozen = false;

    array_map(function($param){
      return $param->unfreeze();
    }, $this->all());

    return $this;
  }

  /**
   * @param  string $message
   * @param  Discern\Parameter\Contract\ParameterInterface $invalid_param
   * @return Discern\Parameter\ParameterCollection
   */

  protected function createException($message, ParameterInterface $invalid_param=null)
  {
    $exception = new InvalidParameterException($message);

    if ($invalid_param) {
      $exception->setInvalidParameter($invalid_param);
    }

    return $exception;
  }

  /**
   * 
   */

  protected function validateParameter(ParameterInterface $param)
  {
    if (!$param->getType()) {
      throw $this->createException("%s::type cannot be empty", get_class($param), $param);
    }
  }
}