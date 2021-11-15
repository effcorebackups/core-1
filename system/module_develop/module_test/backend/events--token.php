<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\captcha;
          use \effcore\core;
          use \effcore\module;
          use \effcore\request;
          use \effcore\step_request;
          use \effcore\user;
          abstract class events_token {

  static protected $cache = [];

  static function on_apply($name, $args = []) {
    if ($name === 'test_software_name') return request::software_get_info()->name ?? '';
    if ($name === 'test_email_random'    && count($args) === 0) {                                                                                                                                            return 'test_'.core::hash_get_mini(random_int(0, PHP_INT_32_MAX)).'@example.com';}
    if ($name === 'test_nickname_random' && count($args) === 0) {                                                                                                                                            return 'test_'.core::hash_get_mini(random_int(0, PHP_INT_32_MAX));               }
    if ($name === 'test_password_random' && count($args) === 0) {                                                                                                                                            return         user::password_generate();                                        }
    if ($name === 'test_email_random'    && count($args) === 1) {if (!isset( static::$cache['test_email_random'   ][core::hash_get($args[0])] )) static::$cache['test_email_random'   ][core::hash_get($args[0])] = 'test_'.core::hash_get_mini(random_int(0, PHP_INT_32_MAX)).'@example.com'; return static::$cache['test_email_random'   ][core::hash_get($args[0])];}
    if ($name === 'test_nickname_random' && count($args) === 1) {if (!isset( static::$cache['test_nickname_random'][core::hash_get($args[0])] )) static::$cache['test_nickname_random'][core::hash_get($args[0])] = 'test_'.core::hash_get_mini(random_int(0, PHP_INT_32_MAX));                return static::$cache['test_nickname_random'][core::hash_get($args[0])];}
    if ($name === 'test_password_random' && count($args) === 1) {if (!isset( static::$cache['test_password_random'][core::hash_get($args[0])] )) static::$cache['test_password_random'][core::hash_get($args[0])] =         user::password_generate();                                         return static::$cache['test_password_random'][core::hash_get($args[0])];}
    if ($name === 'test_cookies') {
      $result = [];
      foreach (step_request::$history as $c_response) {
        if ( isset($c_response['headers']['Set-Cookie']) ) {
          foreach ($c_response['headers']['Set-Cookie'] as $c_cookie) {
            $c_key   = core::array_key_first($c_cookie['parsed']);
            $c_value =                 reset($c_cookie['parsed']);
            $result[$c_key] = $c_value; }}}
      return core::data_to_attributes($result, false, '; ', '', '');
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
    if (strpos($name, 'test_response_') === 0) {
      $type = substr($name, strlen('test_response_'));
      $last_response = end(step_request::$history);
      if ($last_response) {
        if ($type === 'content'        && isset($last_response['data'])                                                                                ) return (string)$last_response['data'];
        if ($type === 'http_code'      && isset($last_response['info'])    && array_key_exists('http_code',                  $last_response['info']   )) return    (int)$last_response['info']['http_code'];
        if ($type === 'location'       && isset($last_response['headers']) && array_key_exists('Location',                   $last_response['headers'])) return (string)$last_response['headers']['Location'];
        if ($type === 'content_length' && isset($last_response['headers']) && array_key_exists('Content-Length',             $last_response['headers'])) return    (int)$last_response['headers']['Content-Length'];
        if ($type === 'content_range'  && isset($last_response['headers']) && array_key_exists('Content-Range',              $last_response['headers'])) return (string)$last_response['headers']['Content-Range'];
        if ($type === 'submit_error'   && isset($last_response['headers']) && array_key_exists('X-Form-Submit-Errors-Count', $last_response['headers'])) return    (int)$last_response['headers']['X-Form-Submit-Errors-Count'];
        if ($type === 'time_total'     && isset($last_response['headers']) && array_key_exists('X-Time-total',               $last_response['headers'])) return  (float)$last_response['headers']['X-Time-total'];
        if ($type === 'return_level'   && isset($last_response['headers']) && array_key_exists('X-Return-level',             $last_response['headers'])) return (string)$last_response['headers']['X-Return-level'];
      }
    }
  }

}}