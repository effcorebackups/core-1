<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\captcha;
          use \effcore\core;
          use \effcore\module;
          use \effcore\step_request;
          abstract class events_token {

  static protected $cache = [];

  static function on_apply($name, $args = []) {
    if ($name === 'test_email_random'    && count($args) === 0) {                                                                                                                                            return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com';}
    if ($name === 'test_nickname_random' && count($args) === 0) {                                                                                                                                            return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));               }
    if ($name === 'test_password_random' && count($args) === 0) {                                                                                                                                            return         core::password_generate();                                    }
    if ($name === 'test_email_random'    && count($args) === 1) {if (!isset( static::$cache['test_email_random'   ][core::hash_get($args[0])] )) static::$cache['test_email_random'   ][core::hash_get($args[0])] = 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com'; return static::$cache['test_email_random'   ][core::hash_get($args[0])];}
    if ($name === 'test_nickname_random' && count($args) === 1) {if (!isset( static::$cache['test_nickname_random'][core::hash_get($args[0])] )) static::$cache['test_nickname_random'][core::hash_get($args[0])] = 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));                return static::$cache['test_nickname_random'][core::hash_get($args[0])];}
    if ($name === 'test_password_random' && count($args) === 1) {if (!isset( static::$cache['test_password_random'][core::hash_get($args[0])] )) static::$cache['test_password_random'][core::hash_get($args[0])] =         core::password_generate();                                     return static::$cache['test_password_random'][core::hash_get($args[0])];}
    if ($name === 'test_cookies') {
      $result = [];
      foreach (step_request::$history as $c_response) {
        if ( isset($c_response['headers']['Set-Cookie']) ) {
          foreach ($c_response['headers']['Set-Cookie'] as $c_cookie) {
            $c_key   = core::array_key_first($c_cookie['parsed']);
            $c_value =                 reset($c_cookie['parsed']);
            $result[$c_key] = $c_value; }}}
      return core::data_to_attr($result, false, '; ', '', '');
    }
    if ($name === 'test_captcha') {
      if (module::is_enabled('captcha')) {
        $last_response = end(step_request::$history);
        if ($last_response) {
          $captcha = captcha::select_by_id(core::ip_to_hex($last_response['info']['primary_ip']));
          return $captcha->characters ?? null;
        }
      }
    }
    if ($name === 'test_form_validation_id' && count($args) === 1) {
      $last_response = end(step_request::$history);
      if ($last_response) {
        return $last_response['headers']['X-Form-Validation-Id--'.$args[0]] ?? '';
      }
    }
    if ($name === 'test_response_http_code') {
      $last_response = end(step_request::$history);
      if ($last_response && isset($last_response['info']) && array_key_exists('http_code', $last_response['info'])) {
        return (int)$last_response['info']['http_code'];
      }
    }
    if ($name === 'test_response_submit_error') {
      $last_response = end(step_request::$history);
      if ($last_response && isset($last_response['headers']) && array_key_exists('X-Form-Submit-Errors-Count', $last_response['headers'])) {
        return (int)$last_response['headers']['X-Form-Submit-Errors-Count'];
      }
    }
    if ($name === 'test_response_time_total') {
      $last_response = end(step_request::$history);
      if ($last_response && isset($last_response['headers']) && array_key_exists('X-Time-total', $last_response['headers'])) {
        return $last_response['headers']['X-Time-total'];
      }
    }
  }

}}