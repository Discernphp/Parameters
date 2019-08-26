<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Contract\ParameterConfigCollectionInterface;
use Discern\Parameter\InvalidParameterConfigException;

class ParameterConfigCollection implements ParameterConfigCollectionInterface {
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
    if ($this->isFrozen()) {
      throw new \BadMethodCallException(
        sprintf(
          'ParameterCollection frozen, cannot add new ParamterConfig(`%s`),
           call `ParameterCollection::unfreeze()` first',
          $param->getId()
        )
      );
    }

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

  /**
   * @param  mixed $id
   * @return boolean
   */
  
  public function exists($id)
  {
    return isset($this->properties[$id]);
  }

  public function isFrozen()
  {
    return $this->frozen;
  }

  public function freeze()
  {
    $this->frozen = true;
    return $this;
  }

  public function unfreeze()
  {
    $this->frozen = false;
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