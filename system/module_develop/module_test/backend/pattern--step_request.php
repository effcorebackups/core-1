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
  public $prev_response;

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    $this->prev_response = $c_results['response'] ?? null;
    $prepared_post = $this->prepared_post_get();
    $c_results['reports'][] = translation::get('make request to "%%_url"', ['url' => $this->prepared_url_get()]);
    foreach ($prepared_post as $c_name => $c_value) {
      $c_results['reports'][] = translation::get('&ndash; request post param "%%_name" = "%%_value"', ['name' => $c_name, 'value' => $c_value]);
    }
  # make request
    $c_results['response'] = static::request(
      $this->prepared_url_get(),
      $this->prepared_headers_get(),
      $prepared_post,
      $this->proxy
    );
  }

  function prepared_url_get() {
    $is_https = $this->https instanceof param_from_form ?
                $this->https->get() :
                $this->https;
    return ($is_https ? 'https' : 'http').'://'.url::current_get()->domain.$this->url;
  }

  function prepared_headers_get() {
    return $this->headers;
  }

  function prepared_post_get() {
    $result = [];
    foreach ($this->post as $c_name => $c_value) {
      if ($c_value == '%%_nick_random'    ) $c_value = $this->nick_random_get    ();
      if ($c_value == '%%_email_random'   ) $c_value = $this->email_random_get   ();
      if ($c_value == '%%_password_random') $c_value = $this->password_random_get();
      if ($c_value == '%%_captcha'        ) $c_value = $this->captcha_code_get   ();
      if ($c_value == '%%_validation_id'  ) $c_value = $this->validation_id_get  ();
      $result[$c_name] = $c_value;
    }
    return $result;
  }

  function nick_random_get() {
    return 'test_'.core::mini_hash_get(random_int(0, 0x7fffffff));
  }

  function email_random_get() {
    return 'test_'.core::mini_hash_get(random_int(0, 0x7fffffff)).'@example.com';
  }

  function password_random_get() {
    return core::password_generate();
  }

  function captcha_code_get() {
    if (module::is_enabled('captcha')) {
      return field_captcha::captcha_localhost_code_get();
    } else {
      return '';
    }
  }

  function validation_id_get() {
    $form_id            = $this->post         ['form_id']                                    ?? '';
    $prev_validation_id = $this->prev_response['headers']['X-Form-Validation-Id--'.$form_id] ?? '';
    return $prev_validation_id;
  }

  ###########################
  ### static declarations ###
  ###########################

  static $curlopt_timeout = 5;
  static $curlopt_sslversion = CURL_SSLVERSION_TLSv1_2;
  static $curlopt_ssl_verifyhost = false;
  static $curlopt_ssl_verifypeer = false;

  static function request($url, $headers = [], $post = [], $proxy = '') {
    $result = ['info' => [], 'headers' => []];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_PATH_AS_IS, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_TIMEOUT,        static::$curlopt_timeout);
    curl_setopt($curl, CURLOPT_SSLVERSION,     static::$curlopt_sslversion);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, static::$curlopt_ssl_verifyhost);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, static::$curlopt_ssl_verifypeer);
    if ($proxy) curl_setopt($curl, CURLOPT_PROXY, $proxy);
  # prepare headers
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, function($curl, $c_header) use (&$result) {
      $c_matches = [];
      preg_match('%^(?<name>[^:]+): (?<value>.*)$%S', $c_header, $c_matches);
      if ($c_matches) $result['headers'][$c_matches['name']] = trim($c_matches['value'], "\r\n\"");
      return strlen($c_header);
    });
  # prepare post query
    if ($post) {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
  # prepare return
    $data = curl_exec($curl);
    $result['error_message'] = curl_error($curl);
    $result['error_number'] = curl_errno($curl);
    $result['data'] = $data ? ltrim($data, chr(255).chr(254)) : '';
    $result['info'] = curl_getinfo($curl);
    curl_close($curl);
    return $result;
  }

}}