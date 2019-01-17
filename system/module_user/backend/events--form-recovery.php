<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\instance;
          abstract class events_form_recovery {

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        if (!$form->has_error()) {
          if (!(new instance('user', ['email' => strtolower($items['#email']->value_get())]))->select()) {
            $items['#email']->error_set(
              'User with this EMail was not registered!'
            );
            return;
          }
        }
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        break;
    }
  }

}}