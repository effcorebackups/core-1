<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\message;
          use \effcore\storage;
          abstract class events_form_console {

  static function on_init($form, $items) {
    $settings = storage::get('files')->select('settings');
    $items['#visibility']->value_set($settings['page']->console_visibility);
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        storage::get('files')->changes_insert('page', 'update', 'settings/page/console_visibility', $items['#visibility']->value_get());
        message::insert('The changes was saved.');
        break;
    }
  }

}}