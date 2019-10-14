<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class step_request {

  public $url;
  public $is_https = false;
  public $proxy    = '';
  public $headers  = [];
  public $post     = [];

  function run(&$test, &$c_scenario, &$c_step, &$c_results) {
    if (isset($c_results['response'])) static::$history_responses[] = $c_results['response'];
    $prepared_post = $this->prepared_get_post();
    $reports[] = translation::get('make request to "%%_url"', ['url' => $this->prepared_get_url()]);
    foreach ($prepared_post as $c_key => $c_value)
      $reports[] = translation::get('&ndash; request post param "%%_name" = "%%_value"', ['name' => $c_key, 'value' => $c_value]);
  # make request
    $response = static::request(
      $this->prepared_get_url(),
      $this->prepared_get_headers(),
      $prepared_post,
      $this->proxy);
    $reports[] = translation::get('&ndash; response param "%%_name" = "%%_value"', ['name' => 'http_code', 'value' => $response['info']['http_code']]);
    $c_results['reports'][] = $reports;
    $c_results['response'] = $response;
  }

  function prepared_get_url() {
    $is_https = $this->is_https instanceof param_from_form ?
                $this->is_https->get() :
                $this->is_https;
    return ($is_https ? 'https' : 'http').'://'.url::get_current()->domain.$this->url;
  }

  function prepared_get_headers() {
    return $this->headers;
  }

  function prepared_get_post() {
    $result = [];
    foreach ($this->post as $c_key => $c_value) {
      if ($c_value == '%%_nickname_random') $c_value = $this->random_get_nickname();
      if ($c_value == '%%_password_random') $c_value = $this->random_get_password();
      if ($c_value == '%%_email_random'   ) $c_value = $this->random_get_email   ();
      if ($c_value == '%%_captcha'        ) $c_value = $this->captcha_code_get   ();
      if ($c_value == '%%_validation_id'  ) $c_value = $this->validation_id_get  ();
      $result[$c_key] = $c_value;
    }
    return $result;
  }

  function random_get_nickname() {
    return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));
  }

  function random_get_email() {
    return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com';
  }

  function random_get_password() {
    return core::password_generate();
  }

  function captcha_code_get() {
    if (module::is_enabled('captcha')) {
      $last_responce = end(static::$history_responses);
      if ($last_responce) {
        return field_captcha::get_code_by_id(
          core::ip_to_hex($last_responce['info']['primary_ip'])
        );
      }
    }
  }

  function validation_id_get() {
    $last_responce = end(static::$history_responses);
    if ($last_responce) {
      $form_id            = $this->post   ['form_id']                                    ?? '';
      $prev_validation_id = $last_responce['headers']['X-Form-Validation-Id--'.$form_id] ?? '';
      return $prev_validation_id;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static $history_responses = [];
  static $curlopt_timeout = 5;
  static $curlopt_sslversion = CURL_SSLVERSION_TLSv1_2;
  static $curlopt_ssl_verifyhost = false;
  static $curlopt_ssl_verifypeer = false;

  static function request($url, $headers = [], $post = [], $proxy = '') {
    $result = ['info' => [], 'headers' => []];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,             $url);
    curl_setopt($curl, CURLOPT_PATH_AS_IS,      true); # added in CURL v.7.42.0 (2015-04-22)
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,  true);
    curl_setopt($curl, CURLOPT_HEADER,         false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER,  $headers);
    curl_setopt($curl, CURLOPT_TIMEOUT,        static::$curlopt_timeout       );
    curl_setopt($curl, CURLOPT_SSLVERSION,     static::$curlopt_sslversion    );
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
      curl_setopt($curl, CURLOPT_POST,        true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
  # prepare return
    $data = curl_exec($curl);
    $result['error_message'] = curl_error($curl);
    $result['error_number' ] = curl_errno($curl);
    $result['data'] = $data ? ltrim($data, chr(255).chr(254)) : '';
    $result['info'] = curl_getinfo($curl);
    curl_close($curl);
    return $result;
  }

}}