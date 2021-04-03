<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\profile_classic {
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_recovery {

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        if (!url::back_url_get() && page::get_current()->id === 'recovery_ru') {
          url::get_current()->query_arg_insert('back', '/ru/login');
        }
        break;
    }
  }

}}