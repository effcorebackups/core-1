<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\translation_be;

use effcore\Language;
use effcore\Message;
use effcore\Module;
use effcore\Page;
use effcore\Storage;

abstract class Events_Module {

    static function on_install($event) {
        if ( (Page::get_current()->id === 'install' && Language::code_get_current() === 'be') ||
             (Page::get_current()->id !== 'install') ) {
            $module = Module::get('translation_be');
            $module->install();
        }
    }

    static function on_uninstall($event) {
        $module = Module::get('translation_be');
        $module->uninstall();
        Storage::get('data')->changes_delete('locale', 'update', 'settings/locale/formats/be');
    }

    static function on_enable($event) {
        if ( (Page::get_current()->id === 'install' && Language::code_get_current() === 'be') ||
             (Page::get_current()->id !== 'install') ) {
            $module = Module::get('translation_be');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = Module::get('translation_be');
        $module->disable();
        if (Language::code_get_current() === 'be') {
            $result = Storage::get('data')->changes_insert('locale', 'update', 'settings/locale/lang_code', 'en');
            if ($result) Language::code_set_current('en');
            if ($result) Message::insert('Language settings have been changed.'             );
            else         Message::insert('Language settings have not been changed!', 'error');
        }
    }

}
