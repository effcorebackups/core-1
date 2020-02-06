<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_decoration_settings {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    $items['#width_min']->value_set($settings->page_width_min);
    $items['#width_max']->value_set($settings->page_width_max);
  }

  static function on_validate($event, $form, $items) {
  }

  static function on_submit($event, $form, $items) {
    storage::get('files')->changes_insert('page', 'update', 'settings/page/page_width_min', $items['#width_min']->value_get(), false);
    storage::get('files')->changes_insert('page', 'update', 'settings/page/page_width_max', $items['#width_max']->value_get()       );
    message::insert('The changes was saved.');
  }

}}