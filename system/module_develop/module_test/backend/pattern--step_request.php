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

  function run(&$test, $dpath, &$c_results) {
    $proxy = $this->proxy instanceof param_from_form ?
             $this->proxy->get() :
             $this->proxy;
    $prepared_url     = $this->prepared_url_get    ();
    $prepared_headers = $this->prepared_headers_get();
    $prepared_post    = $this->prepared_post_get   ();
                $c_results['reports'][$dpath][] = new text('make request to "%%_url"', ['url'   => $this->prepared_url_get()]);
    if ($proxy) $c_results['reports'][$dpath][] = new text('proxy server = %%_proxy',  ['proxy' => $proxy]);
    foreach ($prepared_headers as           $c_value) $c_results['reports'][$dpath][] = new text('&ndash; request header param "%%_value"',           [                  'value' => $c_value]);
    foreach ($prepared_post    as $c_key => $c_value) $c_results['reports'][$dpath][] = new text('&ndash; request post param "%%_name" = "%%_value"', ['name' => $c_key, 'value' => $c_value]);
  # make request
    $response = static::request(
      $prepared_url,
      $prepared_headers,
      $prepared_post, $proxy);
    if (isset($response['info']['http_code'   ])) $c_results['reports'][$dpath][] = new text('&ndash; response param "%%_name" = "%%_value"', ['name' => 'http_code',    'value' => $response['info']['http_code'   ]]);
    if (isset($response['info']['primary_ip'  ])) $c_results['reports'][$dpath][] = new text('&ndash; response param "%%_name" = "%%_value"', ['name' => 'primary_ip',   'value' => $response['info']['primary_ip'  ]]);
    if (isset($response['info']['primary_port'])) $c_results['reports'][$dpath][] = new text('&ndash; response param "%%_name" = "%%_value"', ['name' => 'primary_port', 'value' => $response['info']['primary_port']]);
    if (isset($response['info']['local_ip'    ])) $c_results['reports'][$dpath][] = new text('&ndash; response param "%%_name" = "%%_value"', ['name' => 'local_ip',     'value' => $response['info']['local_ip'    ]]);
    if (isset($response['info']['local_port'  ])) $c_results['reports'][$dpath][] = new text('&ndash; response param "%%_name" = "%%_value"', ['name' => 'local_port',   'value' => $response['info']['local_port'  ]]);
    if (isset( $response['headers']['Set-Cookie']) ) {
      foreach ($response['headers']['Set-Cookie'] as $c_cookie) {
        $c_results['reports'][$dpath][] = new text('&ndash; response param "%%_name" = "%%_value"', ['name' => 'Set-Cookie', 'value' => $c_cookie['raw']]);
      }
    }
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
    $result = [];
    foreach ($this->headers as $c_key => $c_value)
      if (is_string($c_value))
        $result[$c_key] = token::apply($c_value);
    return $result;
  }

  function prepared_post_get() {
    $result = [];
    foreach ($this->post as $c_key => $c_value)
      if (is_string($c_value))
        $result[$c_key] = token::apply($c_value);
    return $result;
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
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($curl, CURLOPT_HEADER,         false);
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
      if ($c_matches && $c_matches['name'] !== 'Set-Cookie') $result['headers'][$c_matches['name']]   =           trim($c_matches['value'], "\r\n\"");
      if ($c_matches && $c_matches['name'] === 'Set-Cookie') $result['headers'][$c_matches['name']][] = ['raw' => trim($c_matches['value'], "\r\n\""), 'parsed' => static::cookie_parse(trim($c_matches['value'], "\r\n\""))];
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

  static function cookie_parse($string) {
    $result = [];
    foreach (explode('; ', $string) as $c_part) {
      $c_matches = [];
      preg_match('%^(?<name>[^=]+)=(?<value>.*)$%S', $c_part, $c_matches);
      if ($c_matches) {
        $result[$c_matches['name']] = $c_matches['value'];
      }
    }
    return $result;
  }

}}