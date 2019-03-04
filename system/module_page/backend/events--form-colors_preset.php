<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\message;
          use \effcore\page;
          use \effcore\storage;
          abstract class events_form_colors_preset {

  static function on_init($form, $items) {
    $id = page::current_get()->args_get('id');
    $preset = color::preset_get($id);
    $colors = color::all_get();
    $items['#color_page_id'            ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_page_id            ]->value, 'attributes');
    $items['#color_menu_id'            ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_menu_id            ]->value, 'attributes');
    $items['#color_menu_active_id'     ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_menu_active_id     ]->value, 'attributes');
    $items['#color_menu_text_id'       ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_menu_text_id       ]->value, 'attributes');
    $items['#color_menu_link_id'       ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_menu_link_id       ]->value, 'attributes');
    $items['#color_menu_link_active_id']->attribute_insert('style', 'background: '.$colors[$preset->colors->color_menu_link_active_id]->value, 'attributes');
    $items['#color_text_id'            ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_text_id            ]->value, 'attributes');
    $items['#color_link_id'            ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_link_id            ]->value, 'attributes');
    $items['#color_link_active_id'     ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_link_active_id     ]->value, 'attributes');
    $items['#color_main_id'            ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_main_id            ]->value, 'attributes');
    $items['#color_ok_id'              ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_ok_id              ]->value, 'attributes');
    $items['#color_warning_id'         ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_warning_id         ]->value, 'attributes');
    $items['#color_error_id'           ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_error_id           ]->value, 'attributes');
    $items['#color_button_id'          ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_button_id          ]->value, 'attributes');
    $items['#color_button_active_id'   ]->attribute_insert('style', 'background: '.$colors[$preset->colors->color_button_active_id   ]->value, 'attributes');
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $id = page::current_get()->args_get('id');
        $preset = color::preset_get($id);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_page_id',             $preset->colors->color_page_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_id',             $preset->colors->color_menu_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_active_id',      $preset->colors->color_menu_active_id,      false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_text_id',        $preset->colors->color_menu_text_id,        false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_id',        $preset->colors->color_menu_link_id,        false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id', $preset->colors->color_menu_link_active_id, false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',             $preset->colors->color_text_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',             $preset->colors->color_link_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id',      $preset->colors->color_link_active_id,      false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',             $preset->colors->color_main_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',               $preset->colors->color_ok_id,               false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',          $preset->colors->color_warning_id,          false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',            $preset->colors->color_error_id,            false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_id',           $preset->colors->color_button_id,           false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_active_id',    $preset->colors->color_button_active_id          );
        message::insert('Colors was applied.');
        static::on_init($form, $items);
        break;
    }
  }

}}