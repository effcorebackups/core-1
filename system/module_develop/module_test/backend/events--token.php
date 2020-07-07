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
    if ($name === 'test_email_random'   ) return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff)).'@example.com';
    if ($name === 'test_nickname_random') return 'test_'.core::hash_get_mini(random_int(0, 0x7fffffff));
    if ($name === 'test_password_random') return core::password_generate();
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
  }

}}