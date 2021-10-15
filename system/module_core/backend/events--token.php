<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\request;
          use \effcore\token;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'request_scheme'     : return request::scheme_get();
      case 'request_host'       : return request::host_get();
      case 'request_host_decode': return request::host_get(true);
      case 'request_addr'       : return request::addr_get();
      case 'request_addr_remote': return request::addr_remote_get();
      case 'request_uri'        : return request::uri_get();
      case 'request_path'       : return request::path_get();
      case 'request_query'      : return request::query_get();
      case 'return_if_token':
        if (count($args) > 2) {
          $text = token::text_decode($args[0]);
          if (strpos($text, 'return_if') === 0) return '!!! "return_if…" inside "return_if_token" is meaningless !!!';
          if (count($args) === 3) return token::apply('%%_'.$text) === $args[1] ? $args[2] : '';
          if (count($args) === 4) return token::apply('%%_'.$text) === $args[1] ? $args[2] : $args[3];
        }
        break;
      case 'return_if_url_arg':
        if (count($args) === 3) return request::value_get($args[0], 0, '_GET') === $args[1] ? $args[2] : '';
        if (count($args) === 4) return request::value_get($args[0], 0, '_GET') === $args[1] ? $args[2] : $args[3];
        break;
      case 'return_url_arg':
        if (count($args) === 3) {
          $color_arg_name = $args[0];
          $color_default  = $args[1];
          $filter         = $args[2];
          if ($filter === 'css_units') {
            $color_arg = core::sanitize_css_units(request::value_get($color_arg_name, 0, '_GET'));
            return $color_arg ?: $color_default;
          }
        }
        break;
    }
  }

}}