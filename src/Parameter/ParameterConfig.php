<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Contract\ParameterConfigCollectionInterface;

class ParameterConfig implements ParameterConfigInterface {
  protected static $ALLOWED_TYPES = [
    'int',
    'object',
    'array',
    'string',
    'float'
  ];

  protected $id;

  protected $default_arguments;

  protected $output_method;

  protected $is_optional;

  protected $missing_parameter_exception;

  public function __construct($id, array $properties)
  {
    $this->setId($id);
    $this->setType($properties['type']);
    
    if (isset($properties['default_arguments'])) {
      $this->setDefaultArguments($properties['default_arguments']); 
    }

    if (isset($properties['output_method'])) {
      $this->setOutputMethod($properties['output_method']);
    }

    if (isset($properties['is_optional'])) {
      $this->setIsOptional($properties['is_optional']);
    }

    if (isset($properties['missing_parameter_exception'])) {
      $this->setMissingParameterException($properties['missing_parameter_exception']);
    }
  }

  public function getId()
  {
    return $this->id;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getDefaultArguments()
  {
    return $this->default_arguments ?: [];
  }

  public function getOutputMethod()
  {
    return $this->output_method;
  }

  public function setOutputMethod($method)
  {
    $this->output_method = $method;
    return $this;
  }

  public function isOptional()
  {
    return $this->is_optional;
  }

  public function makeMissingParameterException()
  {
    $exception_class_name = $this->missing_parameter_exception ?: ParameterConfigException::class;
    $exception = new $exception_class_name(
      sprintf('Parameter %s is required, no value given', $this->getId())
    );

    if ($exception instanceof ParameterConfigException) {
      $exception->setParameterConfig($this);
    }

    return $exception;
  }

  public function setMissingParameterException($exception_class_name)
  {
    $this->missing_parameter_exception = (string) $exception_class_name;
    return $this;
  }

  protected function setId($id)
  {
    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id)) {
      throw new \InvalidArgumentException("Parameter id `{$id}` invalid, no special characters allowed");
    }

    $this->id = $id;
    return $this;
  }

  protected function setIsOptional($is_optional)
  {
    $this->is_optional = !!$is_optional;
    return $this;
  }

  protected function setDefaultArguments(array $default_arguments)
  {
    $this->default_arguments = $default_arguments;
    return $this;
  }

  protected function setType($type)
  {
    if (!class_exists($type) && !in_array($type, static::$ALLOWED_TYPES)) {
      $exception = new ParameterConfigException(
        sprintf(
          'Invalid value set for {%s}::type: `%s`, type must be a class or in (%s)',
          $this->getId(),
          $type,
          implode(',', static::$ALLOWED_TYPES)
        )
      );

      $exception->setParameterConfig($this);
      throw $exception;
    }

    $this->type = $type;
    return $this;
  }
}