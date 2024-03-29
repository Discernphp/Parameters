<?php namespace Discern\Test\Parameter;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\StringParser;
use Discern\Parameter\ParameterCollectionFactory;
use Discern\Parameter\ParameterCollection;
use Discern\Parameter\TypeFactory;
use Discern\Parameter\ParameterFactory;
use Discern\Parameter\ParameterChildFactory;
use Discern\Parameter\TypeFactoryCollection;
use Discern\Parameter\InjectionFactory;
use Discern\Parameter\Contract\ParameterInterface;
use Discern\Parameter\InvalidParameterException;
use Discern\Parameter\TypeValidator;
use Discern\Test\Parameter\User;
use Discern\Test\Parameter\Animal;
use Discern\Parameter\Object\ObjectAccessor;

final class ParameterStringParserTest extends TestCase {
  public function __construct()
  {
    $param_collection_factory = new ParameterCollectionFactory();
    $param_factory_collection = new TypeFactoryCollection();
    $param_factory = new TypeFactory();
    $param_config_factory = new ParameterFactory();
    $param_config_child = new ParameterChildFactory();
    $injection_factory = new InjectionFactory();
    $accessor = new ObjectAccessor();
    $parser = new StringParser();
    $type_validator = new TypeValidator();
    $param_config_factory->setParameterTypeValidator($type_validator);

    $param_factory_collection
      ->add(TypeFactoryCollection::$DEFAULT_FACTORY_ID, $param_factory);
    
    $parser
      ->setParameterTypeFactoryCollection($param_factory_collection)
      ->setParameterCollectionFactory($param_collection_factory)
      ->setParameterFactory($param_config_factory)
      ->setParameterInjectionFactory($injection_factory)
      ->setObjectAccessor($accessor)
      ->setParameterChildFactory($param_config_child);

    $this->parser = $parser;
    $this->param_collection = $param_collection_factory;
  }

  public function testCanExtractParameterDefinitions()
  {
    $string = 'categories/{category_id:UserCategory.id}/users/{user_id:User.id}';
    $definitions = $this->parser->extractParameterDefinitions($string);

    $this->assertEquals(
      count($definitions),
      2
    );

    $this->assertEquals(
      $definitions[0],
      'category_id:UserCategory.id'
    );

    $this->assertEquals(
      $definitions[1],
      'user_id:User.id'
    );
  }

  public function testCanConvertStringToParameter()
  {
    $user_class = User::class;

    $param_config = $this->parser->parseParameterString('user:'.$user_class.'.name|[1]');

    $this->assertInstanceOf(ParameterInterface::class,$param_config);

    $this->assertEquals(
      $param_config->getId(),
      'user'
    );

    $this->assertEquals(
      $param_config->getType(),
      $user_class
    );

    $this->assertEquals(
      $param_config->getOutputMethod(),
      'name'
    );

    $this->assertEquals(
      $param_config->getDefaultArguments()[0],
      1
    );
  }

  public function testCanInjectParametersIntoString()
  {
    $user = new User(1);

    $injection = $this->parser->injectParameters(
      '{user:Discern\Test\Parameter\User.first_name} {user.last_name} is {user.age} years old',
      ['user' => [1]],
      new ParameterCollection()
    );

    $this->assertEquals(
      sprintf('%s %s is %s years old', $user->getFirstName(), $user->getLastName(), $user->getAge()),
      $injection->getOutput()
    );
    
    $this->assertInstanceOf(
      User::class,
      $injection->getObjects()['user']
    );

    $injection = $this->parser->injectParameters(
      '{user:Discern\Test\Parameter\User}',
      ['user' => [1]],
      new ParameterCollection()
    );

    $this->assertInstanceOf(
      User::class,
      $injection->getOutput()
    );
  }

  public function testCanInvokeParameterDefinitionWithDefaultParams()
  {
    $user = new User(3);

    $injection = $this->parser->injectParameters('{user:Discern\Test\Parameter\User.id|[3]}');
    
    $this->assertInstanceOf(
      User::class,
      $injection->getObjects()['user']
    );

    $this->assertEquals(
      $user->id,
      $injection->getOutput()
    );
  }

  public function testCanInjectParametersIntoArray()
  {
    $user = new User(1);
    $animal = new Animal(2);

    $subject = [
      'user_name' => '{user.first_name} {user.last_name}',
      'user' => '{user:Discern\Test\Parameter\User}',
      'about' => 'My name is {user.first_name} and I have a pet {animal.type}',
      'animal' => '{animal:Discern\Test\Parameter\Animal}'
    ];

    $injection = $this->parser->arrayInjectParameters(
      $subject,
      ['user' => [1], 'animal' => [2]],
      new ParameterCollection()
    );

    $this->assertEquals(
      sprintf('%s %s', $user->getFirstName(), $user->getLastName()),
      $injection->getOutput()['user_name']
    );

    $this->assertEquals(
      sprintf('My name is %s and I have a pet %s', $user->getFirstName(), $animal->getType()),
      $injection->getOutput()['about']
    );
    
    $this->assertInstanceOf(
      User::class,
      $injection->getOutput()['user']
    );

    $this->assertInstanceOf(
      Animal::class,
      $injection->getOutput()['animal']
    );
  }

  public function testFailsWhenParameterIsDefinedMultipleTimes()
  {
    $user = new User(3);

    $this->expectException(InvalidParameterException::class);

    $injection = $this->parser->injectParameters(
      '{user:Discern\Test\Parameter\User.id|[3]}: {user:Discern\Test\Parameter\User.first_name|[3]}',
      [],
      new ParameterCollection()
    );
  }

  public function testCanRenderNestedParameterOutputMethods()
  {
    $user = new User(3);

    $injection = $this->parser->injectParameters(
      '{user_pet_type:Discern\Test\Parameter\User.pet.type}',
      ['user_pet_type' => $user],
      new ParameterCollection()
    );

    $this->assertEquals(
      'dog',
      $injection->getOutput()
    );
  }

  public function testCanRenderEmptyOptionalParameters()
  {
    $injection = $this->parser->injectParameters(
      'completely blank.{user?:Discern\Test\Parameter\User}',
      [],
      new ParameterCollection()
    );

    $this->assertEquals(
      'completely blank.',
      $injection->getOutput()
    );
  }
}
