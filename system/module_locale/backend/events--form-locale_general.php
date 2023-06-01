<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\language;
use effcore\message;
use effcore\module;
use effcore\storage;

abstract class events_form_locale_general {

    static function on_init($event, $form, $items) {
        $settings = module::settings_get('locale');
        $items['#lang_code'      ]->value_set($settings->lang_code);
        $items['#timezone_server']->value_set(date_default_timezone_get());
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code', $items['#lang_code']->value_get());
                if ($result) language::code_set_current($items['#lang_code']->value_get());
                if ($result) message::insert('Changes was saved.'             );
                else         message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = storage::get('data')->changes_delete('locale', 'update', 'settings/locale/lang_code');
                if ($result) language::code_set_current('en');
                if ($result) static::on_init(null, $form, $items);
                if ($result) message::insert('Changes was deleted.'             );
                else         message::insert('Changes was not deleted!', 'error');
                break;
        }
    }

}
