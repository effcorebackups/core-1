<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\module;
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_registration {

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'register':
        if (!url::back_url_get() && page::get_current()->id === 'registration_ru') {
          if (module::settings_get('user')->send_password_to_email)
               url::back_url_set('back', '/ru/login');
          else url::back_url_set('back', '/ru/user/'.$items['#nickname']->value_get());
        }
        break;
    }
  }

}}