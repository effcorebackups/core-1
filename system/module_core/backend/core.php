<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class core {

  static protected $cache;

  #################################
  ### functionality for classes ###
  #################################

  static function autoload($name) {
    console::log_add('autoload', 'search', $name, 'ok');
    $name = strtolower($name);
    if (isset(static::structures_map_get()[$name])) {
      $c_item_info = static::structures_map_get()[$name];
      $c_file = new file($c_item_info->file);
      $c_file->insert();
    }
  }

  static function structures_map_get() {
    $cache = cache::select('structures');
    if ($cache) {
      return $cache;
    } else {
      $result = [];
      $php_files = file::select_recursive(dir_system,  '%^.*\\.php$%') +
                   file::select_recursive(dir_modules, '%^.*/modules/.*/.*\\.php$%');
      foreach ($php_files as $c_file) {
        $c_matches = [];
        preg_match_all('%(?:namespace (?<namespace>[a-z0-9_\\\\]+)\\s*[{;]\\s*(?<dependencies>.*?|)|)\\s*'.
                                     '(?<modifier>abstract|final|)\\s*'.
                                     '(?<type>class|trait|interface)\\s+'.
                                     '(?<name>[a-z0-9_]+)\\s*'.
                          '(?:extends (?<extends>[a-z0-9_\\\\]+)|)\\s*'.
                       '(?:implements (?<implements>[a-z0-9_,\\s\\\\]+)|)\\s*{%isS', $c_file->load(), $c_matches, PREG_SET_ORDER);
        foreach ($c_matches as $c_match) {
          if (!empty($c_match['name'])) {
            $c_item = new \stdClass;
          # define modifier (abstract|final)
            if (!empty($c_match['modifier'])) {
              $c_item->modifier = $c_match['modifier'];
            }
          # define namespace, name, type = class|trait|interface
            $c_item->namespace = !empty($c_match['namespace']) ? $c_match['namespace'] : '';
            $c_item->name = $c_match['name'];
            $c_item->type = $c_match['type'];
          # define parent class
            if (!empty($c_match['extends'])) {
              if ($c_match['extends'][0] == '\\')
                   $c_item->extends = ltrim($c_match['extends'], '\\');
              else $c_item->extends = ltrim($c_item->namespace.'\\'.$c_match['extends'], '\\');
            }
          # define implements interfaces
            if (!empty($c_match['implements'])) {
              foreach (explode(',', $c_match['implements']) as $c_implement) {
                $c_implement = trim($c_implement);
                if ($c_implement[0] == '\\')
                     $c_implement = ltrim($c_implement, '\\');
                else $c_implement = ltrim($c_item->namespace.'\\'.$c_implement, '\\');
                $c_item->implements[$c_implement] = $c_implement;
              }
            }
          # define file path
            $c_item->file = $c_file->path_relative_get();
          # add to result pool
            if (!$c_item->namespace)
                 $result[strtolower($c_item->name)] = $c_item;
            else $result[strtolower($c_item->namespace.'\\'.$c_item->name)] = $c_item;
          }
        }
      }
      ksort($result);
      cache::update('structures', $result, '', ['build_date' => static::datetime_get()]);
      return $result;
    }
  }

  static function class_parts_get($class_name) {
    return explode('\\', $class_name);
  }

  static function class_handler_part_get($handler, $partname) {
    $parts = explode('::', $handler);
    if ($partname == 'classname') return !empty($parts[0]) ? $parts[0] : null;
    if ($partname == 'method')    return !empty($parts[1]) ? $parts[1] : null;
  }

  static function class_is_local($class_name) {
    $parts = static::class_parts_get($class_name);
    return $parts[0] === __NAMESPACE__;
  }

  static function class_name_short_get($class_name) {
    $parts = static::class_parts_get($class_name);
    return end($parts);
  }

  static function class_instance_new_get($class_name, $args = [], $use_constructor = false) {
    $reflection = new \ReflectionClass($class_name);
    return $use_constructor ? $reflection->newInstanceArgs($args) :
                              $reflection->newInstanceWithoutConstructor();
  }

  ##############################
  ### functionality for data ###
  ##############################

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
      case 'string' : return '\''.addcslashes($data, '\'\\').'\'';
      case 'boolean': return $data ? 'true' : 'false';
      case 'NULL'   : return 'null';
      case 'object' :
      case 'array'  :
        $expressions = [];
        foreach($data as $c_key => $c_value) {
          $expressions[] = static::data_to_string($c_key).' => '.
                           static::data_to_string($c_value);
        }
        return gettype($data) === 'object' ?
          '(object)['.implode(', ', $expressions).']' :
                  '['.implode(', ', $expressions).']';
      default: return (string)$data;
    }
  }

  static function data_to_attr($data, $join_part = ' ', $key_wrapper = '', $value_wrapper = '"') {
    $result = [];
    foreach ((array)$data as $c_name => $c_value) {
      switch (gettype($c_value)) {
        case 'boolean': $result[] = $key_wrapper.$c_name.$key_wrapper; break;
        case 'array'  : $result[] = $key_wrapper.$c_name.$key_wrapper.'='.$value_wrapper.implode(' ', $c_value).$value_wrapper; break;
        case 'object' : $result[] = $key_wrapper.$c_name.$key_wrapper.'='.$value_wrapper.(method_exists($c_value, 'render') ? $c_value->render() : '').$value_wrapper; break;
        default       : $result[] = $key_wrapper.$c_name.$key_wrapper.'='.$value_wrapper.$c_value.$value_wrapper; break;
      }
    }
    return $join_part ? implode($join_part, $result) :
                                            $result;
  }

  static function data_to_codeline($data, $prefix = '') {
    $result = '';
    switch (gettype($data)) {
      case 'array':
        if (count($data)) {
          foreach ($data as $c_key => $c_value) {
            $result.= static::data_to_codeline($c_value, $prefix.(is_int($c_key) ?
                                                                     '['.$c_key.']' :
                                                       '[\''.addcslashes($c_key, '\'\\').'\']'));
          }
        } else {
          $result.= $prefix.' = [];'.nl;
        }
        break;
      case 'object':
        $c_class_name = get_class($data);
        $c_reflection = new \ReflectionClass($c_class_name);
        $c_defs                = $c_reflection->getDefaultProperties();
        $c_is_post_constructor = $c_reflection->implementsInterface('\\effcore\\has_post_constructor');
        $c_is_postinit         = $c_reflection->implementsInterface('\\effcore\\has_postinit');
        if ($c_is_post_constructor)
             $result = $prefix.' = core::class_instance_new_get(\''.addslashes('\\'.$c_class_name).'\');'.nl;
        else $result = $prefix.' = new \\'.$c_class_name.'();'.nl;
        foreach ($data as $c_prop => $c_value) {
          if (array_key_exists($c_prop, $c_defs) && $c_defs[$c_prop] === $c_value) continue;
          $result.= static::data_to_codeline($c_value, $prefix.'->'.$c_prop);
        }
        if ($c_is_post_constructor) $result.= $prefix.'->__construct();'.nl;
        if ($c_is_postinit)         $result.= $prefix.  '->_postinit();'.nl;
        break;
      default:
        $result.= $prefix.' = '.static::data_to_string($data).';'.nl;
    }
    return $result;
  }

  ################################
  ### functionality for arrays ###
  ################################

  static function array_rotate($data) {
    $result = [];
    foreach ($data as $c_row) {                  # convert │1│2│ to │1│3│
      for ($i = 0; $i < count($c_row); $i++) {   #         │3│4│    │2│4│
        $result[$i][] = $c_row[$i];
      }
    }
    return $result;
  }

  # ┌───────────────────────────────────┐
  # │ weight scale by element direction │
  # ╞═══════════════════════════════════╡
  # │                 ▲ +100            │
  # │                 │                 │
  # │                 │                 │
  # │ ◀───────────────┼──────────────── │
  # │ +100            │ 0          -100 │
  # │                 │                 │
  # │                 │ -100            │
  # └───────────────────────────────────┘

  static function array_sort_by_weight(&$array) {
  # note:
  # ═════════════════════════════════════════════════════════════════════════
  # if two members compare as equal,
  # their relative order in the sorted array will be undefined.
  # we should preprocess items with weight = 0 before sorting
  # ─────────────────────────────────────────────────────────────────────────
    $c_weight = 0;
    foreach ($array as $c_item)
      if ($c_item->weight === 0)
          $c_item->weight = $c_weight -= .0001;
    return static::array_sort_by_property($array, 'weight', 'a');
  }

  static function array_sort_by_property(&$array, $property, $order = 'd') {
    uasort($array, function($a, $b) use ($property, $order) {
      if ($order == 'a') return $b->{$property} <=> $a->{$property};
      if ($order == 'd') return $a->{$property} <=> $b->{$property};
    });
    return $array;
  }

  static function array_kmap($array) {
    return array_combine($array, $array);
  }

  static function array_clone_deep($array) {
    return unserialize(serialize($array));
  }

  static function array_values_select_recursive(&$array, $all = false, $dpath = '') {
    $result = [];
    foreach ($array as $c_key => &$c_value) {
      $c_dpath = $dpath ? $dpath.'/'.$c_key : $c_key;
      if (is_array($c_value)) $result += static::array_values_select_recursive($c_value, $all, $c_dpath);
      if (is_array($c_value) == false || $all) $result[$c_dpath] = &$c_value;
    }
    return $result;
  }

  static function in_array_string_compare($value, $array) {
    foreach ($array as $c_item) {
      if ((string)$c_item === (string)$value) {
        return true;
      }
    }
  }

  #############################################
  ### functionality for mix of array|object ###
  #############################################

  static function &arrobj_value_select(&$data, $name) {
    if (is_array ($data)) return $data  [$name];
    if (is_object($data)) return $data->{$name};
  }

  static function arrobj_value_insert(&$data, $name, $value) {
    if (is_array ($data)) $data  [$name] = $value;
    if (is_object($data)) $data->{$name} = $value;
  }

  static function arrobj_child_delete(&$data, $name) {
    if (is_array ($data)) unset($data  [$name]);
    if (is_object($data)) unset($data->{$name});
  }

  static function arrobj_values_select_recursive(&$data, $all = false, $dpath = '') {
    $result = [];
    foreach ($data as $c_key => &$c_value) {
      $c_dpath = $dpath ? $dpath.'/'.$c_key : $c_key;
      if ((is_array($c_value) || is_object($c_value))) $result += static::arrobj_values_select_recursive($c_value, $all, $c_dpath);
      if ((is_array($c_value) || is_object($c_value)) == false || $all) $result[$c_dpath] = &$c_value;
    }
    return $result;
  }

  ###############################
  ### functionality for dpath ###
  ###############################

  static function dpath_pointers_get(&$data, $dpath, $is_unique_keys = false) {
    $result = [];
    $c_pointer = $data;
    foreach (explode('/', $dpath) as $c_part) {
      $c_pointer = &static::arrobj_value_select($c_pointer, $c_part);
      if ($is_unique_keys) $result[]        = &$c_pointer;
      else                 $result[$c_part] = &$c_pointer;
    }
    return $result;
  }

  ###################################
  ### functionality for date|time ###
  ###################################

  # see: locale::format_time(…);
  # see: locale::format_date(…);
  # see: locale::format_datetime(…);

  static function time_get    ($offset = '', $format = 'H:i:s')       {return (new \DateTime('now', new \DateTimeZone('UTC')))->modify($offset ?: '+0')->format($format);}
  static function date_get    ($offset = '', $format = 'Y-m-d')       {return (new \DateTime('now', new \DateTimeZone('UTC')))->modify($offset ?: '+0')->format($format);}
  static function datetime_get($offset = '', $format = 'Y-m-d H:i:s') {return (new \DateTime('now', new \DateTimeZone('UTC')))->modify($offset ?: '+0')->format($format);}

  ###############
  ### filters ###
  ###############

  # number validation matrix: number(n) => is_valid(0|1|2)
  # ┌───────────╥───────────┬────────────┬────────────┬─────────────┬────────────┬─────────────┬─────────────┬──────────────┬─────────────┬──────────────┐
  # │           ║           ┊ with '-'   │            ┊ with '-'    │            ┊ with '-'    │             ┊ with '-'     │             ┊ with '-'     │
  # ╞═══════════╬═══════════┊════════════╪════════════┊═════════════╪════════════┊═════════════╪═════════════┊══════════════╪═════════════┊══════════════╡
  # │           ║ ''   => 0 ┊ '-'   => 0 │ '0'   => 1 ┊ '-0'   => 0 │ '1'   => 1 ┊ '-1'   => 1 │ '01'   => 0 ┊ '-01'   => 0 │ '10'   => 1 ┊ '-10'   => 1 │
  # │ with '.'  ║ '.'  => 0 ┊ '-.'  => 0 │ '0.'  => 0 ┊ '-0.'  => 0 │ '1.'  => 0 ┊ '-1.'  => 0 │ '01.'  => 0 ┊ '-01.'  => 0 │ '10.'  => 0 ┊ '-10.'  => 0 │
  # │ with '.0' ║ '.0' => 0 ┊ '-.0' => 0 │ '0.0' => 1 ┊ '-0.0' => 2 │ '1.0' => 1 ┊ '-1.0' => 1 │ '01.0' => 0 ┊ '-01.0' => 0 │ '10.0' => 1 ┊ '-10.0' => 1 │
  # └───────────╨───────────┴────────────┴────────────┴─────────────┴────────────┴─────────────┴─────────────┴──────────────┴─────────────┴──────────────┘

  static function validate_number($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
      '%^(?<integer>[-]{0,1}[1-9][0-9]*|0)$|'.
       '^(?<float_s>[-]{0,1}[0-9][.][0-9]{1,3})$|'.
       '^(?<float_l>[-]{0,1}[1-9][0-9]+[.][0-9]{1,3})$%']]);
  }

  static function validate_date($value) {
    $matches = [];
    if (strlen($value) &&
        preg_match('%^(?<Y>[0-9]{4})-(?<m>[0-1][0-9])-(?<d>[0-3][0-9])$%', $value, $matches) &&
        checkdate($matches['m'],
                  $matches['d'],
                  $matches['Y'])) {
      return $value;
    } else return false;
  }

  static function validate_time($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
          '%^(?<H>[0-1][0-9]|20|21|22|23)'.
      '(?:[:](?<i>[0-5][0-9]))'.
      '(?:[:](?<s>[0-5][0-9])|)$%']]);
  }

  static function validate_hex_color($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
      '%^#(?<R>[a-f0-9]{2})'.
         '(?<G>[a-f0-9]{2})'.
         '(?<B>[a-f0-9]{2})$%']]);
  }

  static function validate_nick($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-z0-9-_]{4,32}$%']]);
  }

  static function validate_phone($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[+][0-9]{1,14}$%']]);
  }

  static function validate_mime_type($value) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-z]{1,20}/[a-z0-9\\-\\+\\.]{1,100}$%i']]);
  }

  static function validate_hash($value, $length = 32) {
    return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-f0-9]{'.$length.'}$%']]); # 32 - md5 | 40 - sha1 | …
  }

  static function validate_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  static function validate_ip_v4($value) {
    return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
  }

  static function validate_ip_v6($value) {
    return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
  }

  static function validate_url($value) {
    return filter_var($value, FILTER_VALIDATE_URL);
  }

  static function sanitize_url($value) {
    return filter_var($value, FILTER_SANITIZE_URL);
  }

  static function sanitize_file_part($value, $allowed_chars, $max_length) {
    $value = trim($value, '.');
    $value = preg_replace_callback('%(?<char>[^'.$allowed_chars.'])%uiS', function($c_match) {
      if ($c_match['char'] == ' ') return '-';
      if (strlen($c_match['char']) == 1) return dechex(ord($c_match['char'][0]));
      if (strlen($c_match['char']) == 2) return dechex(ord($c_match['char'][0])).dechex(ord($c_match['char'][1]));
      if (strlen($c_match['char']) == 3) return dechex(ord($c_match['char'][0])).dechex(ord($c_match['char'][1])).dechex(ord($c_match['char'][2]));
      if (strlen($c_match['char']) == 4) return dechex(ord($c_match['char'][0])).dechex(ord($c_match['char'][1])).dechex(ord($c_match['char'][2])).dechex(ord($c_match['char'][3]));
    }, $value);
    return substr($value, 0, $max_length) ?: '';
  }

  ##############################
  ### bytes|human conversion ###
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

  ############################
  ### functionality for ip ###
  ############################

  static function ip_to_hex($ip, $ip_v6_allways = true) {
    $ip_hex = '';
    $inaddr = inet_pton($ip);
    foreach (str_split($inaddr, 1) as $c_char) {
      $ip_hex.= str_pad(dechex(ord($c_char)), 2, '0', STR_PAD_LEFT);
    }
    return !$ip_v6_allways ? $ip_hex :
                     str_pad($ip_hex, 32, '0', STR_PAD_LEFT);
  }

  # hex ip to ip platform differences:
  # ┌──────────────────────────────────┬─────────────────────────────────────────┬─────────────────────────────────────────┐
  # │ hex ip                           │                        to ip on win-x86 │                        to ip on osx-x64 │
  # ├──────────────────────────────────┼─────────────────────────────────────────┼─────────────────────────────────────────┤
  # │ 00000000000000000000000000000000 │                                      :: │                                      :: │
  # │ 0000000000000000000000000000000f │                                     ::f │                              ::0.0.0.15 │
  # │ 000000000000000000000000000000ff │                                    ::ff │                             ::0.0.0.255 │
  # │ 00000000000000000000000000000fff │                                   ::fff │                            ::0.0.15.255 │
  # │ 0000000000000000000000000000ffff │                                  ::ffff │                           ::0.0.255.255 │
  # │ 000000000000000000000000000fffff │                          ::0.15.255.255 │                          ::0.15.255.255 │
  # │ 00000000000000000000000000ffffff │                         ::0.255.255.255 │                         ::0.255.255.255 │
  # │ 0000000000000000000000000fffffff │                        ::15.255.255.255 │                        ::15.255.255.255 │
  # │ 000000000000000000000000ffffffff │                       ::255.255.255.255 │                       ::255.255.255.255 │
  # │ 00000000000000000000000fffffffff │                           ::f:ffff:ffff │                           ::f:ffff:ffff │
  # │ 0000000000000000000000ffffffffff │                          ::ff:ffff:ffff │                          ::ff:ffff:ffff │
  # │ 000000000000000000000fffffffffff │                         ::fff:ffff:ffff │                         ::fff:ffff:ffff │
  # │ 00000000000000000000ffffffffffff │                  ::ffff:255.255.255.255 │                  ::ffff:255.255.255.255 │
  # │ 0000000000000000000fffffffffffff │                      ::f:ffff:ffff:ffff │                      ::f:ffff:ffff:ffff │
  # │ 000000000000000000ffffffffffffff │                     ::ff:ffff:ffff:ffff │                     ::ff:ffff:ffff:ffff │
  # │ 00000000000000000fffffffffffffff │                    ::fff:ffff:ffff:ffff │                    ::fff:ffff:ffff:ffff │
  # │ 0000000000000000ffffffffffffffff │                   ::ffff:ffff:ffff:ffff │                   ::ffff:ffff:ffff:ffff │
  # │ 000000000000000fffffffffffffffff │                 ::f:ffff:ffff:ffff:ffff │                 ::f:ffff:ffff:ffff:ffff │
  # │ 00000000000000ffffffffffffffffff │                ::ff:ffff:ffff:ffff:ffff │                ::ff:ffff:ffff:ffff:ffff │
  # │ 0000000000000fffffffffffffffffff │               ::fff:ffff:ffff:ffff:ffff │               ::fff:ffff:ffff:ffff:ffff │
  # │ 000000000000ffffffffffffffffffff │              ::ffff:ffff:ffff:ffff:ffff │              ::ffff:ffff:ffff:ffff:ffff │
  # │ 00000000000fffffffffffffffffffff │            ::f:ffff:ffff:ffff:ffff:ffff │            ::f:ffff:ffff:ffff:ffff:ffff │
  # │ 0000000000ffffffffffffffffffffff │           ::ff:ffff:ffff:ffff:ffff:ffff │           ::ff:ffff:ffff:ffff:ffff:ffff │
  # │ 000000000fffffffffffffffffffffff │          ::fff:ffff:ffff:ffff:ffff:ffff │          ::fff:ffff:ffff:ffff:ffff:ffff │
  # │ 00000000ffffffffffffffffffffffff │         ::ffff:ffff:ffff:ffff:ffff:ffff │         ::ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 0000000fffffffffffffffffffffffff |       0:f:ffff:ffff:ffff:ffff:ffff:ffff │       ::f:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 000000ffffffffffffffffffffffffff |      0:ff:ffff:ffff:ffff:ffff:ffff:ffff │      ::ff:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 00000fffffffffffffffffffffffffff |     0:fff:ffff:ffff:ffff:ffff:ffff:ffff │     ::fff:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 0000ffffffffffffffffffffffffffff |    0:ffff:ffff:ffff:ffff:ffff:ffff:ffff │    ::ffff:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 000fffffffffffffffffffffffffffff |    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff │    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 00ffffffffffffffffffffffffffffff |   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ 0fffffffffffffffffffffffffffffff |  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
  # │ ffffffffffffffffffffffffffffffff | ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │ ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
  # └──────────────────────────────────┴─────────────────────────────────────────┴─────────────────────────────────────────┘

  static function hex_to_ip($ip_hex) {
    $inaddr = '';
    foreach (str_split($ip_hex, 2) as $c_part) {
      $inaddr.= chr(hexdec($c_part));
    }
    return inet_ntop($inaddr);
  }


  ##############################################
  ### functionality for signatures|keys|hash ###
  ##############################################

  static function signature_get($string, $length = 40, $key_name) {
    $key = static::key_get($key_name);
    if ($key) return substr(sha1($string.$key), 0, $length);
    else message::insert(translation::get('Key "%%_name" does not exist!', ['name' => $key_name]), 'error');
  }

  static function key_get($name) {
    return storage::get('files')->select('settings/core/keys/'.$name);
  }

  static function key_generate() {
    return sha1(random_int(0, 0x7fffffff));
  }

  static function hash_password_get($data) {
    return sha1($data.static::key_get('salt'));
  }

  static function hash_data_get($data) {
    return md5(serialize($data));
  }

  static function random_part_get() {
    $hex_time = str_pad(dechex(time()),                    8, '0', STR_PAD_LEFT);
    $hex_rand = str_pad(dechex(random_int(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);
    return $hex_time.$hex_rand;
  }

  #####################################
  ### functionality for binary data ###
  #####################################

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

  # ┌─────────────────╥───────┬────────────────╥────────┐
  # │        ╲  modes ║       │                ║        │
  # │ server  ╲       ║ HTTPS │ REQUEST_SCHEME ║ result │
  # ╞═════════════════╬═══════╪════════════════╬════════╡
  # │ Apache v2.4     ║ -     │ http           ║ http   │
  # │ Apache v2.4 SSL ║ on    │ https          ║ https  │
  # │ NGINX  v1.1     ║ -     │ http           ║ http   │
  # │ NGINX  v1.1 SSL ║ on    │ https          ║ https  │
  # │ IIS    v7.5     ║ off   │ -              ║ http   │
  # │ IIS    v7.5 SSL ║ on    │ -              ║ https  │
  # └─────────────────╨───────┴────────────────╨────────┘

  static function server_request_scheme_get() {
    if (isset($_SERVER['REQUEST_SCHEME']))                     return $_SERVER['REQUEST_SCHEME'];
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') return 'https';
    return 'http';
  }

  static function server_host_get() {
    return $_SERVER['HTTP_HOST'];
  }

  static function server_remote_addr_get() {
    return $_SERVER['REMOTE_ADDR'] == '127.0.0.1' ? '::1' :
           $_SERVER['REMOTE_ADDR'];
  }

  static function server_request_uri_get() {
    return $_SERVER['REQUEST_URI'];
  }

  static function server_user_agent_get() {
    return isset($_SERVER['HTTP_USER_AGENT']) ?
          substr($_SERVER['HTTP_USER_AGENT'], 0, 240) : '';
  }

  static function server_http_range_get() {
    $matches = [];
    preg_match('%^bytes=(?<min>[0-9]+)-'.
                       '(?<max>[0-9]*|)$%', $_SERVER['HTTP_RANGE'] ?? '', $matches);
    $result = new \stdClass;
    $result->min = array_key_exists('min', $matches) && strlen($matches['min']) ? (int)$matches['min'] : null;
    $result->max = array_key_exists('max', $matches) && strlen($matches['max']) ? (int)$matches['max'] : null;
    return $result;
  }

  static function server_user_agent_info_get() {
    $result = new \stdCLass;
  # detect Internet Explorer v.6-v.11
  # note: unexist version like '12' will be identified as '1'
    $matches = [];
    $ie_core_to_name = ['8' => '11', '7' => '11', '6' => '10', '5' => '9', '4' => '8', '3' => '7', '2' => '6', '1' => '5'];
    $ie_name_to_core = array_flip($ie_core_to_name);
    preg_match('%^(?:.+?(?<name>MSIE) '.'(?<name_v>11|10|9|8|7|6|5|4|3|2|1)|)'.
                 '(?:.+?(?<core>Trident)/(?<core_v>8|7|6|5|4|3|2|1)|)%', static::server_user_agent_get(), $matches);
    $result->name = isset($matches['name']) ? strtolower($matches['name']) : '';
    $result->core = isset($matches['core']) ? strtolower($matches['core']) : '';
    $result->core_version = $matches['core_v'] ?? '';
    $result->name_version = $matches['name_v'] ?? '';
    if ($result->name == '' && $result->core && isset($ie_core_to_name[$matches['core_v']])) {$result->name = 'msie';    $result->name_version = $ie_core_to_name[$matches['core_v']];}
    if ($result->core == '' && $result->name && isset($ie_name_to_core[$matches['name_v']])) {$result->core = 'trident'; $result->core_version = $ie_name_to_core[$matches['name_v']];}
    return $result;
  }

  static function server_software_get() {
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
    $result = $precision ? substr(
      number_format($number, $precision, $dec_point, $thousands), 0, -5) :
      number_format($number, $precision, $dec_point, $thousands);
    if ($no_zeros) {
      $result = rtrim($result, '0');
      $result = rtrim($result, $dec_point);
    }
    return $result;
  }

  static function send_header_and_exit($type, $title = '', $message = '', $p = '') {
    switch ($type) {
      case 'redirect'        : header('Location: '.$p);          break;
      case 'page_refresh'    : header('Refresh: ' .$p);          break;
      case 'access_forbidden': header('HTTP/1.1 403 Forbidden'); break;
      case 'page_not_found'  : header('HTTP/1.0 404 Not Found'); break;
      case 'file_not_found'  : header('HTTP/1.0 404 Not Found'); break;
    }
    $front_page_link = translation::get('go to <a href="/">front page</a>');
    if ($type == 'access_forbidden') {print (new template('page_access_forbidden', ['attributes' => core::data_to_attr(['lang' => language::current_code_get()]), 'message' => $message ?: $front_page_link, 'title' => translation::get('Access forbidden')]))->render(); exit();}
    if ($type == 'page_not_found')   {print (new template('page_not_found',        ['attributes' => core::data_to_attr(['lang' => language::current_code_get()]), 'message' => $message ?: $front_page_link, 'title' => translation::get('Page not found')]))->render();   exit();}
    if ($type == 'file_not_found')   {print (new template('page_not_found',        ['attributes' => core::data_to_attr(['lang' => language::current_code_get()]), 'message' => $message ?: $front_page_link, 'title' => translation::get('File not found')]))->render();   exit();}
    if ($message)                    {print (new template('page_simple',           ['attributes' => core::data_to_attr(['lang' => language::current_code_get()]), 'message' => $message ?: $front_page_link, 'title' => translation::get($title)]))->render();             exit();}
    exit();
  }

  static function to_css_class($string) {
    return str_replace(['/', ' '], '-', strtolower($string));
  }

}}