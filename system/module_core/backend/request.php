<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class request {

  # conversion matrix:
  # ┌──────────────────────────────────────────╥────────────────┐
  # │ input value (undefined | string | array) ║ result value   │
  # ╞══════════════════════════════════════════╬════════════════╡
  # │ source[field] === undefined              ║ return ''      │
  # │ source[field] === ''                     ║ return ''      │
  # │ source[field] === 'value'                ║ return 'value' │
  # ├──────────────────────────────────────────╫────────────────┤
  # │ source[field] === [0 => '']              ║ return ''      │
  # │ source[field] === [0 => '', …]           ║ return ''      │
  # │ source[field] === [0 => 'value']         ║ return 'value' │
  # │ source[field] === [0 => 'value', …]      ║ return 'value' │
  # └──────────────────────────────────────────╨────────────────┘

  static function value_get($name, $number = 0, $source = '_POST') {
    global ${$source};
    return !isset(${$source}[$name]) ? '' :
       (is_string(${$source}[$name]) ? ${$source}[$name] :
        (is_array(${$source}[$name]) &&
            isset(${$source}[$name][$number]) ?
                  ${$source}[$name][$number] : ''));
  }


  # conversion matrix:
  # ┌──────────────────────────────────────────╥──────────────────────────┐
  # │ input value (undefined | string | array) ║ result value             │
  # ╞══════════════════════════════════════════╬══════════════════════════╡
  # │ source[field] === undefined              ║ return []                │
  # │ source[field] === ''                     ║ return [0 => '']         │
  # │ source[field] === 'value'                ║ return [0 => 'value']    │
  # ├──────────────────────────────────────────╫──────────────────────────┤
  # │ source[field] === [0 => '']              ║ return [0 => '']         │
  # │ source[field] === [0 => '', …]           ║ return [0 => '', …]      │
  # │ source[field] === [0 => 'value']         ║ return [0 => 'value']    │
  # │ source[field] === [0 => 'value', …]      ║ return [0 => 'value', …] │
  # └──────────────────────────────────────────╨──────────────────────────┘

  static function values_get($name, $source = '_POST') {
    global ${$source};
    return !isset(${$source}[$name]) ? [] :
       (is_string(${$source}[$name]) ? [${$source}[$name]] :
        (is_array(${$source}[$name]) ?
                  ${$source}[$name] : []));
  }

  static function values_set($name, $values, $source = '_POST') {
    global ${$source};
    ${$source}[$name] = $values;
  }

  static function values_reset() {
    $_POST    = [];
    $_GET     = [];
    $_REQUEST = [];
    $_FILES   = [];
  }

  # $is_files === false
  # ─────────────────────────────────────────────────────────────────────
  #   (string)key => (string)value
  # ─────────────────────────────────────────────────────────────────────
  #   (string)key => [
  #     (int)0 => (string)value,
  #     (int)1 => (string)value …
  #     (int)N => (string)value
  #   ]

  # $is_files !== false
  # ─────────────────────────────────────────────────────────────────────
  #   (string)key => [
  #     (string)key => (string)|(int)value
  #   ]
  # ─────────────────────────────────────────────────────────────────────
  #   (string)key => [
  #     (string)key => [
  #       (int)0 => (string)|(int)value,
  #       (int)1 => (string)|(int)value …
  #       (int)N => (string)|(int)value
  #     ]
  #   ]

  static function values_sanitize($source = '_POST', $is_files = false) {
    $result = [];
    global ${$source};
    if (is_array(${$source}) && count(${$source})) {
      $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator(${$source}));
      foreach ($iterator as $c_value) {
        $c_depth = $iterator->getDepth();
        $c_k0 = $iterator->getSubIterator(0) ? $iterator->getSubIterator(0)->key() : null;
        $c_k1 = $iterator->getSubIterator(1) ? $iterator->getSubIterator(1)->key() : null;
        $c_k2 = $iterator->getSubIterator(2) ? $iterator->getSubIterator(2)->key() : null;
        if ($is_files !== true && $c_depth === 0 && is_string($c_k0) &&                                      is_string($c_value)) $result[$c_k0]          = $c_value;
        if ($is_files !== true && $c_depth === 1 && is_string($c_k0) &&    is_int($c_k1) &&                  is_string($c_value)) $result[$c_k0][]        = $c_value;
        if ($is_files === true && $c_depth === 1 && is_string($c_k0) && is_string($c_k1) &&                  is_string($c_value)) $result[$c_k0][$c_k1]   = $c_value;
        if ($is_files === true && $c_depth === 1 && is_string($c_k0) && is_string($c_k1) &&                     is_int($c_value)) $result[$c_k0][$c_k1]   = $c_value;
        if ($is_files === true && $c_depth === 2 && is_string($c_k0) && is_string($c_k1) && is_int($c_k2) &&    is_int($c_value)) $result[$c_k0][$c_k1][] = $c_value;
        if ($is_files === true && $c_depth === 2 && is_string($c_k0) && is_string($c_k1) && is_int($c_k2) && is_string($c_value)) $result[$c_k0][$c_k1][] = $c_value;
      }
    }
    return $result;
  }

  # conversion matrix:
  # ┌──────────────────────────────────────────────────────────╥───────────────────────────────────────────────────────────────────────┐
  # │ input value (undefined | array)                          ║ result value                                                          │
  # ╞══════════════════════════════════════════════════════════╬═══════════════════════════════════════════════════════════════════════╡
  # │ $_FILES[field] === undefined                             ║ return []                                                             │
  # │ $_FILES[field] === [error = 4]                           ║ return []                                                             │
  # │ $_FILES[field] === [name = 'file']                       ║ return [0 => (object)[name = 'file']]                                 │
  # │ $_FILES[field] === [name = [0 => 'file']]                ║ return [0 => (object)[name = 'file']]                                 │
  # │ $_FILES[field] === [name = [0 => 'file1', 1 => 'file2']] ║ return [0 => (object)[name = 'file1'], 1 => (object)[name = 'file2']] │
  # └──────────────────────────────────────────────────────────╨───────────────────────────────────────────────────────────────────────┘

  static function files_get($name) {
    $result = [];
    if (isset($_FILES[$name]['name'    ]) &&
        isset($_FILES[$name]['type'    ]) &&
        isset($_FILES[$name]['size'    ]) &&
        isset($_FILES[$name]['tmp_name']) &&
        isset($_FILES[$name]['error'   ])) {
      $info = $_FILES[$name];
      if (!is_array($info['name'    ])) $info['name'    ] = [$info['name'    ]];
      if (!is_array($info['type'    ])) $info['type'    ] = [$info['type'    ]];
      if (!is_array($info['size'    ])) $info['size'    ] = [$info['size'    ]];
      if (!is_array($info['tmp_name'])) $info['tmp_name'] = [$info['tmp_name']];
      if (!is_array($info['error'   ])) $info['error'   ] = [$info['error'   ]];
      foreach ($info['name'] as $c_number => $c_name) {
        $c_type     = $info['type'    ][$c_number];
        $c_size     = $info['size'    ][$c_number];
        $c_tmp_name = $info['tmp_name'][$c_number];
        $c_error    = $info['error'   ][$c_number];
        if ($c_error !== UPLOAD_ERR_NO_FILE) {
          $result[$c_number] = new file_history;
          $result[$c_number]->init_from_tmp(
            $c_name,
            $c_type,
            $c_size,
            $c_tmp_name,
            $c_error
          );
        }
      }
    }
    return $result;
  }

}}