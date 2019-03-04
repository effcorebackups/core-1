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
    $items['*color_page_id'            ]->value_set( $preset->colors->color_page_id             );
    $items['*color_menu_id'            ]->value_set( $preset->colors->color_menu_id             );
    $items['*color_menu_active_id'     ]->value_set( $preset->colors->color_menu_active_id      );
    $items['*color_menu_text_id'       ]->value_set( $preset->colors->color_menu_text_id        );
    $items['*color_menu_link_id'       ]->value_set( $preset->colors->color_menu_link_id        );
    $items['*color_menu_link_active_id']->value_set( $preset->colors->color_menu_link_active_id );
    $items['*color_text_id'            ]->value_set( $preset->colors->color_text_id             );
    $items['*color_link_id'            ]->value_set( $preset->colors->color_link_id             );
    $items['*color_link_active_id'     ]->value_set( $preset->colors->color_link_active_id      );
    $items['*color_main_id'            ]->value_set( $preset->colors->color_main_id             );
    $items['*color_ok_id'              ]->value_set( $preset->colors->color_ok_id               );
    $items['*color_warning_id'         ]->value_set( $preset->colors->color_warning_id          );
    $items['*color_error_id'           ]->value_set( $preset->colors->color_error_id            );
    $items['*color_button_id'          ]->value_set( $preset->colors->color_button_id           );
    $items['*color_button_active_id'   ]->value_set( $preset->colors->color_button_active_id    );
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_page_id',             $items['*color_page_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_id',             $items['*color_menu_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_active_id',      $items['*color_menu_active_id'     ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_text_id',        $items['*color_menu_text_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_id',        $items['*color_menu_link_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id', $items['*color_menu_link_active_id']->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',             $items['*color_text_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',             $items['*color_link_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id',      $items['*color_link_active_id'     ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',             $items['*color_main_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',               $items['*color_ok_id'              ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',          $items['*color_warning_id'         ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',            $items['*color_error_id'           ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_id',           $items['*color_button_id'          ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_active_id',    $items['*color_button_active_id'   ]->value_get());
        message::insert('Colors was applied.');
        static::on_init($form, $items);
        break;
    }
  }

}}