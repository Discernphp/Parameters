<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterInterface;
use Discern\Parameter\Contract\TypeValidatorInterface;
use Discern\Parameter\Contract\Accessor\TypeValidatorAccessorInterface;
use Discern\Parameter\Contract\Accessor\TypeValidatorAccessorTrait;
use Discern\Parameter\Struct\Contract\FreezableInterface;
use Discern\Parameter\Struct\FreezableTrait;

class Parameter implements ParameterInterface, TypeValidatorAccessorInterface, FreezableInterface {
  use FreezableTrait, TypeValidatorAccessorTrait;

  protected static $ALLOWED_TYPES = [
    'int',
    'object',
    'array',
    'string',
    'float'
  ];

  private $id;

  private $default_arguments;

  private $output_method;

  private $is_optional;

  protected $missing_parameter_exception;

  public function __construct($id, array $properties, TypeValidatorInterface $validator)
  {
    $this->setParameterTypeValidator($validator);

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

    $this->freeze();
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
    $exception_class_name = $this->missing_parameter_exception ?: ParameterException::class;
    $exception = new $exception_class_name(
      sprintf('Parameter %s is required, no value given', $this->getId())
    );

    if ($exception instanceof ParameterException) {
      $exception->setParameter($this);
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
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage('Parameter', 'id='.$id)
    );

    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $id)) {
      throw new \InvalidArgumentException("Parameter id `{$id}` invalid, no special characters allowed");
    }

    $this->id = $id;
    return $this;
  }

  protected function setIsOptional($is_optional)
  {
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage(
        'Parameter',
        'is_optional='.($is_optional ? 'true' : 'false')
      )
    );

    $this->is_optional = !!$is_optional;
    return $this;
  }

  protected function setDefaultArguments(array $default_arguments)
  {
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage(
        'Parameter',
        'default_arguments'
      )
    );

    $this->default_arguments = $default_arguments;
    return $this;
  }

  protected function setType($type)
  {
    $this->preventActionWhenFrozen(
      $this->getPreventActionMessage(
        'Parameter',
        'type'
      )
    );

    $type_validator = $this->getParameterTypeValidator();

    if (!in_array($type, static::$ALLOWED_TYPES) && !$type_validator->exists($type)) {
      $exception = new ParameterException(
        sprintf(
          'Invalid value set for {%s}::type: `%s`, type must be a class or in (%s)',
          $this->getId(),
          $type,
          implode(',', static::$ALLOWED_TYPES)
        )
      );

      $exception->setParameter($this);
      throw $exception;
    }

    $this->type = $type;
    return $this;
  }
}