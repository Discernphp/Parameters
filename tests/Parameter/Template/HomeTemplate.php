<?php namespace Discern\Test\Parameter\Template;

use Discern\Parameter\Template\Contract\ClassTemplateInterface;
use Discern\Parameter\Template\ClassTemplateTrait;

class HomeTemplate implements ClassTemplateInterface {
  use ClassTemplateTrait;

  protected $templated_class = Home::class;

  protected $template_properties = [
    'owner_name',
    'pet_name',
    'address'
  ];

  protected $owner_name = '{owner:Discern\Test\Parameter\User.first_name} {owner.last_name}';

  protected $pet_name = '{pet.name} ({pet:Discern\Test\Parameter\Animal.type})';

  protected $address = '{owner.address}';
}