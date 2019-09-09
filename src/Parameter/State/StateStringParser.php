<?php namespace Discern\Parameter\State;

use Discern\Parameter\State\Contract\StateStringParserInterface;

class StateStringParser implements StateStringParserInterface {
  public function parseStateString($string)
  {
    //extract text inside parentheses
    $mapping = $this->mapStateExpression($string);
    $mapping_raw = $this->arrayInjectMapping($mapping);
    return $this->initMappings($mapping_raw);
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

      $states = $this->splitByConjunction($expression);

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


        $mapping_parsed[$key][] = array_map(
          'trim',
          preg_split("/(,| and | then )/", $state)
        );
      }
    }

    $mappings_cleaned = $this->cleanMappings($mapping, $mapping_parsed);
    return array_values($mappings_cleaned);
  }

  protected function initMappings($mapping)
  {
    $results = [];
    foreach ($mapping as $key => $value) {
      if (is_array($value)) {
        $results[$key] = $this->initMappings($value);
        continue;
      }
      $positive_id = $this->getPositiveActionId($value);
      $results[$key] = new ActionResultContext($positive_id, [
        'negate' => $positive_id !== $value
      ]);
    }
    return $results;
  }

  private function getPositiveActionId($id)
  {
    $replace = [
      'isn\'t' => 'is ',
      'can\'t' => 'can ',
      'cannot' => 'can ',
      'not'    => ' ',
      'won\'t' => 'will ',
      'doesn\'t' => 'does ',
      'wasn\'t' => 'was ',
    ];

    $pattern = sprintf(
      '/(^|[ ])(%s)\s+/',
      implode('|', array_keys($replace))
    );

    $positive_id = trim(preg_replace_callback($pattern, function($matches) use ($replace) {
      return $replace[$matches[2]];
    }, $id));

    return $positive_id ?: $id;
  }

  private function splitByConjunction($expression)
  {
    $states = array_map('trim', explode(' or ', $expression));
    $i = 0;
    while (isset($states[$i])) {
      if (strpos($states[$i], ',') !== false) {
        $parts = array_map('trim', explode(',', $states[$i]));
        $parts_last = count($parts)-1;
        if (strpos($parts[$parts_last], ' and ') === false && isset($states[$i+1])) {
          array_splice($states, $i, 1, $parts);
          $i += count($parts)-1;
          continue;
        }
      }
      $i++;
    }
    return $states;
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

  private function cleanMappings(array $mapping, array $mapped_values)
  {
    foreach ($mapped_values as $key => $value) {
      if (is_array($value)) {
        $mapped_values[$key] = $this->cleanMappings($mapping, $value);
        continue;
      }

      if (isset($mapping[$value])) {
        unset($mapped_values[$key]);
      }
    }

    return $mapped_values;
  }
}
