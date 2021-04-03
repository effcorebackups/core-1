<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\profile_classic {
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_registration {

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        if (!url::back_url_get() && page::get_current()->id === 'registration_ru') {
          url::get_current()->query_arg_insert('back', '/ru/user/'.$items['#nickname']->value_get());
        }
        break;
    }
  }

}}