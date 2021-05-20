<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\translation;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    switch ($name) {
      case 'return_translation':
        if (count($args) > 0)
          return translation::apply($args[0]);
        break;
    }
  }

}}