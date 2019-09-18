<?php namespace Discern\Test\Parameter\Template;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\StringParser;
use Discern\Parameter\ParameterCollectionFactory;
use Discern\Parameter\ParameterCollection;
use Discern\Parameter\ParameterFactory;
use Discern\Parameter\ParameterChildFactory;
use Discern\Parameter\TypeFactoryCollection;
use Discern\Parameter\TypeFactory;
use Discern\Parameter\InjectionFactory;
use Discern\Parameter\TypeValidator;
use Discern\Parameter\Contract\ParameterInterface;
use Discern\Parameter\Struct\StructFactory;
use Discern\Parameter\Template\Factory;
use Discern\Parameter\Template\ClassTemplate;
use Discern\Test\Parameter\User;
use Discern\Test\Parameter\Animal;
use Discern\Parameter\Object\ObjectAccessor;

class ClassTemplateTest extends TestCase {
  public function __construct()
  {
    $param_collection_factory = new ParameterCollectionFactory();
    $param_factory_collection = new TypeFactoryCollection();
    $param_factory = new TypeFactory();
    $param_config_factory = new ParameterFactory();
    $param_config_child = new ParameterChildFactory();
    $injection_factory = new InjectionFactory();
    $accessor = new ObjectAccessor(true);

    $param_config_factory->setParameterTypeValidator(new TypeValidator());

    $param_factory_collection
      ->add(TypeFactoryCollection::$DEFAULT_FACTORY_ID, $param_factory);

    $parser = new StringParser();
    $parser
      ->setParameterTypeFactoryCollection($param_factory_collection)
      ->setParameterCollectionFactory($param_collection_factory)
      ->setParameterFactory($param_config_factory)
      ->setParameterInjectionFactory($injection_factory)
      ->setObjectAccessor($accessor)
      ->setParameterChildFactory($param_config_child);

    $this->home = new HomeTemplate();
    $this->home
      ->setParameterStructFactory(new StructFactory())
      ->setParameterCollectionFactory($param_collection_factory)
      ->setParameterTypeFactoryCollection($param_factory_collection)
      ->setObjectAccessor($accessor)
      ->setParameterStringParser($parser);

    $blank_template = new ClassTemplate();
    $blank_template
      ->setParameterStructFactory(new StructFactory())
      ->setParameterCollectionFactory($param_collection_factory)
      ->setParameterTypeFactoryCollection($param_factory_collection)
      ->setObjectAccessor($accessor)
      ->setParameterStringParser($parser);

    $this->factory = new Factory();
    $this->factory->setClassTemplate($blank_template);
  }

  public function testCanInitializeTemplatedClass()
  {
    $house = $this->home;

    $home = $house([
      'owner' => [1],
      'pet' => [2]
    ]);

    $this->assertInstanceOf(
      Home::class,
      $home
    );

    $this->assertEquals(
      $home->owner_name,
      'Richard Lee'
    );

    $this->assertEquals(
      $home->pet_name,
      'Rover (dog)'
    );

    $this->assertEquals(
      $home->address,
      '333 25th lane'
    );

    return $home;
  }

  /**
   * @depends testCanInitializeTemplatedClass
   */
  public function testCanRerenderTemplatedClassWithPartialParams($home)
  {
    $new_home = $home->with([
      'owner' => [1, [
        'first_name' => 'Oshane'
      ]]
    ]);

    $this->assertEquals(
      $new_home->owner_name,
      'Oshane Lee'
    );

    // still shouldn't have changed
    $this->assertEquals(
      $home->owner_name,
      'Richard Lee'
    );

    $this->assertEquals(
      $home->pet_name,
      $new_home->pet_name
    );
  }

  public function testCanGenerateClassTemplatesUsingFactory()
  {
    $callback = $this->factory->definition([
      'owner_name' => '{owner:Discern\Test\Parameter\User.first_name} {owner.last_name}',
      'pet_name' => '{pet.name} ({pet:Discern\Test\Parameter\Animal.type})',
      'address' => '{owner.address}'
    ]);

    $return_value = $callback([
      'owner' => [1],
      'pet' => [2]
    ], function($params) {
      $this->assertEquals(
        $params->owner_name,
        'Richard Lee'
      );

      $this->assertEquals(
        $params->pet_name,
        'Rover (dog)'
      );

      $this->assertEquals(
        $params->address,
        '333 25th lane'
      );

      return 1;
    });

    $this->assertEquals(
      $return_value,
      1
    );

    $callback([
      'owner' => [1, ['first_name' => 'Oshane']],
      'pet' => [2, ['name' => 'Meowth', 'type' => 'cat']]
    ], function($params) {
      $this->assertEquals(
        $params->pet_name,
        'Meowth (cat)'
      );

      $this->assertEquals(
        $params->owner_name,
        'Oshane Lee'
      );

      $this->assertInstanceOf(
        User::class,
        $params->params()->owner
      );
    });
  }
}