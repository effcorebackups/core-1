<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\message;
          use \effcore\page;
          use \effcore\storage;
          abstract class events_form_colors_preset {

  static function on_init($form, $items) {
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        break;
    }
  }

}}