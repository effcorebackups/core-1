<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\captcha {
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_security_settings_captcha {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('captcha');
    $items['#length']->value_set($settings->captcha_length);
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        storage::get('files')->changes_insert('captcha', 'update', 'settings/captcha/captcha_length', $items['#length']->value_get());
        message::insert('The changes was saved.');
        break;
    }
  }

}}