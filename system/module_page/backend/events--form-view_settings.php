<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_view_settings {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    $items['#width_min'    ]->value_set($settings->page_width_min    );
    $items['#width_max'    ]->value_set($settings->page_width_max    );
    $items['#meta_viewport']->value_set($settings->page_meta_viewport);
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        if ($items['#width_min']->value_get() >
            $items['#width_max']->value_get()) {
            $items['#width_min']->error_set();
            $items['#width_max']->error_set();
            $form->error_set('The minimum value is greater than the maximum value!');
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $result = storage::get('files')->changes_insert('page', 'update', 'settings/page/page_width_min',     $items['#width_min'    ]->value_get(), false);
        $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/page_width_max',     $items['#width_max'    ]->value_get(), false);
        $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/page_meta_viewport', $items['#meta_viewport']->value_get()       );
        if ($result) message::insert('Changes was saved.'             );
        else         message::insert('Changes was not saved!', 'error');
        break;
    }
  }

}}