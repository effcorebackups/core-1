<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\session;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_logout {

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'logout':
        session::delete(user::current_get()->nick);
        url::go('/');
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/');
        break;
    }
  }

}}