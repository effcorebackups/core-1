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
    $prepared_url     = $this->prepared_url_get    ();
    $prepared_headers = $this->prepared_headers_get();
    $prepared_post    = $this->prepared_post_get   ();
    $reports[] = translation::apply('make request to "%%_url"', ['url' => $this->prepared_url_get()]);
    foreach ($prepared_post as $c_key => $c_value)
      $reports[] = translation::apply('&ndash; request post param "%%_name" = "%%_value"', ['name' => $c_key, 'value' => $c_value]);
  # make request
    $response = static::request(
      $prepared_url,
      $prepared_headers,
      $prepared_post, $this->proxy);
    if (isset($response['info'   ]['http_code'   ])) $reports[] = translation::apply('&ndash; response param "%%_name" = "%%_value"', ['name' => 'http_code',    'value' => $response['info'   ]['http_code'   ]]);
    if (isset($response['info'   ]['primary_ip'  ])) $reports[] = translation::apply('&ndash; response param "%%_name" = "%%_value"', ['name' => 'primary_ip',   'value' => $response['info'   ]['primary_ip'  ]]);
    if (isset($response['info'   ]['primary_port'])) $reports[] = translation::apply('&ndash; response param "%%_name" = "%%_value"', ['name' => 'primary_port', 'value' => $response['info'   ]['primary_port']]);
    if (isset($response['info'   ]['local_ip'    ])) $reports[] = translation::apply('&ndash; response param "%%_name" = "%%_value"', ['name' => 'local_ip',     'value' => $response['info'   ]['local_ip'    ]]);
    if (isset($response['info'   ]['local_port'  ])) $reports[] = translation::apply('&ndash; response param "%%_name" = "%%_value"', ['name' => 'local_port',   'value' => $response['info'   ]['local_port'  ]]);
    if (isset($response['headers']['Set-Cookie'  ])) $reports[] = translation::apply('&ndash; response param "%%_name" = "%%_value"', ['name' => 'Set-Cookie',   'value' => $response['headers']['Set-Cookie'  ]]);
    $c_results['reports'][] = $reports;
    $c_results['response'] = $response;
    static::$history[    ] = $response;
  }

  function prepared_url_get() {
    $is_https = $this->is_https instanceof param_from_form ?
                $this->is_https->get() :
                $this->is_https;
    return ($is_https ? 'https' : 'http').'://'.url::get_current()->domain.$this->url;
  }

  function prepared_headers_get() {
    return $this->headers;
  }

  function prepared_post_get() {
    $result = [];
    foreach ($this->post as $c_key => $c_value) {
      if ($c_value == '%%_nickname_random') $c_value = $this->random_nickname_get();
      if ($c_value == '%%_password_random') $c_value = $this->random_password_get();
      if ($c_value == '%%_email_random'   ) $c_value = $this->random_email_get   ();
      if ($c_value == '%%_captcha'        ) $c_value = $this->captcha_code_get   ();
      if ($c_value == '%%_validation_id'  ) $c_value = $this->validation_id_get  ();
      $result[$c_key] = $c_value;
    }
    return $result;
  }

  function random_nickname_get() {
    return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));
  }

  function random_email_get() {
    return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com';
  }

  function random_password_get() {
    return core::password_generate();
  }

  function captcha_code_get() {
    if (module::is_enabled('captcha')) {
      $last_responce = end(static::$history);
      if ($last_responce) {
        return field_captcha::get_code_by_id(
          core::ip_to_hex($last_responce['info']['primary_ip'])
        );
      }
    }
  }

  function validation_id_get() {
    $last_responce = end(static::$history);
    if ($last_responce) {
      $form_id            = $this->post   ['form_id']                                    ?? '';
      $prev_validation_id = $last_responce['headers']['X-Form-Validation-Id--'.$form_id] ?? '';
      return $prev_validation_id;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static $history = [];
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
  # prepare post query
    if ($post) {
      curl_setopt($curl, CURLOPT_POST,        true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
  # prepare headers
    curl_setopt($curl, CURLOPT_HEADERFUNCTION, function ($curl, $c_header) use (&$result) {
      $c_matches = [];
      preg_match('%^(?<name>[^:]+): (?<value>.*)$%S', $c_header, $c_matches);
      if ($c_matches) $result['headers'][$c_matches['name']] = trim($c_matches['value'], "\r\n\"");
      return strlen($c_header);
    });
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