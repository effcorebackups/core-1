<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_request {

  public $url;
  public $https = false;
  public $post = [];

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $post = $this->post;
    foreach ($post as $c_name => $c_value) {
      if ($c_value == '%%_captcha')
          $c_value = $this->captcha_code_get();
      $post[$c_name] = $c_value;
    }
    $c_results['report'][] = translation::get('make request');
    $c_results['request'] = test::request($this->url_generate(), [], $post, $test->proxy);
  }

  function captcha_code_get() {
    $captcha = (new instance('captcha', [
      'ip_address' => '127.0.0.1'
    ]))->select();
    if ($captcha) {
      return $captcha->characters;
    }
  }

  function url_generate() {
    return ($this->https ? 'https' : 'http').'://'.url::current_get()->domain.$this->url;
  }

}}