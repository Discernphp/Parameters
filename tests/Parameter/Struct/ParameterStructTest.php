<?php namespace Discern\Test\Parameter;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\Struct\ParameterStructFactory;
use Discern\Parameter\ParameterConfigCollectionFactory;
use Discern\Parameter\ParameterConfig;
use Discern\Parameter\ParameterConfigCollection;
use Discern\Parameter\ParameterFactoryCollection;
use Discern\Parameter\ParameterFactory;
use Discern\Parameter\ParameterConfigException;
use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Test\Parameter\User;
use Discern\Test\Parameter\Animal;

final class ParameterStructTest extends TestCase {
  public function __construct()
  {
    $this->struct = new ParameterStructFactory();

    $this->params = new ParameterConfigCollectionFactory();
    
    // initialize factory collection
    $param_factory_collection = new ParameterFactoryCollection();
    $param_factory = new ParameterFactory();
    $param_factory_collection
      ->add(ParameterFactoryCollection::$DEFAULT_FACTORY_ID, $param_factory);
    $this->param_factory = $param_factory_collection;
  }

  public function testCanGetParamInstancesFromStruct()
  {
    $params = $this->params->make([
      new ParameterConfig('user', [
        'type' => User::class
      ]),
      new ParameterConfig('pet', [
        'type' => Animal::class
      ]),      
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
      new ParameterConfig('user', [
        'type' => User::class
      ]),   
    ]);

    $this->expectException(ParameterConfigException::class);

    $struct = $this->struct->make($params, $this->param_factory);

    $struct->setProperties([
      'user' => new Animal(1),
    ]);
  }

  public function testFailsWhenSettingPropertyofFrozenStruct()
  {
    $params = $this->params->make([
      new ParameterConfig('user', [
        'type' => User::class
      ]),   
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
