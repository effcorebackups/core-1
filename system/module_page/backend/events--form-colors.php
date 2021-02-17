<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color_preset;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_colors {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('page');
    $items['*color_page_id'                  ]->value_set( $settings->color_page_id                   );
    $items['*color_text_id'                  ]->value_set( $settings->color_text_id                   );
    $items['*color_main_id'                  ]->value_set( $settings->color_main_id                   );
    $items['*color_link_id'                  ]->value_set( $settings->color_link_id                   );
    $items['*color_link_active_id'           ]->value_set( $settings->color_link_active_id            );
    $items['*color_table_row_odd_id'         ]->value_set( $settings->color_table_row_odd_id          );
    $items['*color_table_row_even_id'        ]->value_set( $settings->color_table_row_even_id         );
    $items['*color_relation_id'              ]->value_set( $settings->color_relation_id               );
    $items['*color_menu_id'                  ]->value_set( $settings->color_menu_id                   );
    $items['*color_menu_active_id'           ]->value_set( $settings->color_menu_active_id            );
    $items['*color_menu_text_id'             ]->value_set( $settings->color_menu_text_id              );
    $items['*color_menu_link_id'             ]->value_set( $settings->color_menu_link_id              );
    $items['*color_menu_link_active_id'      ]->value_set( $settings->color_menu_link_active_id       );
    $items['*color_tabs_id'                  ]->value_set( $settings->color_tabs_id                   );
    $items['*color_tabs_link_id'             ]->value_set( $settings->color_tabs_link_id              );
    $items['*color_tabs_link_active_id'      ]->value_set( $settings->color_tabs_link_active_id       );
    $items['*color_tabs_link_active_no_bg_id']->value_set( $settings->color_tabs_link_active_no_bg_id );
    $items['*color_ok_id'                    ]->value_set( $settings->color_ok_id                     );
    $items['*color_warning_id'               ]->value_set( $settings->color_warning_id                );
    $items['*color_error_id'                 ]->value_set( $settings->color_error_id                  );
    $items['*color_fieldset_id'              ]->value_set( $settings->color_fieldset_id               );
    $items['*color_fieldset_nested_id'       ]->value_set( $settings->color_fieldset_nested_id        );
    $items['*color_field_id'                 ]->value_set( $settings->color_field_id                  );
    $items['*color_field_text_id'            ]->value_set( $settings->color_field_text_id             );
    $items['*color_button_id'                ]->value_set( $settings->color_button_id                 );
    $items['*color_button_active_id'         ]->value_set( $settings->color_button_active_id          );
    $items['*color_button_text_id'           ]->value_set( $settings->color_button_text_id            );
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $result = true;
        $storage = storage::get('files');
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_page_id',                   $items['*color_page_id'                  ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_text_id',                   $items['*color_text_id'                  ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_main_id',                   $items['*color_main_id'                  ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_link_id',                   $items['*color_link_id'                  ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_link_active_id',            $items['*color_link_active_id'           ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_table_row_odd_id',          $items['*color_table_row_odd_id'         ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_table_row_even_id',         $items['*color_table_row_even_id'        ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_relation_id',               $items['*color_relation_id'              ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_menu_id',                   $items['*color_menu_id'                  ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_menu_active_id',            $items['*color_menu_active_id'           ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_menu_text_id',              $items['*color_menu_text_id'             ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_menu_link_id',              $items['*color_menu_link_id'             ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_menu_link_active_id',       $items['*color_menu_link_active_id'      ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_tabs_id',                   $items['*color_tabs_id'                  ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_tabs_link_id',              $items['*color_tabs_link_id'             ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_tabs_link_active_id',       $items['*color_tabs_link_active_id'      ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_tabs_link_active_no_bg_id', $items['*color_tabs_link_active_no_bg_id']->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_ok_id',                     $items['*color_ok_id'                    ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_warning_id',                $items['*color_warning_id'               ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_error_id',                  $items['*color_error_id'                 ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_fieldset_id',               $items['*color_fieldset_id'              ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_fieldset_nested_id',        $items['*color_fieldset_nested_id'       ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_field_id',                  $items['*color_field_id'                 ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_field_text_id',             $items['*color_field_text_id'            ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_button_id',                 $items['*color_button_id'                ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_button_active_id',          $items['*color_button_active_id'         ]->value_get(), false);
        $result&= $storage->changes_insert('page', 'update', 'settings/page/color_button_text_id',            $items['*color_button_text_id'           ]->value_get()       );
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