<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\message;
          use \effcore\storage;
          abstract class events_form_decoration {

  static function on_init($form, $items) {
    $settings = storage::get('files')->select('settings');
    $items['*color_id'   ]->value_set($settings['page']->color_id   );
    $items['*color_bg_id']->value_set($settings['page']->color_bg_id);
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_id',    $items['*color_id'   ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_bg_id', $items['*color_bg_id']->value_get());
        message::insert('The changes was saved.');
        break;
      case 'restore':
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_id', false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_bg_id');
        message::insert('The changes was deleted.');
        static::on_init($form, $items);
        break;
    }
  }

}}