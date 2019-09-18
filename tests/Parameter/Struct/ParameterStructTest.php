<?php namespace Discern\Test\Parameter;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\Struct\StructFactory;
use Discern\Parameter\ParameterCollectionFactory;
use Discern\Parameter\Parameter;
use Discern\Parameter\ParameterCollection;
use Discern\Parameter\TypeValidator;
use Discern\Parameter\TypeFactory;
use Discern\Parameter\TypeFactoryCollection;
use Discern\Parameter\ParameterException;
use Discern\Parameter\Contract\ParameterInterface;
use Discern\Test\Parameter\User;
use Discern\Test\Parameter\Animal;

final class ParameterStructTest extends TestCase {
  public function __construct()
  {
    $this->struct = new StructFactory();

    $this->params = new ParameterCollectionFactory();
    $this->type_validator = new TypeValidator();
    
    // initialize factory collection
    $param_factory_collection = new TypeFactoryCollection();
    $param_factory = new TypeFactory();
    $param_factory_collection
      ->add(TypeFactoryCollection::$DEFAULT_FACTORY_ID, $param_factory);
    $this->param_factory = $param_factory_collection;
  }

  public function testCanGetParamInstancesFromStruct()
  {
    $params = $this->params->make([
      new Parameter('user', [
        'type' => User::class
      ], $this->type_validator),
      new Parameter('pet', [
        'type' => Animal::class
      ], $this->type_validator),      
    ]);

    $struct = $this->struct->make($params, $this->param_factory);
    $struct->setProperties([
      'user' => [1],
      'pet'  => new Animal(1),
    ]);

    $this->assertInstanceOf(
      User::class,
      $struct->user
    );

    $this->assertInstanceOf(
      Animal::class,
      $struct->pet
    );
  }

  public function testFailsWhenIncorrectTypeGiven()
  {
    $params = $this->params->make([
      new Parameter('user', [
        'type' => User::class
      ], $this->type_validator),   
    ]);

    $this->expectException(ParameterException::class);

    $struct = $this->struct->make($params, $this->param_factory);

    $struct->setProperties([
      'user' => new Animal(1),
    ]);
  }

  public function testFailsWhenSettingPropertyofFrozenStruct()
  {
    $params = $this->params->make([
      new Parameter('user', [
        'type' => User::class
      ], $this->type_validator),   
    ]);

    $struct = $this->struct
      ->make($params, $this->param_factory)
      ->freeze();

    $this->expectException(\BadMethodCallException::class);

    $struct->setProperties([
      'user' => [1],
    ]);
  }
}
