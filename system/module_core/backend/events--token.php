<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\url;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'return_if_url_arg':
        if (count($args) > 2) {
          $arg_name_expected  = $args[0];
          $arg_value_expected = $args[1];
          $arg_value_real     = url::get_current()->query_arg_select($arg_name_expected);
          $value_if_true      = $args[2] ?? '';
          $value_if_false     = $args[3] ?? '';
          return $arg_value_real ===
                 $arg_value_expected ?
                 $value_if_true :
                 $value_if_false;
        }
        break;
    }
    return '';
  }

}}