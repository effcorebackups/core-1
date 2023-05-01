<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class request {

  static protected $cache;
  static protected $allowed_args_in_get = [];

  static function init() {
    if (static::$cache === null) {
      foreach (storage::get('data')->select_array('request_settings') as $c_module_id => $c_settings) {
        static::$allowed_args_in_get+= $c_settings->allowed_args_in_get;
        static::$cache[$c_module_id] = $c_settings;
      }
    }
  }

  static function allowed_args_in_get_get() {
    static::init();
    return static::$allowed_args_in_get;
  }

  # ─────────────────────────────────────────────────────────────────────
  # sanitize(…, $is_files === false) of requests:
  # ═════════════════════════════════════════════════════════════════════
  #   (string)key => (string)value
  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  #   (string)key => [
  #     (int)0 => (string)value,
  #     (int)1 => (string)value …
  #     (int)N => (string)value
  #   ]
  # ─────────────────────────────────────────────────────────────────────

  # ─────────────────────────────────────────────────────────────────────
  # sanitize(…, $is_files !== false) of requests:
  # ═════════════════════════════════════════════════════════════════════
  #   (string)key => [
  #     (string)key => (string)|(int)value
  #   ]
  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  #   (string)key => [
  #     (string)key => [
  #       (int)0 => (string)|(int)value,
  #       (int)1 => (string)|(int)value …
  #       (int)N => (string)|(int)value
  #     ]
  #   ]
  # ─────────────────────────────────────────────────────────────────────

  static function sanitize($source = '_POST', $is_files = false) {
    $result = [];
    global ${$source};
  # filtering by structure
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
  # filtering by whitelist
    if ($source === '_GET') {
      $allowed_args = request::allowed_args_in_get_get();
      foreach ($result as $c_name => $c_value) {
        if (!isset($allowed_args[$c_name])) {
          unset($result[$c_name]);
        }
      }
    }
    return $result;
  }

  # conversion matrix:
  # ┌──────────────────────────────────────────╥────────────────┐
  # │ input value (undefined | string | array) ║ result value   │
  # ╞══════════════════════════════════════════╬════════════════╡
  # │ source[field] === undefined              ║ return ''      │
  # │ source[field] === ''                     ║ return ''      │
  # │ source[field] === 'value'                ║ return 'value' │
  # ├──────────────────────────────────────────╫────────────────┤
  # │ source[field] === [undefined]            ║ return ''      │
  # │ source[field] === [0 => '']              ║ return ''      │
  # │ source[field] === [0 => 'value']         ║ return 'value' │
  # └──────────────────────────────────────────╨────────────────┘

  static function value_get($name, $number = 0, $source = '_POST', $return_default = '') {
    global ${$source};
    if (   !isset(${$source}[$name])) return  $return_default;
    if (is_string(${$source}[$name])) return ${$source}[$name];
    if ( is_array(${$source}[$name]) &&
            isset(${$source}[$name][$number]))
    return        ${$source}[$name][$number];
    return $return_default;
  }

  # conversion matrix:
  # ┌──────────────────────────────────────────╥──────────────────────────┐
  # │ input value (undefined | string | array) ║ result value             │
  # ╞══════════════════════════════════════════╬══════════════════════════╡
  # │ source[field] === undefined              ║ return []                │
  # │ source[field] === ''                     ║ return [0 => '']         │
  # │ source[field] === 'value'                ║ return [0 => 'value']    │
  # ├──────────────────────────────────────────╫──────────────────────────┤
  # │ source[field] === [undefined]            ║ return []                │
  # │ source[field] === [0 => '']              ║ return [0 => '']         │
  # │ source[field] === [0 => '', …]           ║ return [0 => '', …]      │
  # │ source[field] === [0 => 'value']         ║ return [0 => 'value']    │
  # │ source[field] === [0 => 'value', …]      ║ return [0 => 'value', …] │
  # └──────────────────────────────────────────╨──────────────────────────┘

  static function values_get($name, $source = '_POST', $return_default = []) {
    global ${$source};
    if (   !isset(${$source}[$name])) return   $return_default;
    if (is_string(${$source}[$name])) return [${$source}[$name]];
    if ( is_array(${$source}[$name])) return  ${$source}[$name];
    return $return_default;
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

  static function files_get($name, $return_class_name = 'file_history') {
    $result = [];
    if (isset($_FILES[$name]['name'    ]) &&
        isset($_FILES[$name]['type'    ]) &&
        isset($_FILES[$name]['size'    ]) &&
        isset($_FILES[$name]['tmp_name']) &&
        isset($_FILES[$name]['error'   ])) {
      $info = $_FILES[$name];
    # converting into a unified structure
      if (!is_array($info['name'    ])) $info['name'    ] = [$info['name'    ]];
      if (!is_array($info['type'    ])) $info['type'    ] = [$info['type'    ]];
      if (!is_array($info['size'    ])) $info['size'    ] = [$info['size'    ]];
      if (!is_array($info['tmp_name'])) $info['tmp_name'] = [$info['tmp_name']];
      if (!is_array($info['error'   ])) $info['error'   ] = [$info['error'   ]];
    # preparing the result
      foreach ($info['name'] as $c_number => $c_file) {
        $c_file     = trim($c_file); # note: "name.type"
        $c_mime     = $info['type'    ][$c_number];
        $c_size     = $info['size'    ][$c_number];
        $c_path_tmp = $info['tmp_name'][$c_number];
        $c_error    = $info['error'   ][$c_number];
        if ($c_error !== UPLOAD_ERR_NO_FILE) {
          if ($return_class_name === 'file_history') {
            $result[$c_number] = new file_history;
            $result[$c_number]->init_from_tmp($c_file, $c_mime, $c_size, $c_path_tmp, $c_error);
          } else {
            $result[$c_number] = new $return_class_name;
            $result[$c_number]->file     = $c_file;
            $result[$c_number]->mime     = $c_mime;
            $result[$c_number]->size     = $c_size;
            $result[$c_number]->path_tmp = $c_path_tmp;
            $result[$c_number]->error    = $c_error;
          }
        }
      }
    }
    return $result;
  }

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

  static function scheme_get() {
    if (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') return 'https';
    if (isset($_SERVER['HTTPS'])          && $_SERVER['HTTPS']          === 'on'   ) return 'https';
    return 'http';
  }

  static function host_get($decode = false) {
    if ($decode && function_exists('idn_to_utf8') && idn_to_utf8($_SERVER['HTTP_HOST'], IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46))
         return idn_to_utf8($_SERVER['HTTP_HOST'], IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
    else return             $_SERVER['HTTP_HOST'];
  }

  static function addr_get() {
    return $_SERVER[!empty($_SERVER['IIS_WasUrlRewritten']) ? 'LOCAL_ADDR' : 'SERVER_ADDR'];
  }

  static function addr_remote_get() {
    return $_SERVER['REMOTE_ADDR'];
  }

  static function uri_get() {
    return $_SERVER[!empty($_SERVER['IIS_WasUrlRewritten']) ? 'UNENCODED_URL' : 'REQUEST_URI'];
  }

  static function path_get() {
    return strstr(static::uri_get(), '?', true) ?:
                  static::uri_get();
  }

  static function query_get() {
    return $_SERVER['QUERY_STRING'];
  }

  static function http_range_get() {
    $result = new \stdClass;
    $result->has_range = false;
    $result->min = null;
    $result->max = null;
    if (isset($_SERVER['HTTP_RANGE'])) {
      $result->has_range = true;
      $matches = [];
      preg_match('%^bytes=(?<min>[0-9]+)-'.
                         '(?<max>[0-9]*)$%', $_SERVER['HTTP_RANGE'], $matches);
      if (array_key_exists('min', $matches) && strlen($matches['min'])) $result->min = (int)$matches['min'];
      if (array_key_exists('max', $matches) && strlen($matches['max'])) $result->max = (int)$matches['max'];
    }
    return $result;
  }

  static function user_agent_get($max_length = 240) {
    return                isset($_SERVER['HTTP_USER_AGENT']) ?
      mb_strcut(trim(strip_tags($_SERVER['HTTP_USER_AGENT'])), 0, $max_length) : '';
  }

  static function user_agent_get_info() {
    $result = new \stdCLass;
    $matches = [];
  # detect Internet Explorer v.6-v.11 (non-existent version like '12' will be identified as '1')
    $ie_core_to_name = ['8' => '11', '7' => '11', '6' => '10', '5' => '9', '4' => '8', '3' => '7', '2' => '6', '1' => '5'];
    $ie_name_to_core = array_flip($ie_core_to_name);
    preg_match('%^(?:.+?(?<name>MSIE) '.'(?<name_v>11|10|9|8|7|6|5|4|3|2|1)|)'.
                 '(?:.+?(?<core>Trident)/(?<core_v>8|7|6|5|4|3|2|1)|)%', static::user_agent_get(), $matches);
    $result->name = isset($matches['name']) ? strtolower($matches['name']) : '';
    $result->core = isset($matches['core']) ? strtolower($matches['core']) : '';
    $result->core_version = $matches['core_v'] ?? '';
    $result->name_version = $matches['name_v'] ?? '';
    if ($result->name === '' && $result->core && isset($ie_core_to_name[$matches['core_v']])) {$result->name = 'msie';    $result->name_version = $ie_core_to_name[$matches['core_v']];}
    if ($result->core === '' && $result->name && isset($ie_name_to_core[$matches['name_v']])) {$result->core = 'trident'; $result->core_version = $ie_name_to_core[$matches['name_v']];}
    return $result;
  }

  static function software_get_info($software = null) {
    $result = new \stdClass;
    $result->name = 'Unknown';
    $result->version = '';
    if ($software === null)
        $software = $_SERVER['SERVER_SOFTWARE'];
    $matches = [];
    preg_match('%^(?<full_name>(?<name>[a-zA-Z0-9\\-]+)/(?<version>[a-zA-Z0-9\\.]+).*)$|'.
                '^(?<full_name_unknown>.*)$%', $software, $matches);
    if (isset($matches['full_name'        ])) {$result->name = strtolower($matches['name']); $result->version = $matches['version'];}
    if (isset($matches['full_name_unknown'])) {$result->name = strtolower($matches['full_name_unknown']);}
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function cookie_get_info($string) {
    $result = [];
    foreach (explode('; ', $string) as $c_part) {
      $c_matches = [];
      preg_match('%^(?<name>[^=]+)='.
                   '(?<value>.*)$%S', $c_part, $c_matches);
      if ($c_matches)
        $result[$c_matches['name']] =
                $c_matches['value'];
    }
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function make($url, $headers = [], $post = [], $settings = []) {
    $result = ['info' => [], 'headers' => []];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,             $url    );
    curl_setopt($curl, CURLOPT_HTTPHEADER,      $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,  true    );
    curl_setopt($curl, CURLOPT_HEADER,          false   );
    curl_setopt($curl, CURLOPT_PATH_AS_IS,      true    ); # added in CURL v.7.42.0 (2015-04-22)
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,  array_key_exists('followlocation', $settings) ? $settings['followlocation'] : false);
    curl_setopt($curl, CURLOPT_TIMEOUT,         array_key_exists('timeout',        $settings) ? $settings['timeout']        : 5);
    curl_setopt($curl, CURLOPT_SSLVERSION,      array_key_exists('sslversion',     $settings) ? $settings['sslversion']     : CURL_SSLVERSION_TLSv1_2);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,  array_key_exists('ssl_verifyhost', $settings) ? $settings['ssl_verifyhost'] : false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,  array_key_exists('ssl_verifypeer', $settings) ? $settings['ssl_verifypeer'] : false);
    curl_setopt($curl, CURLOPT_PROXY,           array_key_exists('proxy',          $settings) ? $settings['proxy']          : null);
  # prepare post query
    if ($post) {
      curl_setopt($curl, CURLOPT_POST,        true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
  # prepare headers
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curl, $c_header) use (&$result) {
      $c_matches = [];
      preg_match('%^(?<name>[^:]+): (?<value>.*)$%S', $c_header, $c_matches);
      if ($c_matches && $c_matches['name'] !== 'Set-Cookie') $result['headers'][$c_matches['name']]   =           trim($c_matches['value'], cr.nl.'"');
      if ($c_matches && $c_matches['name'] === 'Set-Cookie') $result['headers'][$c_matches['name']][] = ['raw' => trim($c_matches['value'], cr.nl.'"'), 'parsed' => static::cookie_get_info(trim($c_matches['value'], cr.nl.'"'))];
      return strlen($c_header);
    });
  # prepare return
    $data = curl_exec($curl);
    $result['error_message'] = curl_error($curl);
    $result['error_number' ] = curl_errno($curl);
    $result['data'] = $data ? ltrim($data, "\xff\xfe") : '';
    $result['info'] = curl_getinfo($curl);
    curl_close($curl);
    return $result;
  }

}}