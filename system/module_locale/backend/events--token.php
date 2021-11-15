<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locale {
          use \effcore\token;
          use \effcore\translation;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'return_translation':
        if (count($args) === 1) return translation::apply($args[0]);
        if (count($args)  >  1) {
          $text = token::text_decode($args[0]);
          $real_args = [];
          for ($i = 1; $i < count($args); $i++) {
            $c_result = explode('=', $args[$i]);
            if (count($c_result) === 2)
              $real_args[$c_result[0]] =
                         $c_result[1]; }
          return translation::apply($text, $real_args);
        }
        break;
    }
  }

}}