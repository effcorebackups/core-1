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
    $items['*color_text_id'       ]->value_set($settings['page']->color_text_id       );
    $items['*color_link_id'       ]->value_set($settings['page']->color_link_id       );
    $items['*color_link_active_id']->value_set($settings['page']->color_link_active_id);
    $items['*color_main_id'       ]->value_set($settings['page']->color_main_id         );
    $items['*color_ok_id'         ]->value_set($settings['page']->color_ok_id         );
    $items['*color_warning_id'    ]->value_set($settings['page']->color_warning_id    );
    $items['*color_error_id'      ]->value_set($settings['page']->color_error_id      );
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',        $items['*color_text_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',        $items['*color_link_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id', $items['*color_link_active_id']->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',        $items['*color_main_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',          $items['*color_ok_id'         ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',     $items['*color_warning_id'    ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',       $items['*color_error_id'      ]->value_get());
        message::insert('The changes was saved.');
        break;
      case 'restore':
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_text_id',        false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_link_id',        false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_link_active_id', false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_main_id',        false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_ok_id',          false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_warning_id',     false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_error_id');
        message::insert('The changes was deleted.');
        static::on_init($form, $items);
        break;
    }
  }

}}