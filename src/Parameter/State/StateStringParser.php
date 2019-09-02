<?php namespace Discern\Parameter\State;

use Discern\Parameter\State\Contract\StateStringParserInterface;

class StateStringParser implements StateStringParserInterface {
  public function parseStateString($string)
  {
    //extract text inside parentheses
    $mapping = $this->mapStateExpression($string);
    return $this->arrayInjectMapping($mapping);
  }

  private function arrayInjectMapping($mapping)
  {
    $mapping_parsed = [];
    foreach ($mapping as $key => $expression) {
      if (is_array($expression)) {
        if (!isset($mapping_parsed[$key])) {
          $mapping_parsed[$key] = [];
        }
        $mapping_parsed[$key] = $this->arrayInjectMapping($expression);
        continue;
      }

      $states = array_map('trim', explode(' or ', $expression));
      foreach ($states as $state) {
        if (isset($mapping_parsed[$state])) {
          $mapping_parsed[$key] = array_merge(
            isset($mapping_parsed[$key]) ? $mapping_parsed[$key] : [],
            $mapping_parsed[$state]
          );
          unset($mapping_parsed[$state]);
          continue;
        }

        if (!isset($mapping_parsed[$key])) {
          $mapping_parsed[$key] = [];
        }

        $mapping_parsed[$key][] = array_map('trim', explode(' and ', $state));
      }
    }
    return array_values($mapping_parsed)[0];
  }

  private function mapStateExpression($expression)
  {
    $matches = [];
    preg_match_all('/\((((?>[^()]+)|(?R))*)\)/', $expression, $matches);
    $state_string_parsed = $expression;
    $expression_array = $matches[1];
    $mapping = [];

    for ($i = 0; isset($expression_array[$i]); $i++) {
      $map_key = uniqid();
      $mapping[$map_key] = $expression_array[$i];
      $state_string_parsed = str_replace($matches[0][$i], $map_key, $state_string_parsed);

      if (strpos($expression_array[$i], '(') !== false) {
        $mapping[$map_key] = $this->mapStateExpression($expression_array[$i]);
      }
    }

    $mapping[uniqid()] = $state_string_parsed;
    return $mapping;
  }
}
