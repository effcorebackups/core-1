<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locale {
          use \effcore\language;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          abstract class events_form_locale {

  static function on_init($event, $form, $items) {
    $settings = module::settings_get('locale');
    $items['#lang_code'          ]->value_set($settings->lang_code          );
    $items['#format_date'        ]->value_set($settings->format_date        );
    $items['#format_time'        ]->value_set($settings->format_time        );
    $items['#format_datetime'    ]->value_set($settings->format_datetime    );
    $items['#decimal_point'      ]->value_set($settings->decimal_point      );
    $items['#thousands_separator']->value_set($settings->thousands_separator);
    $items['#timezone_server'    ]->value_set(date_default_timezone_get()   );
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $result = true;
        $result&= storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code',           $items['#lang_code'          ]->value_get(), false);
        $result&= storage::get('data')->changes_insert('locale', 'update', 'settings/locale/format_date',         $items['#format_date'        ]->value_get(), false);
        $result&= storage::get('data')->changes_insert('locale', 'update', 'settings/locale/format_time',         $items['#format_time'        ]->value_get(), false);
        $result&= storage::get('data')->changes_insert('locale', 'update', 'settings/locale/format_datetime',     $items['#format_datetime'    ]->value_get(), false);
        $result&= storage::get('data')->changes_insert('locale', 'update', 'settings/locale/decimal_point',       $items['#decimal_point'      ]->value_get(), false);
        $result&= storage::get('data')->changes_insert('locale', 'update', 'settings/locale/thousands_separator', $items['#thousands_separator']->value_get());
        if ($result) message::insert('Changes was saved.'             );
        else         message::insert('Changes was not saved!', 'error');
        if ($result) {
          language::code_set_current($items['#lang_code']->value_get());
        }
        break;
      case 'reset':
        $result = true;
        $result&= storage::get('data')->changes_delete('locale', 'update', 'settings/locale/lang_code',       false);
        $result&= storage::get('data')->changes_delete('locale', 'update', 'settings/locale/format_date',     false);
        $result&= storage::get('data')->changes_delete('locale', 'update', 'settings/locale/format_time',     false);
        $result&= storage::get('data')->changes_delete('locale', 'update', 'settings/locale/format_datetime', false);
        $result&= storage::get('data')->changes_delete('locale', 'update', 'settings/locale/decimal_point',   false);
        $result&= storage::get('data')->changes_delete('locale', 'update', 'settings/locale/thousands_separator');
        if ($result) message::insert('Changes was deleted.'             );
        else         message::insert('Changes was not deleted!', 'error');
        if ($result) {
          language::code_set_current('en');
          static::on_init(null, $form, $items);
        }
        break;
    }
  }

}}