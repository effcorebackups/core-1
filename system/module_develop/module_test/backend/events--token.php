<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\core;
          use \effcore\field_captcha;
          use \effcore\module;
          use \effcore\step_request;
          abstract class events_token {

  static $cache = [];

  static function on_apply($name, $args = []) {
    if ($name === 'test_email_random'    && count($args) === 0) {                                                                                                            return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com';}
    if ($name === 'test_nickname_random' && count($args) === 0) {                                                                                                            return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));               }
    if ($name === 'test_password_random' && count($args) === 0) {                                                                                                            return         core::password_generate();                                    }
    if ($name === 'test_email_random'    && count($args) === 1) {if (!isset( static::$cache['test_email_random'   ][$args[0]] )) static::$cache['test_email_random'   ][$args[0]] = 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com'; return static::$cache['test_email_random'   ][$args[0]];}
    if ($name === 'test_nickname_random' && count($args) === 1) {if (!isset( static::$cache['test_nickname_random'][$args[0]] )) static::$cache['test_nickname_random'][$args[0]] = 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));                return static::$cache['test_nickname_random'][$args[0]];}
    if ($name === 'test_password_random' && count($args) === 1) {if (!isset( static::$cache['test_password_random'][$args[0]] )) static::$cache['test_password_random'][$args[0]] =         core::password_generate();                                     return static::$cache['test_password_random'][$args[0]];}
    if ($name === 'test_captcha') {
      if (module::is_enabled('captcha')) {
        $last_responce = end(step_request::$history);
        if ($last_responce) {
          return field_captcha::get_code_by_id(
            core::ip_to_hex($last_responce['info']['primary_ip'])
          );
        }
      }
    }
    if ($name === 'test_form_validation_id' && count($args) === 1) {
      $last_responce = end(step_request::$history);
      if ($last_responce) {
        $form_id = $args[0];
        return $last_responce['headers']['X-Form-Validation-Id--'.$form_id] ?? '';
      }
    }
    if ($name === 'test_cookies') {
      $result = [];
      foreach (step_request::$history as $c_responce) {
        if ( isset($c_responce['headers']['Set-Cookie']) ) {
          foreach ($c_responce['headers']['Set-Cookie'] as $c_cookie) {
            $c_key   = core::array_key_first($c_cookie['parsed']);
            $c_value =                 reset($c_cookie['parsed']);
            $result[$c_key] = $c_value; }}}
      return core::data_to_attr($result, false, '; ', '', '');
    }
  }

}}