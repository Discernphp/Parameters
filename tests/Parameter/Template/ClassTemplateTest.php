<?php namespace Discern\Test\Parameter\Template;

use PHPUnit\Framework\TestCase;
use Discern\Parameter\ParameterStringParser;
use Discern\Parameter\ParameterConfigCollectionFactory;
use Discern\Parameter\ParameterConfigCollection;
use Discern\Parameter\ParameterConfigFactory;
use Discern\Parameter\ParameterConfigChildFactory;
use Discern\Parameter\ParameterFactoryCollection;
use Discern\Parameter\ParameterFactory;
use Discern\Parameter\ParameterInjectionFactory;
use Discern\Parameter\ParameterRenderer;
use Discern\Parameter\Contract\ParameterConfigInterface;
use Discern\Parameter\Struct\ParameterStructFactory;
use Discern\Parameter\Template\Factory;
use Discern\Parameter\Template\ClassTemplate;
use Discern\Test\Parameter\User;
use Discern\Test\Parameter\Animal;

class ClassTemplateTest extends TestCase {
  public function __construct()
  {
    $param_collection_factory = new ParameterConfigCollectionFactory();
    $param_factory_collection = new ParameterFactoryCollection();
    $param_factory = new ParameterFactory();
    $param_config_factory = new ParameterConfigFactory();
    $param_config_child = new ParameterConfigChildFactory();
    $injection_factory = new ParameterInjectionFactory();
    $renderer = new ParameterRenderer();

    $param_factory_collection
      ->add(ParameterFactoryCollection::$DEFAULT_FACTORY_ID, $param_factory);

    $parser = new ParameterStringParser();
    $parser
      ->setParameterFactoryCollection($param_factory_collection)
      ->setParameterConfigCollectionFactory($param_collection_factory)
      ->setParameterConfigFactory($param_config_factory)
      ->setParameterInjectionFactory($injection_factory)
      ->setParameterRenderer($renderer)
      ->setParameterConfigChildFactory($param_config_child);

    $this->home = new HomeTemplate();
    $this->home
      ->setParameterStructFactory(new ParameterStructFactory())
      ->setParameterConfigCollectionFactory($param_collection_factory)
      ->setParameterFactoryCollection($param_factory_collection)
      ->setParser($parser);

    $blank_template = new ClassTemplate();
    $blank_template
      ->setParameterStructFactory(new ParameterStructFactory())
      ->setParameterConfigCollectionFactory($param_collection_factory)
      ->setParameterFactoryCollection($param_factory_collection)
      ->setParser($parser);

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