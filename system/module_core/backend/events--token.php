<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\url;
          use \effcore\token;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'return_if_token':
        if (count($args) > 2) {
          $text = token::text_decode($args[0]);
          if (strpos($text, 'return_if_token') === 0) return '/* "return_if_token" inside "return_if_token" is meaningless */';
          if (count($args) === 3) return token::apply('%%_'.$text) === $args[1] ? $args[2] : '';
          if (count($args) === 4) return token::apply('%%_'.$text) === $args[1] ? $args[2] : $args[3];
        }
        break;
      case 'return_if_url_arg':
        if (count($args) === 3) return url::get_current()->query_arg_select($args[0]) === $args[1] ? $args[2] : '';
        if (count($args) === 4) return url::get_current()->query_arg_select($args[0]) === $args[1] ? $args[2] : $args[3];
        break;
    }
  }

}}