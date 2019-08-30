<?php namespace Discern\Parameter\Template;

use Discern\Parameter\Template\Contract\BlankClassTemplateInterface;

class Factory {
  protected $template;

  public function setClassTemplate(BlankClassTemplateInterface $class_template)
  {
    $this->template = $class_template;
    return $this;
  }

  public function getClassTemplate()
  {
    return $this->template;
  }

  public function definition(array $properties)
  {
    return function(array $arguments, $callback = null) use($properties) {
      if ($callback && !is_callable($callback)) {
        throw new InvalidArgumentException(
          sprintf(
            'definition: %s, requires callback function as second parameter, received `%s`',
            json_encode($arguments),
            gettype($callback)
          )
        );
      }

      $template = $this->getClassTemplate()->with($properties);
      $params = $template($arguments);
      return $callback ? $callback($params) : $params;
    };
  }
}
