<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color_preset;
          use \effcore\field_checkbox_color;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_colors_preset {

  static function on_init($event, $form, $items) {
    $id = page::get_current()->args_get('id');
    $preset = color_preset::get($id);
    if ($preset) {
      foreach ($items as $c_item) {
        if ($c_item instanceof field_checkbox_color) {
          if (strpos($c_item->name_get(), 'color__') === 0) {
            $c_item->color_set(
              $preset->colors->{$c_item->name_get()}
            );
          }
        }
      }
    } else $items['~apply']->disabled_set(true);
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $changes = [];
        $id = page::get_current()->args_get('id');
        $preset = color_preset::get($id);
        if ($preset) {
          foreach ($items as $c_item) {
            if ($c_item instanceof field_checkbox_color) {
              if (strpos($c_item->name_get(), 'color__') === 0) {
                if ($c_item->checked_get()) {
                  $changes[$c_item->name_get()] = true;
                }
              }
            }
          }
          if (!count($changes)) {
            message::insert('No one item was selected!', 'warning');
          } else {
            $result = color_preset::apply($id, $changes, true);
            if ($result) message::insert('Colors was applied.'             );
            else         message::insert('Colors was not applied!', 'error');
            static::on_init(null, $form, $items);
          }
        }
        break;
    }
  }

}}