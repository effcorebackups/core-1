<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class test
          implements has_external_cache {

  public $id;
  public $title;
  public $scenario;
  public $max_iteration = 1000;

  function run() {
    $c_scenario = $this->scenario;
    $c_step = reset($c_scenario);
    $c_iteration = 0;
    $c_results = [];
    while ($c_step !== false) {

    # prevention from looping
      if (++$c_iteration > $this->max_iteration) {
        break;
      }

    # run next step
      $c_step->run($this, $c_scenario, $c_step, $c_results);
      if (array_key_exists('is_continue', $c_results)) {unset($c_results['is_continue']); continue;}
      if (array_key_exists('return', $c_results)) break;

    # go to the next item
      $c_step = next($c_scenario);
    }
    return $c_results;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function not_external_properties_get() {
    return ['id' => 'id', 'title' => 'title'];
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

  static function all_get($load = true) {
    if (!static::$cache) static::init();
    if ($load)
      foreach (static::$cache as &$c_item)
        if ($c_item instanceof external_cache && $load)
            $c_item = $c_item->external_cache_load();
    return static::$cache;
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