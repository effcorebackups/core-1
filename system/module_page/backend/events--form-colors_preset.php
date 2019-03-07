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
    $items['#color_page_id'            ]->color_set($preset->colors->color_page_id            );
    $items['#color_text_id'            ]->color_set($preset->colors->color_text_id            );
    $items['#color_main_id'            ]->color_set($preset->colors->color_main_id            );
    $items['#color_link_id'            ]->color_set($preset->colors->color_link_id            );
    $items['#color_link_active_id'     ]->color_set($preset->colors->color_link_active_id     );
    $items['#color_table_row_odd_id'   ]->color_set($preset->colors->color_table_row_odd_id   );
    $items['#color_table_row_even_id'  ]->color_set($preset->colors->color_table_row_even_id  );
    $items['#color_relation_id'        ]->color_set($preset->colors->color_relation_id        );
    $items['#color_menu_id'            ]->color_set($preset->colors->color_menu_id            );
    $items['#color_menu_active_id'     ]->color_set($preset->colors->color_menu_active_id     );
    $items['#color_menu_text_id'       ]->color_set($preset->colors->color_menu_text_id       );
    $items['#color_menu_link_id'       ]->color_set($preset->colors->color_menu_link_id       );
    $items['#color_menu_link_active_id']->color_set($preset->colors->color_menu_link_active_id);
    $items['#color_ok_id'              ]->color_set($preset->colors->color_ok_id              );
    $items['#color_warning_id'         ]->color_set($preset->colors->color_warning_id         );
    $items['#color_error_id'           ]->color_set($preset->colors->color_error_id           );
    $items['#color_fieldset_id'        ]->color_set($preset->colors->color_fieldset_id        );
    $items['#color_fieldset_nested_id' ]->color_set($preset->colors->color_fieldset_nested_id );
    $items['#color_field_id'           ]->color_set($preset->colors->color_field_id           );
    $items['#color_field_text_id'      ]->color_set($preset->colors->color_field_text_id      );
    $items['#color_button_id'          ]->color_set($preset->colors->color_button_id          );
    $items['#color_button_active_id'   ]->color_set($preset->colors->color_button_active_id   );
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $id = page::current_get()->args_get('id');
        $preset = color::preset_get($id);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_page_id',             $preset->colors->color_page_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',             $preset->colors->color_text_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',             $preset->colors->color_main_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',             $preset->colors->color_link_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id',      $preset->colors->color_link_active_id,      false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_odd_id',    $preset->colors->color_table_row_odd_id,    false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_even_id',   $preset->colors->color_table_row_even_id,   false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_relation_id',         $preset->colors->color_relation_id,         false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_id',             $preset->colors->color_menu_id,             false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_active_id',      $preset->colors->color_menu_active_id,      false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_text_id',        $preset->colors->color_menu_text_id,        false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_id',        $preset->colors->color_menu_link_id,        false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id', $preset->colors->color_menu_link_active_id, false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',               $preset->colors->color_ok_id,               false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',          $preset->colors->color_warning_id,          false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',            $preset->colors->color_error_id,            false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_id',         $preset->colors->color_fieldset_id,         false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_nested_id',  $preset->colors->color_fieldset_nested_id,  false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_id',            $preset->colors->color_field_id,            false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_text_id',       $preset->colors->color_field_text_id,       false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_id',           $preset->colors->color_button_id,           false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_active_id',    $preset->colors->color_button_active_id          );
        message::insert('Colors was applied.');
        static::on_init($form, $items);
        break;
    }
  }

}}