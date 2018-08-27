<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\br;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\file;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\page;
          use \effcore\session;
          use \effcore\translation;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form extends \effcore\events_form {

  ####################
  ### form: logout ###
  ####################

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