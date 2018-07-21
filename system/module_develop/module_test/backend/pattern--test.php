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
    foreach ($this->scenario as $c_step) {
      switch ($c_step->type) {
        case 'set'     : print ' set ';     break; # @todo: make functionality (name|value)
        case 'request' : print ' request '; break; # @todo: make functionality (url|https)
        case 'check'   : print ' check ';   break; # @todo: make functionality (where|match|on_success|on_failure)
        case 'return'  : print ' return ';  break; # @todo: make functionality (value)
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

  static function get($id) {
    if (!isset(static::$cache)) static::init();
    if (static::$cache[$id] instanceof external_cache)
        static::$cache[$id] = static::$cache[$id]->external_cache_load();
    return static::$cache[$id];
  }

  static function request_send($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_SSLv3);
    $data = curl_exec($curl);
    $info = curl_getinfo($curl);
    $info['simplexml'] = null;
    curl_close($curl);
    if ($data && $info['http_code'] == 200) {
      libxml_use_internal_errors(true);
      $simplexml = simplexml_load_string($data);
      if ($simplexml) {
        $info['simplexml'] = $simplexml;
      }
    }
    return $info;
  }

}}