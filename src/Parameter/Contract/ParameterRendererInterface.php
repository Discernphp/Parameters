<?php namespace Discern\Parameter\Contract;

interface ParameterRendererInterface {
  public function render($instance, $output_method);
}