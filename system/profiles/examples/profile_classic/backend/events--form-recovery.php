<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_recovery {

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'recovery':
        if (!url::back_url_get() && page::get_current()->id === 'recovery_ru') {
          url::back_url_set('back', '/ru/login');
        }
        break;
    }
  }

}}