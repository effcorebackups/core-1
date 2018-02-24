<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore\modules\page {
          use \effectivecore\storage as storage;
          abstract class events_token extends \effectivecore\events_token {

  static function on_color_get($match, $arg_1_num = null) {
    $settings = storage::get('files')->select('settings');
    $colors   = storage::get('files')->select('colors');
    switch ($match) {
      case '%%_color'   : return $colors['page'][ $settings['page']->color_id    ]->value;
      case '%%_color_bg': return $colors['page'][ $settings['page']->color_bg_id ]->value;
    }
  }

}}