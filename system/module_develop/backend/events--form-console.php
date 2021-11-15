<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\console;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_console {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    $items['#visibility']->value_set($settings->console_visibility);
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $result = storage::get('data')->changes_insert('page', 'update', 'settings/page/console_visibility', $items['#visibility']->value_get());
        if ($result) message::insert('Changes was saved.'             );
        else         message::insert('Changes was not saved!', 'error');
        console::init(true);
        break;
    }
  }

}}