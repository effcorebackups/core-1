<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          abstract class events_form_instance_update {

  static function on_init($form, &$items) {
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'update':
        break;
    }
  }

}}