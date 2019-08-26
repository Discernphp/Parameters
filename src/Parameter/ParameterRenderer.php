<?php namespace Discern\Parameter;

use Discern\Parameter\Contract\ParameterRendererInterface;

class ParameterRenderer implements ParameterRendererInterface {
  public function render($instance, $output_method)
  {
    if (!get_class($instance) || !$output_method) {
      return $instance;
    }

    $output_methods = explode('.', $output_method);

    $output = '';
    $current_instance = $instance;

    foreach ($output_methods as $output_method) {
      $output_method_camel = str_replace(' ', '', ucwords(str_replace('_', ' ', $output_method)));

      $conversion_method = '__to'.ucfirst($output_method_camel);
      if (method_exists($current_instance, $conversion_method)) {
        $current_instance = $current_instance->{$conversion_method}();
        continue;
      }

      $getter_method = 'get'.ucfirst($output_method_camel);
      if (method_exists($current_instance, $getter_method)) {
        $current_instance = $current_instance->{$getter_method}();
        continue;
      }

      if (array_key_exists($output_method, get_class_vars(get_class($instance)))) {
        $current_instance = $current_instance->{$output_method};
        continue;
      }

      throw new \InvalidArgumentException(
        sprintf(
          'Could not render `%s.%s`',
          get_class($instance),
          $output_method
        )
      );
    }

    $output = $current_instance;
    return $output;
  }
}
