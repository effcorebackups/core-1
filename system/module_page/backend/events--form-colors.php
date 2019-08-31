<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_colors {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    $items['*color_page_id'            ]->value_set( $settings->color_page_id             );
    $items['*color_text_id'            ]->value_set( $settings->color_text_id             );
    $items['*color_main_id'            ]->value_set( $settings->color_main_id             );
    $items['*color_link_id'            ]->value_set( $settings->color_link_id             );
    $items['*color_link_active_id'     ]->value_set( $settings->color_link_active_id      );
    $items['*color_table_row_odd_id'   ]->value_set( $settings->color_table_row_odd_id    );
    $items['*color_table_row_even_id'  ]->value_set( $settings->color_table_row_even_id   );
    $items['*color_relation_id'        ]->value_set( $settings->color_relation_id         );
    $items['*color_menu_id'            ]->value_set( $settings->color_menu_id             );
    $items['*color_menu_active_id'     ]->value_set( $settings->color_menu_active_id      );
    $items['*color_menu_text_id'       ]->value_set( $settings->color_menu_text_id        );
    $items['*color_menu_link_id'       ]->value_set( $settings->color_menu_link_id        );
    $items['*color_menu_link_active_id']->value_set( $settings->color_menu_link_active_id );
    $items['*color_ok_id'              ]->value_set( $settings->color_ok_id               );
    $items['*color_warning_id'         ]->value_set( $settings->color_warning_id          );
    $items['*color_error_id'           ]->value_set( $settings->color_error_id            );
    $items['*color_fieldset_id'        ]->value_set( $settings->color_fieldset_id         );
    $items['*color_fieldset_nested_id' ]->value_set( $settings->color_fieldset_nested_id  );
    $items['*color_field_id'           ]->value_set( $settings->color_field_id            );
    $items['*color_field_text_id'      ]->value_set( $settings->color_field_text_id       );
    $items['*color_button_id'          ]->value_set( $settings->color_button_id           );
    $items['*color_button_active_id'   ]->value_set( $settings->color_button_active_id    );
    $items['*color_button_text_id'     ]->value_set( $settings->color_button_text_id      );
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_page_id',             $items['*color_page_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_text_id',             $items['*color_text_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_main_id',             $items['*color_main_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_id',             $items['*color_link_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_link_active_id',      $items['*color_link_active_id'     ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_odd_id',    $items['*color_table_row_odd_id'   ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_table_row_even_id',   $items['*color_table_row_even_id'  ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_relation_id',         $items['*color_relation_id'        ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_id',             $items['*color_menu_id'            ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_active_id',      $items['*color_menu_active_id'     ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_text_id',        $items['*color_menu_text_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_id',        $items['*color_menu_link_id'       ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id', $items['*color_menu_link_active_id']->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_ok_id',               $items['*color_ok_id'              ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_warning_id',          $items['*color_warning_id'         ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_error_id',            $items['*color_error_id'           ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_id',         $items['*color_fieldset_id'        ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_fieldset_nested_id',  $items['*color_fieldset_nested_id' ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_id',            $items['*color_field_id'           ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_field_text_id',       $items['*color_field_text_id'      ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_id',           $items['*color_button_id'          ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_active_id',    $items['*color_button_active_id'   ]->value_get(), false);
        storage::get('files')->changes_insert('page', 'update', 'settings/page/color_button_text_id',      $items['*color_button_text_id'     ]->value_get()       );
        message::insert('The changes was saved.');
        break;
      case 'reset':
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_page_id',             false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_text_id',             false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_main_id',             false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_link_id',             false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_link_active_id',      false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_table_row_odd_id',    false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_table_row_even_id',   false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_relation_id',         false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_id',             false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_active_id',      false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_text_id',        false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_link_id',        false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_menu_link_active_id', false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_ok_id',               false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_warning_id',          false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_error_id',            false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_fieldset_id',         false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_fieldset_nested_id',  false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_field_id',            false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_field_text_id',       false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_button_id',           false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_button_active_id',    false);
        storage::get('files')->changes_delete('page', 'update', 'settings/page/color_button_text_id'            );
        message::insert('The changes was deleted.');
        static::on_init(null, $form, $items);
        break;
    }
  }

}}