<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\storage;
          abstract class events_token {

  static function on_color_get($name, $arg_1_number = null) {
    $settings = storage::get('files')->select('settings');
    $colors   = color::all_get();
    switch ($name) {
      case 'color'   : return $colors[$settings['page']->color_id   ]->value;
      case 'color_bg': return $colors[$settings['page']->color_bg_id]->value;
    }
  }

}}