<?php namespace Discern\Parameter\Object;

use Discern\Parameter\Object\Contract\ObjectAccessorInterface;

class ObjectAccessor implements ObjectAccessorInterface {
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
    $method = $action.$property_snake;
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

    throw new \InvalidArgumentException(
      sprintf(
        'Could not %s `%s.%s` using `%s`',
        $action,
        get_class($instance),
        $property,
        gettype($value)
      )
    );
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
