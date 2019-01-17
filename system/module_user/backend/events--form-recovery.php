<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          abstract class events_form_recovery {

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        break;
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        break;
    }
  }

}}