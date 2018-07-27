<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class test
          implements has_external_cache {

  public $id;
  public $scenario;

  function run() {
    $values = [];
    $result = null;
    foreach ($this->scenario as $c_step) {
      switch ($c_step->type) {
        case 'set':
          $values = $c_step->values;
          break;
        case 'request':
          if (isset($values['captcha'])) {
            $captcha = (new instance('captcha', [
              'ip_address' => '127.0.0.1'
            ]))->select();
            if ($captcha) {
              $values['captcha'] = $captcha->characters;
            }
          }
          $url = ($c_step->https ? 'https' : 'http').'://'.url::current_get()->domain.$c_step->url;
          $result = test::request($url, [], $values);
          break;
        case 'check':
        # @todo: make functionality
          break;
        case 'return':
        # @todo: make functionality
          break;
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function not_external_properties_get() {
    return ['id' => 'id'];
  }

  static function init() {
    foreach (storage::get('files')->select('tests') as $c_module_id => $c_tests) {
      foreach ($c_tests as $c_row_id => $c_test) {
        if (isset(static::$cache[$c_test->id])) console::log_about_duplicate_add('test', $c_test->id);
        static::$cache[$c_test->id] = $c_test;
        static::$cache[$c_test->id]->module_id = $c_module_id;
      }
    }
  }

  static function get($id, $load = true) {
    if (!isset(static::$cache)) static::init();
    if (static::$cache[$id] instanceof external_cache && $load)
        static::$cache[$id] = static::$cache[$id]->external_cache_load();
    return static::$cache[$id];
  }

  static function request($url, $headers = [], $post = [], $proxy = '') {
    $return = ['info' => [], 'headers' => []];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    if ($proxy) curl_setopt($curl, CURLOPT_PROXY, $proxy);
  # prepare headers
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $c_header) use (&$return) {
      $c_matches = [];
      preg_match('%^(?<name>[^:]+): (?<value>.*)$%S', $c_header, $c_matches);
      if ($c_matches) $return['headers'][$c_matches['name']] = trim($c_matches['value'], "\r\n\"");
      return strlen($c_header);
    });
  # prepare post query
    if ($post) {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
  # prepare return
    $data = curl_exec($curl);
    $return['error_message'] = curl_error($curl);
    $return['error_num'] = curl_errno($curl);
    $return['data'] = $data ? ltrim($data, chr(0xff).chr(0xfe)) : '';
    $return['info'] = curl_getinfo($curl);
    curl_close($curl);
    return $return;
  }

}}