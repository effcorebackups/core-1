<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\markup;
          abstract class events_form_modules_update {

  static function on_init($form, $items) {
  # @todo: make functionality
    $items['~apply']->disabled_set();
    $form->child_update('info', new markup('x-no-result', [], 'no items'));
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        break;
    }
  }

}}