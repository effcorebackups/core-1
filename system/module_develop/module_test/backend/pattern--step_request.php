<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_request {

  public $url;
  public $https = false;
  public $proxy = '';
  public $headers = [];
  public $post = [];

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $c_results['reports'][] = translation::get('make request');
    $c_results['request'] = test::request(
      $this->prepared_url_get(),
      $this->prepared_headers_get(),
      $this->prepared_post_get(),
      $this->proxy
    );
  }

  function prepared_url_get() {
    return ($this->https ? 'https' : 'http').'://'.url::current_get()->domain.$this->url;
  }

  function prepared_headers_get() {
    return $this->headers;
  }

  function prepared_post_get() {
    $return = [];
    foreach ($this->post as $c_name => $c_value) {
      if ($c_value == '%%_captcha')
          $c_value = $this->captcha_code_get();
      $return[$c_name] = $c_value;
    }
    return $return;
  }

  function captcha_code_get() {
    $captcha = (new instance('captcha', [
      'ip_address' => '127.0.0.1'
    ]))->select();
    if ($captcha) {
      return $captcha->characters;
    }
  }

}}