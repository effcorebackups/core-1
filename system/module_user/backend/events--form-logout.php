<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\session;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_logout extends \effcore\events_form {

  static function on_submit_logout($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'logout':
        session::delete(user::current_get()->id);
        url::go('/');
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/');
        break;
    }
  }

}}