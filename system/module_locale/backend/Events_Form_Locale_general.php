<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Language;
use effcore\Message;
use effcore\Module;
use effcore\Storage;

abstract class Events_Form_Locale_general {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('locale');
        $items['#lang_code']->value_set($settings->lang_code);
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = Storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code', $items['#lang_code']->value_get());
                if ($result) {
                    Language::code_set_current($items['#lang_code']->value_get());
                       Message::insert('Changes was saved.'             );
                } else Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $result = Storage::get('data')->changes_delete('locale', 'update', 'settings/locale/lang_code');
                if ($result) {
                    Language::code_set_current('en');
                    static::on_init(null, $form, $items);
                       Message::insert('Changes was deleted.'             );
                } else Message::insert('Changes was not deleted!', 'error');
                break;
        }
    }

}
