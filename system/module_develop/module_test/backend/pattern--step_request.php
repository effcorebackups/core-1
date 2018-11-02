<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_request {

  public $url;
  public $https = false;
  public $proxy = '';
  public $headers = [];
  public $post = [];

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $prepared_post = $this->prepared_post_get();
    $c_results['reports'][] = translation::get('make request to "%%_url"', ['url' => $this->prepared_url_get()]);
    foreach ($prepared_post as $c_name => $c_value) {
      $c_results['reports'][] = translation::get('&ndash; request post param "%%_name" = "%%_value"', ['name' => $c_name, 'value' => $c_value]);
    }
  # make request
    $c_results['request'] = test::request(
      $this->prepared_url_get(),
      $this->prepared_headers_get(),
      $prepared_post,
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
    $result = [];
    foreach ($this->post as $c_name => $c_value) {
      if ($c_value == '%%_nick_random')     $c_value = $this->nick_random_get();
      if ($c_value == '%%_email_random')    $c_value = $this->email_random_get();
      if ($c_value == '%%_password_random') $c_value = $this->password_random_get();
      if ($c_value == '%%_captcha')         $c_value = $this->captcha_code_get();
      if ($c_value == '%%_validation_id')   $c_value = $this->validation_id_get();
      $result[$c_name] = $c_value;
    }
    return $result;
  }

  function nick_random_get() {
    return 'test_'.substr(md5(random_int(0, 0x7fffffff)), 0, 8);
  }

  function email_random_get() {
    return 'test_'.substr(md5(random_int(0, 0x7fffffff)), 0, 8).'@example.com';
  }

  function password_random_get() {
    return substr(md5(random_int(0, 0x7fffffff)), 0, 8);
  }

  function captcha_code_get() {
    return field_captcha::captcha_localhost_code_get();
  }

  function validation_id_get() {
    return 'UNDER CONSTRUCTION'; # @todo: make functionality
  }

}}