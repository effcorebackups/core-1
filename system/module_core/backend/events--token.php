<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\url;
          use \effcore\token;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'return_if_token':
        if (count($args) > 2) {
          $text = token::text_decode($args[0]);
          if (strpos($text, 'return_if') === 0) return '!!! "return_if…" inside "return_if_token" is meaningless !!!';
          if (count($args) === 3) return token::apply('%%_'.$text) === $args[1] ? $args[2] : '';
          if (count($args) === 4) return token::apply('%%_'.$text) === $args[1] ? $args[2] : $args[3];
        }
        break;
      case 'return_if_url_arg':
        if (count($args) === 3) return url::get_current()->query_arg_select($args[0]) === $args[1] ? $args[2] : '';
        if (count($args) === 4) return url::get_current()->query_arg_select($args[0]) === $args[1] ? $args[2] : $args[3];
        break;
      case 'return_url_arg':
        if (count($args) === 3) {
          $arg_name      = $args[0];
          $default_value = $args[1];
          $filter        = $args[2];
          if ($filter === 'css_units') {
            return is_string(url::get_current()->query_arg_select($arg_name)) ? core::sanitize_css_units(
                             url::get_current()->query_arg_select($arg_name)) : $default_value;
          }
        }
        break;
    }
  }

}}