<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\message;
          use \effcore\page;
          use \effcore\storage;
          abstract class events_form_colors_preset {

  static function on_init($event, $form, $items) {
    $id = page::get_current()->args_get('id');
    $preset = color::preset_get($id);
    if ($preset) {
      $items['#color_page_id'                  ]->color_set($preset->colors->color_page_id                  );
      $items['#color_text_id'                  ]->color_set($preset->colors->color_text_id                  );
      $items['#color_main_id'                  ]->color_set($preset->colors->color_main_id                  );
      $items['#color_link_id'                  ]->color_set($preset->colors->color_link_id                  );
      $items['#color_link_active_id'           ]->color_set($preset->colors->color_link_active_id           );
      $items['#color_table_row_odd_id'         ]->color_set($preset->colors->color_table_row_odd_id         );
      $items['#color_table_row_even_id'        ]->color_set($preset->colors->color_table_row_even_id        );
      $items['#color_relation_id'              ]->color_set($preset->colors->color_relation_id              );
      $items['#color_menu_id'                  ]->color_set($preset->colors->color_menu_id                  );
      $items['#color_menu_active_id'           ]->color_set($preset->colors->color_menu_active_id           );
      $items['#color_menu_text_id'             ]->color_set($preset->colors->color_menu_text_id             );
      $items['#color_menu_link_id'             ]->color_set($preset->colors->color_menu_link_id             );
      $items['#color_menu_link_active_id'      ]->color_set($preset->colors->color_menu_link_active_id      );
      $items['#color_tabs_id'                  ]->color_set($preset->colors->color_tabs_id                  );
      $items['#color_tabs_link_id'             ]->color_set($preset->colors->color_tabs_link_id             );
      $items['#color_tabs_link_active_id'      ]->color_set($preset->colors->color_tabs_link_active_id      );
      $items['#color_tabs_link_active_no_bg_id']->color_set($preset->colors->color_tabs_link_active_no_bg_id);
      $items['#color_ok_id'                    ]->color_set($preset->colors->color_ok_id                    );
      $items['#color_warning_id'               ]->color_set($preset->colors->color_warning_id               );
      $items['#color_error_id'                 ]->color_set($preset->colors->color_error_id                 );
      $items['#color_fieldset_id'              ]->color_set($preset->colors->color_fieldset_id              );
      $items['#color_fieldset_nested_id'       ]->color_set($preset->colors->color_fieldset_nested_id       );
      $items['#color_field_id'                 ]->color_set($preset->colors->color_field_id                 );
      $items['#color_field_text_id'            ]->color_set($preset->colors->color_field_text_id            );
      $items['#color_button_id'                ]->color_set($preset->colors->color_button_id                );
      $items['#color_button_active_id'         ]->color_set($preset->colors->color_button_active_id         );
      $items['#color_button_text_id'           ]->color_set($preset->colors->color_button_text_id           );
    } else $items['~apply']->disabled_set(true);
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $id = page::get_current()->args_get('id');
        $preset = color::preset_get($id);
        if ($preset) {
          $result = true;
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_page_id',                   $preset->colors->color_page_id,                   false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',                   $preset->colors->color_text_id,                   false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',                   $preset->colors->color_main_id,                   false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',                   $preset->colors->color_link_id,                   false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id',            $preset->colors->color_link_active_id,            false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_odd_id',          $preset->colors->color_table_row_odd_id,          false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_even_id',         $preset->colors->color_table_row_even_id,         false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_relation_id',               $preset->colors->color_relation_id,               false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_id',                   $preset->colors->color_menu_id,                   false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_active_id',            $preset->colors->color_menu_active_id,            false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_text_id',              $preset->colors->color_menu_text_id,              false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_id',              $preset->colors->color_menu_link_id,              false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id',       $preset->colors->color_menu_link_active_id,       false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_id',                   $preset->colors->color_tabs_id,                   false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_link_id',              $preset->colors->color_tabs_link_id,              false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_link_active_id',       $preset->colors->color_tabs_link_active_id,       false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_tabs_link_active_no_bg_id', $preset->colors->color_tabs_link_active_no_bg_id, false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',                     $preset->colors->color_ok_id,                     false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',                $preset->colors->color_warning_id,                false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',                  $preset->colors->color_error_id,                  false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_id',               $preset->colors->color_fieldset_id,               false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_nested_id',        $preset->colors->color_fieldset_nested_id,        false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_id',                  $preset->colors->color_field_id,                  false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_text_id',             $preset->colors->color_field_text_id,             false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_id',                 $preset->colors->color_button_id,                 false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_active_id',          $preset->colors->color_button_active_id,          false);
          $result&= storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_text_id',            $preset->colors->color_button_text_id                  );
          if ($result) message::insert('Colors was applied.'             );
          else         message::insert('Colors was not applied!', 'error');
          static::on_init(null, $form, $items);
        }
        break;
    }
  }

}}