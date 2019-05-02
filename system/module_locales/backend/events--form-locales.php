<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\locales {
          use \effcore\field_timezone;
          use \effcore\language;
          use \effcore\locale;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\url;
          abstract class events_form_locales {

  static function on_init($form, $items) {
    $settings = module::settings_get('locales');
    $items['#lang_code'          ]->build(); # @todo: make it better
    $items['#lang_code'          ]->value_set($settings->lang_code          );
    $items['#format_date'        ]->value_set($settings->format_date        );
    $items['#format_time'        ]->value_set($settings->format_time        );
    $items['#format_datetime'    ]->value_set($settings->format_datetime    );
    $items['#decimal_point'      ]->value_set($settings->decimal_point      );
    $items['#thousands_separator']->value_set($settings->thousands_separator);
    $items['#timezone_server'    ]->value_set(date_default_timezone_get()   );
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/lang_code',           $items['#lang_code'          ]->value_get(), false);
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/format_date',         $items['#format_date'        ]->value_get(), false);
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/format_time',         $items['#format_time'        ]->value_get(), false);
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/format_datetime',     $items['#format_datetime'    ]->value_get(), false);
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/decimal_point',       $items['#decimal_point'      ]->value_get(), false);
        storage::get('files')->changes_insert('locales', 'update', 'settings/locales/thousands_separator', $items['#thousands_separator']->value_get());
        language::code_set_current($items['#lang_code']->value_get());
        static::on_init($form, $items);
        message::insert('The changes was saved.');
        break;
      case 'restore':
        storage::get('files')->changes_delete('locales', 'update', 'settings/locales/lang_code',       false);
        storage::get('files')->changes_delete('locales', 'update', 'settings/locales/format_date',     false);
        storage::get('files')->changes_delete('locales', 'update', 'settings/locales/format_time',     false);
        storage::get('files')->changes_delete('locales', 'update', 'settings/locales/format_datetime', false);
        storage::get('files')->changes_delete('locales', 'update', 'settings/locales/decimal_point',   false);
        storage::get('files')->changes_delete('locales', 'update', 'settings/locales/thousands_separator');
        language::code_set_current('en');
        static::on_init($form, $items);
        message::insert('The changes was deleted.');
        break;
    }
  }

}}