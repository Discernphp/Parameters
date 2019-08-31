<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\InvalidParameterConfigException;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;

class ParameterConfigCollection implements ParameterConfigCollectionInterface {
  use FreezableTrait;
  /**
   * @var array
   */
  protected $properties;

  
  protected $frozen = false;

  /**
   * @param array Discern\Parameter\Contract\ParameterConfigInterface $parameters
   */
  public function __construct(array $parameters = [])
  {
    $this->addArray($parameters);
  }

  /**
   * Adds property to property list if the key doesn't exist. Fails otherwise.
   * @param Discern\Parameter\Contract\ParameterConfigInterface $param
   */
  public function add(ParameterConfigInterface $param)
  {
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage('ParameterCollection:add', "ParamterConfig(`{$param->getId()}`)")
    );

    $this->validateParameterConfig($param);

    if ($this->exists($param->getId())) {
      throw $this->createException(
        sprintf('ParameterConfig `%s` already exists, replacing parameters is forbidden', $param->getId()),
        $param
      );
    }

    $this->properties[$param->getId()] = $param;

    return $this;
  }

  /**
   * @param array Discern\Parameter\Contract\ParameterConfigInterface $parameters
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
   * @return Discern\Parameter\Contract\ParameterConfigInterface
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
   * @return Discern\Parameters\ParameterConfigCollection - instance
   */
  public function freeze()
  {
    $this->frozen = true;

    array_map(function($param){
      return $param->freeze();
    }, $this->all());

    return $this;
  }

  /**
   * @return Discern\Parameters\ParameterConfigCollection - instance
   */
  public function unfreeze()
  {
    $this->frozen = false;

    array_map(function($param){
      return $param->unfreeze();
    }, $this->all());

    return $this;
  }

  /**
   * @param  string $message
   * @param  Discern\Parameter\Contract\ParameterConfigInterface $invalid_param
   * @return Discern\Parameter\ParameterConfigCollection
   */

  protected function createException($message, ParameterConfigInterface $invalid_param=null)
  {
    $exception = new InvalidParameterConfigException($message);

    if ($invalid_param) {
      $exception->setInvalidParameterConfig($invalid_param);
    }

    return $exception;
  }

  /**
   * 
   */

  protected function validateParameterConfig(ParameterConfigInterface $param)
  {
    if (!$param->getType()) {
      throw $this->createException("%s::type cannot be empty", get_class($param), $param);
    }
  }
}