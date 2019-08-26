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
}