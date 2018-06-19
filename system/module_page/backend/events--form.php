<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\message;
          use \effcore\storage;
          abstract class events_form extends \effcore\events_form {

  ########################
  ### form: decoration ###
  ########################

  static function on_init_decoration($form, $items) {
    $settings = storage::get('files')->select('settings');
    $items['##color_id'   ]->value_set($settings['page']->color_id   );
    $items['##color_bg_id']->value_set($settings['page']->color_bg_id);
  }

  static function on_validate_decoration($form, $items) {
  }

  static function on_submit_decoration($form, $items) {
    switch ($form->clicked_button_name) {
      case 'save':
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_id',    $items['##color_id'   ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_bg_id', $items['##color_bg_id']->value_get());
        message::insert('The changes was saved.');
        break;
      case 'restore':
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_id');
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_bg_id');
        message::insert('The changes was deleted.');
        break;
    }
  }

}}