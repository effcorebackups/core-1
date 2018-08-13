<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\storage;
          abstract class events_token extends \effcore\events_token {

  static function on_color_get($name, $arg_1_number = null) {
    $settings = storage::get('files')->select('settings');
    $colors   = storage::get('files')->select('colors');
    switch ($name) {
      case 'color'   : return $colors['page'][ $settings['page']->color_id    ]->value;
      case 'color_bg': return $colors['page'][ $settings['page']->color_bg_id ]->value;
    }
  }

}}