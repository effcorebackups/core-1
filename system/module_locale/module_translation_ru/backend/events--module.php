<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\translation_ru;

use effcore\language;
use effcore\message;
use effcore\module;
use effcore\page;
use effcore\storage;

abstract class events_module {

    static function on_install($event) {
        if ( (page::get_current()->id === 'install' && language::code_get_current() === 'ru') ||
             (page::get_current()->id !== 'install') ) {
            $module = module::get('translation_ru');
            $module->install();
        }
    }

    static function on_uninstall($event) {
        $module = module::get('translation_ru');
        $module->uninstall();
        storage::get('data')->changes_delete('locale', 'update', 'settings/locale/formats/ru');
    }

    static function on_enable($event) {
        if ( (page::get_current()->id === 'install' && language::code_get_current() === 'ru') ||
             (page::get_current()->id !== 'install') ) {
            $module = module::get('translation_ru');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = module::get('translation_ru');
        $module->disable();
        if (language::code_get_current() === 'ru') {
            $result = storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code', 'en');
            if ($result) language::code_set_current('en');
            if ($result) message::insert('Language settings have been changed.'             );
            else         message::insert('Language settings have not been changed!', 'error');
        }
    }

}
