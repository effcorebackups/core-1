<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color_preset;
          use \effcore\group_palette;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_colors {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    foreach ($items as $c_item) {
      if ($c_item instanceof group_palette) {
        if (strpos($c_item->name_get_complex(), 'color__') === 0) {
          $c_item->value_set(
            $settings->{$c_item->name_get_complex()}
          );
        }
      }
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $selected = [];
        foreach ($items as $c_item) {
          if ($c_item instanceof group_palette) {
            if ( strpos($c_item->name_get_complex(), 'color__') === 0 ) {
              $selected[$c_item->name_get_complex()] = $c_item->value_get();
            }
          }
        }
        $result = color_preset::apply_with_custom_ids($selected, true);
        if ($result) message::insert('Changes was saved.'             );
        else         message::insert('Changes was not saved!', 'error');
        break;
      case 'reset':
        $result = color_preset::reset();
        if ($result) message::insert('Changes was deleted.'             );
        else         message::insert('Changes was not deleted!', 'error');
        static::on_init(null, $form, $items);
        break;
    }
  }

}}