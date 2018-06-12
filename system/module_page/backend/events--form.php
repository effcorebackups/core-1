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
    $items['colors/color_id'   ]->values_set([$settings['page']->color_id   ]);
    $items['colors/color_bg_id']->values_set([$settings['page']->color_bg_id]);
  }

  static function on_validate_decoration($form, $fields, &$values) {
  }

  static function on_submit_decoration($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'save':
        storage::get('files')->changes_register('page', 'update', 'settings/page/color_id',    $values['color_id'][0], false);
        storage::get('files')->changes_register('page', 'update', 'settings/page/color_bg_id', $values['color_bg_id'][0]);
        message::insert('The changes have been saved.');
        break;
      case 'restore':
        storage::get('files')->changes_unregister('page', 'update', 'settings/page/color_id');
        storage::get('files')->changes_unregister('page', 'update', 'settings/page/color_bg_id');
      # message::insert('The changes have been deleted.');
        message::insert('UNDER CONSTRUCTION'); # @todo: make functionality
        break;
    }
  }

}}