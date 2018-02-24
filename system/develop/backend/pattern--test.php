<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          class test {

  public $id;
  public $https = false;
  public $url = null;
  public $id_user = 0;

  ######################
  ### static methods ###
  ######################

  static protected $cache;

  static function init() {
    $tests = storage::get('files')->select('tests');
    foreach ($tests as $c_module_id => $c_module_tests) {
      foreach ($c_module_tests as $c_row_id => $c_test) {
        static::$cache[$c_test->id] = $c_test;
      }
    }
  }

  static function get($id) {
    if (!isset(static::$cache[$id])) static::init();
    return     static::$cache[$id];
  }

  static function run($id) {
    $test = static::get($id);
    $is_https = $test->https;
    $url = $test->url;
    $id_user = $test->id_user;
    foreach ($test->scenario as $c_action) {
      switch ($c_action->action_type) {
        case 'fill':   break; # @todo: make functionality
        case 'submit': break; # @todo: make functionality
        case 'check':  break; # @todo: make functionality
      }
    }
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