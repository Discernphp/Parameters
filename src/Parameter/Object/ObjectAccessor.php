<?php namespace Discern\Parameter\Object;

use Discern\Parameter\Object\Contract\ObjectAccessorInterface;
use Discern\Parameter\Template\Contract\BlankClassTemplateInterface;

class ObjectAccessor implements ObjectAccessorInterface {
  public function __construct($strict = false)
  {
    $this->strict = $strict;
  }

  /**
   * [get description]
   * @param  object $instance [description]
   * @param  [type] $property [description]
   * @return [type]           [description]
   */
  public function get($instance, $property = null)
  {
    if (!$property) {
      return $instance;
    }

    if ($this->propertyIsNested($property)) {
      list($instance, $property) = $this->getNestedProperty($instance, $property);
    }

    // allow `string` shorthand for `__toString`
    if ($property === 'string') {
      return $instance->__toString();
    }

    return $this->accessPropertyValue($instance, $property);
  }

  /**
   * [set description]
   * @param [type] $instance [description]
   * @param [type] $property [description]
   * @param [type] $value    [description]
   */
  public function set($instance, $property, $value)
  {
    if ($this->propertyIsNested($property)) {
      list($instance, $property) = $this->getNestedProperty($instance, $property);
    }

    $this->accessPropertyValue($instance, $property, 'set', $value);
    return $instance;
  }

  /**
   * [getNestedProperty description]
   * @param  [type] $instance [description]
   * @param  [type] $property [description]
   * @return [type]           [description]
   */
  public function getNestedProperty($instance, $property)
  {
    $i = 0;
    $properties = explode('.', $property);
    while ($i < count($properties)-1) {
      $instance = $this->accessPropertyValue($instance, $properties[$i]);
      $i++;
    }
    // return the last property
    return [$instance, $properties[$i]];
  }

  /**
   * [isStrict description]
   * @return boolean [description]
   */
  public function isStrict()
  {
    return $this->strict;
  }

  /**
   * [accessPropertyValue description]
   * @param  [type] $instance [description]
   * @param  [type] $property [description]
   * @param  string $action   [description]
   * @param  [type] $value    [description]
   * @return [type]           [description]
   */
  public function accessPropertyValue($instance, $property, $action='get', $value=null)
  {
    $property_camel = $this->toCamelCase($property);
    $method = $action.ucfirst($property_camel);
    if (method_exists($instance, $method)) {
      return $instance->{$method}($value);
    }

    $property_snake = $this->toSnakeCase($property);
    $method = $action.'_'.$property_snake;
    if (method_exists($instance, $method)) {
      return $instance->{$method}($value);
    }

    if (method_exists($instance, $property)) {
      return $instance->{$property}($value);
    }

    if (array_key_exists($property, get_class_vars(get_class($instance)))) {
      switch ($action) {
        case 'get':
          return $instance->{$property};
          break;
        case 'set':
          return $instance->{$property} = $value;
          break;
      }
    }

    $exception = '';

    if ($action === 'set') {
      // determine if the property is private or protected before setting
      // to avoid fatal error. We want to throw an exception instead
      // If the property is not private or protected, and doesn't exist
      // we will define it
      if (!$this->hasProtectedProperty($instance, $property) && !$this->isStrict()) {
        return $instance->{$property} = $value;
      }

      // attempt to set the property through magic setter if available
      if (method_exists($instance, '__set')) {
        try {
          return $instance->__set($property, $value);
        } catch (\Exception $e) {
          $exception_message = $e->getMessage();
          $exception_class = get_class($e);
          $exception = "Instance threw Exception `{$exception_class}` with message '{$exception_message}'";
        }
      }
    }

    $instance_class = get_class($instance);
    $value_type = gettype($value);
    $value = is_object($value) ? get_class($value) : $value;

    throw new \InvalidArgumentException(
      sprintf(
        'Could not %s `%s::%s` using `%s`:`%s`.
         Make sure the property is defined as `public` or is accessible via `%s::__set("%s", {value})`.
         %s',
        $action,
        $instance_class,
        $property,
        $value_type,
        $value,
        $instance_class,
        $property,
        $exception
      )
    );
  }

  /**
   * [hasPublicProperty description]
   * @param  [type]  $instance [description]
   * @param  [type]  $property [description]
   * @return boolean           [description]
   */
  public static function hasProtectedProperty($instance, $property)
  {
    $reflection = new \ReflectionObject($instance);
    $properties = $reflection->getProperties(
      \ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED
    );
    return !empty(array_filter($properties, function($prop) use($property) {
      return $property === $prop->getName();
    }));
  }

  /**
   * [propertyIsNested description]
   * @param  [type] $property [description]
   * @return [type]           [description]
   */
  public static function propertyIsNested($property)
  {
    return strpos($property, '.') !== false;
  }

  /**
   * [toCamelCase description]
   * @param  [type] $name [description]
   * @return [type]       [description]
   */
  public static function toCamelCase($name)
  {
    return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
  }

  /**
   * [toSnakeCase description]
   * @param  [type] $name [description]
   * @return [type]       [description]
   */
  public static function toSnakeCase($name)
  {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
  }
}
