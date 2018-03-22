<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class factory {

  static protected $cache;

  #########################
  ### classes functions ###
  #########################

  static function autoload($name) {
    console::add_log('autoload', 'search', $name, 'ok');
    foreach (static::get_classes_map() as $c_class_name => $c_class_info) {
      if ($c_class_name == $name) {
        $c_file = new file($c_class_info->file);
        $c_file->insert();
      }
    }
  }

  static function get_classes_map() {
    $cache = cache::select('classes_map');
    if ($cache) {
      return $cache;
    } else {
      $classes_map = [];
      $files = file::select_all_recursive(dir_system, '%^.*\.php$%');
      foreach ($files as $c_file) {
        $c_matches = [];
        preg_match_all('%namespace (?<namespace>[a-z0-9_\\\\]+)\\s*[{;]\\s*'.
                                  '(?<dependencies>.*?|)'.
                                  '(?<is_abstract>abstract|)\\s*'.
                                  '(?<type>class|trait|interface)\\s+'.
                                  '(?<name>[a-z0-9_]+)\\s*'.
                       '(?:extends (?<extends>[a-z0-9_\\\\]+)|)\\s*'.
                    '(?:implements (?<implements>[a-z0-9_,\\s\\\\]+)|)\\s*{%isS', $c_file->load(), $c_matches, PREG_SET_ORDER);
        foreach ($c_matches as $c_match) {
          if (!empty($c_match['namespace']) &&
              !empty($c_match['name'])) {
            $c_info = new \stdClass();
            $c_info->type      = $c_match['type'];
            $c_info->namespace = $c_match['namespace'];
            $c_info->name      = $c_match['name'];
            if (!empty($c_match['extends']))    $c_info->extends    = trim($c_match['extends']);
            if (!empty($c_match['implements'])) $c_info->implements = trim($c_match['implements']);
            $c_info->file = $c_file->get_path_relative();
            $classes_map[$c_match['namespace'].'\\'.
                         $c_match['name']] = $c_info;
          }
        }
      }

      cache::update('classes_map', $classes_map, ['build' => static::datetime_get()]);
      return $classes_map;
    }
  }

  static function class_get_parts($class_name) {
    return explode('\\', $class_name);
  }

  static function class_handler_get_part($handler, $partname) {
    $parts = explode('::', $handler);
    if ($partname == 'classname') return !empty($parts[0]) ? $parts[0] : null;
    if ($partname == 'method')    return !empty($parts[1]) ? $parts[1] : null;
  }

  static function class_is_local($class_name) {
    $parts = static::class_get_parts($class_name);
    return $parts[0] === __NAMESPACE__;
  }

  static function class_get_short_name($class_name) {
    $parts = static::class_get_parts($class_name);
    return end($parts);
  }

  static function class_get_new_instance($class_name, $args = [], $use_constructor = false) {
    $reflection = new \ReflectionClass($class_name);
    return $use_constructor ? $reflection->newInstanceArgs($args) :
                              $reflection->newInstanceWithoutConstructor();
  }

  ######################
  ### data functions ###
  ######################

  static function string_to_data($string) {
  # ─────────────────────────────────────────────────────────────────────
  # hexadecimal notation is not allowed (example: '0x123')
  # octal notation is not allowed (example: '0123')
  # binary notation is not allowed (example: '0b101')
  # ─────────────────────────────────────────────────────────────────────
    if (is_numeric($string)) return $string += 0;
    if ($string === 'true')  return true;
    if ($string === 'false') return false;
    if ($string === 'null')  return null;
    return $string;
  }

  static function data_to_string($data) {
    switch (gettype($data)) {
      case 'string' : return '\''.str_replace('\"', '"', addslashes($data)).'\'';
      case 'boolean': return $data ? 'true' : 'false';
      case 'NULL'   : return 'null';
      default       : return (string)$data;
    }
  }

  static function data_to_attr($data, $join_part = ' ', $key_wrapper = '', $value_wrapper = '"') {
    $return = [];
    foreach ((array)$data as $c_name => $c_value) {
      switch (gettype($c_value)) {
        case 'boolean': $return[] = $key_wrapper.$c_name.$key_wrapper; break;
        case 'array'  : $return[] = $key_wrapper.$c_name.$key_wrapper.'='.$value_wrapper.implode(' ', $c_value).$value_wrapper; break;
        case 'object' : $return[] = $key_wrapper.$c_name.$key_wrapper.'='.$value_wrapper.(method_exists($c_value, 'render') ? $c_value->render() : '').$value_wrapper; break;
        default       : $return[] = $key_wrapper.$c_name.$key_wrapper.'='.$value_wrapper.$c_value.$value_wrapper; break;
      }
    }
    return $join_part ? implode($join_part, $return) :
                                            $return;
  }

  static function data_export($data, $prefix = '') {
    $return = '';
    switch (gettype($data)) {
      case 'array':
        if (count($data)) {
          foreach ($data as $c_key => $c_value) {
            $return.= static::data_export($c_value, $prefix.(is_int($c_key) ? '['.$c_key.']' : '[\''.$c_key.'\']'));
          }
        } else {
          $return.= $prefix.' = [];'.nl;
        }
        break;
      case 'object':
        $c_class_name = get_class($data);
        $c_reflection = new \ReflectionClass($c_class_name);
        $c_defs                = $c_reflection->getDefaultProperties();
        $c_is_post_constructor = $c_reflection->implementsInterface('\\effcore\\post_constructor');
        $c_is_post_init        = $c_reflection->implementsInterface('\\effcore\\post_init');
        if ($c_is_post_constructor) $return = $prefix.' = factory::class_get_new_instance(\''.addslashes('\\'.$c_class_name).'\');'.nl;
        else                        $return = $prefix.' = new \\'.$c_class_name.'();'.nl;
        foreach ($data as $c_prop => $c_value) {
          if (array_key_exists($c_prop, $c_defs) && $c_defs[$c_prop] === $c_value) continue;
          $return.= static::data_export($c_value, $prefix.'->'.$c_prop);
        }
        if ($c_is_post_constructor) $return.= $prefix.'->__construct();'.nl;
        if ($c_is_post_init)        $return.= $prefix.'->__post_init();'.nl;
        break;
      default: $return.= $prefix.' = '.static::data_to_string($data).';'.nl;
    }
    return $return;
  }

  #######################
  ### array functions ###
  #######################

  static function array_rotate($data) {
    $return = [];
    foreach ($data as $c_row) {                  # convert: |1|2| to: |1|3|
      for ($i = 0; $i < count($c_row); $i++) {   #          |3|4|     |2|4|
        $return[$i][] = $c_row[$i];
      }
    }
    return $return;
  }

  static function array_sort_by_weight(&$array) {
  # prepare weight for stable sorting
    $c_weight = 0;
    foreach ($array as $c_item) {
      $c_item->weight = $c_item->weight ?: $c_weight += .0002;
    }
  # sorting
    uasort($array, function($a, $b){
      return $a->weight == $b->weight ? 0 : ($a->weight < $b->weight ? -1 : 1);
    });
    return $array;
  }

  static function array_values_map_to_keys($array) {
    return array_combine($array, $array);
  }

  static function array_deep_clone($array) {
    return unserialize(serialize($array));
  }

  static function array_values_recursive(&$array, $all = false, $dpath = '') {
    $return = [];
    foreach ($array as $c_key => &$c_value) {
      $c_dpath = $dpath ? $dpath.'/'.$c_key : $c_key;
      if (is_array($c_value)) $return += static::array_values_recursive($c_value, $all, $c_dpath);
      if (is_array($c_value) == false || $all) $return[$c_dpath] = &$c_value;
    }
    return $return;
  }

  static function in_array_string_compare($value, $array) {
    foreach ($array as $c_item) {
      if ((string)$c_item === (string)$value) {
        return true;
      }
    }
  }

  #####################################
  ### mix of array/object functions ###
  #####################################

  static function &arrobj_select_value(&$data, $name) {
    if (gettype($data) == 'array')  return $data  [$name];
    if (gettype($data) == 'object') return $data->{$name};
  }

  static function arrobj_insert_value(&$data, $name, $value) {
    if (gettype($data) == 'array')  $data  [$name] = $value;
    if (gettype($data) == 'object') $data->{$name} = $value;
  }

  static function arrobj_delete_child(&$data, $name) {
    if (gettype($data) == 'array')  unset($data  [$name]);
    if (gettype($data) == 'object') unset($data->{$name});
  }

  static function arrobj_values_recursive(&$data, $all = false, $dpath = '') {
    $return = [];
    foreach ($data as $c_key => &$c_value) {
      $c_dpath = $dpath ? $dpath.'/'.$c_key : $c_key;
      if ((is_array($c_value) || is_object($c_value))) $return += static::arrobj_values_recursive($c_value, $all, $c_dpath);
      if ((is_array($c_value) || is_object($c_value)) == false || $all) $return[$c_dpath] = &$c_value;
    }
    return $return;
  }

  #######################
  ### dpath functions ###
  #######################

  static function dpath_get_chain(&$data, $dpath) {
    $chain = [];
    $c_pointer = $data;
    foreach (explode('/', $dpath) as $c_part) {
      $c_pointer = &static::arrobj_select_value($c_pointer, $c_part);
      $chain[$c_part] = &$c_pointer;
    }
    return $chain;
  }

  ###########################
  ### date/time functions ###
  ###########################

  # see: locale::format_time(...);
  # see: locale::format_date(...);
  # see: locale::format_datetime(...);

  static function datetime_get($offset = '') {
    $datetime = new \DateTime('now', new \DateTimeZone('UTC'));
    if ($offset) $datetime->modify($offset);
    return $datetime->format('Y-m-d H:i:s');
  }

  ###############
  ### filters ###
  ###############

  static function filter_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  static function filter_url($value) {
    return filter_var($value, FILTER_SANITIZE_URL);
  }

  static function filter_mime_type($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-z]{1,20}/[a-z0-9\-\+\.]{1,100}$%i']]);
  }

  static function filter_hash($value, $lenght = 32) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[0-9a-f]{'.$lenght.'}$%']]); # 32 - md5 | 40 - sha1 | ...
  }

  static function filter_file_name($value) {
    $return = preg_replace_callback('%(?<char>[^a-z0-9_.\-])%uiS', function($m) {
      if ($m['char'] == ' ') return '-';
      if (strlen($m['char']) == 1) return dechex(ord($m['char'][0]));
      if (strlen($m['char']) == 2) return dechex(ord($m['char'][0])).dechex(ord($m['char'][1]));
      if (strlen($m['char']) == 3) return dechex(ord($m['char'][0])).dechex(ord($m['char'][1])).dechex(ord($m['char'][2]));
      return '-';
    }, $value);
    return substr($return, strlen($return) - 255);
  }

  ######################
  ### hash functions ###
  ######################

  static function hash_password_get($data) {
    return sha1($data);
  }

  ##############################
  ### bytes/human conversion ###
  ##############################

  static function is_human_bytes($number) {
    $character = substr($number, -1);
    return in_array($character, ['B', 'K', 'M', 'G', 'T']);
  }

  static function bytes_to_human($bytes) {
    if ($bytes && fmod($bytes, 1024 ** 4) == 0) return ($bytes / 1024 ** 4).'T';
    if ($bytes && fmod($bytes, 1024 ** 3) == 0) return ($bytes / 1024 ** 3).'G';
    if ($bytes && fmod($bytes, 1024 ** 2) == 0) return ($bytes / 1024 ** 2).'M';
    if ($bytes && fmod($bytes, 1024 ** 1) == 0) return ($bytes / 1024 ** 1).'K';
    else return $bytes.'B';
  }

  static function human_to_bytes($human) {
    $powers = array_flip(['B', 'K', 'M', 'G', 'T']);
    $character = strtoupper(substr($human, -1));
    $value = (int)substr($human, 0, -1);
    return $value * 1024 ** $powers[$character];
  }

  ####################
  ### ip functions ###
  ####################

  static function ip_to_hex($ip) {
    $ip_parts_int = explode('.', $ip);
    return str_pad(dechex($ip_parts_int[0]), 2, '0', STR_PAD_LEFT).
           str_pad(dechex($ip_parts_int[1]), 2, '0', STR_PAD_LEFT).
           str_pad(dechex($ip_parts_int[2]), 2, '0', STR_PAD_LEFT).
           str_pad(dechex($ip_parts_int[3]), 2, '0', STR_PAD_LEFT);
  }

  static function hex_to_ip($ip_hex) {
    $ip_parts_hex = str_split($ip_hex, 2);
    return hexdec($ip_parts_hex[0]).'.'.
           hexdec($ip_parts_hex[1]).'.'.
           hexdec($ip_parts_hex[2]).'.'.
           hexdec($ip_parts_hex[3]);
  }


  ############################
  ### signatures functions ###
  ############################

  static function signature_get($string, $length = 40) {
    $key = storage::get('files')->select('settings/core/key');
    return substr(sha1($string.$key), 0, $length);
  }

  ########################
  ### binary functions ###
  ########################

  static function binstr_to_hexstr($binstr) {
    $hexstr = '';
    foreach (str_split($binstr, 8) as $c_chunk) {
      $hexstr.= str_pad(base_convert(str_pad($c_chunk, 8, '0'), 2, 16), 2, '0', STR_PAD_LEFT);
    }
    return $hexstr;
  }

  static function hexstr_to_binstr($hexstr) {
    $binstr = '';
    foreach (str_split($hexstr, 2) as $c_chunk) {
      $binstr.= str_pad(base_convert($c_chunk, 16, 2), 8, '0', STR_PAD_LEFT);
    }
    return $binstr;
  }

  ##########################
  ### server information ###
  ##########################

  static function server_full_name_get() {
    $matches = [];
    preg_match('%^(?<full_name>(?<name>[a-z0-9-]+)/(?<version>[a-z0-9.]+))|'.
                 '(?<full_name_unknown>.*)%i', $_SERVER['SERVER_SOFTWARE'], $matches);
    return !empty($matches['full_name']) ?
                  $matches['name'].' '.
                  $matches['version'] :
                  $matches['full_name_unknown'];
  }

  ########################
  ### shared functions ###
  ########################

  static function format_number($number, $precision = 0, $dec_point = '.', $thousands = '', $no_zeros = true) {
    $precision = $precision ? $precision + 5 : 0; # disable the rounding effect
    $return = $precision ? substr(
      number_format($number, $precision, $dec_point, $thousands), 0, -5) :
      number_format($number, $precision, $dec_point, $thousands);
    if ($no_zeros) {
      $return = rtrim($return, '0');
      $return = rtrim($return, $dec_point);
    }
    return $return;
  }

  static function send_header_and_exit($type, $title = '', $message = '', $p = '') {
    switch ($type) {
      case 'redirect'      : header('Location: '.$p);          break;
      case 'page_refresh'  : header('Refresh: ' .$p);          break;
      case 'access_denided': header('HTTP/1.1 403 Forbidden'); break;
      case 'not_found'     : header('HTTP/1.0 404 Not Found'); break;
    }
    $front_page_link = translation::get('go to <a href="/">front page</a>');
    if ($type == 'access_denided') {print (new template('page_access_denided', ['message' => $message ?: $front_page_link, 'title' => translation::get('Access denided')]))->render(); exit();}
    if ($type == 'not_found')      {print (new template('page_not_found',      ['message' => $message ?: $front_page_link, 'title' => translation::get('Page not found')]))->render(); exit();}
    if ($message)                  {print (new template('page_simple',         ['message' => $message ?: $front_page_link, 'title' => translation::get($title)]))->render();           exit();}
    exit();
  }

  static function to_css_class($string) {
    return str_replace(['/', ' '], '-', strtolower($string));
  }

}}