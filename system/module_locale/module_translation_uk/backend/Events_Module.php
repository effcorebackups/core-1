<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\translation_uk;

use effcore\Language;
use effcore\Locale;
use effcore\Message;
use effcore\Module;
use effcore\Page;

abstract class Events_Module {

    static function on_install($event) {
        if ( (Page::get_current()->id === 'install' && Language::code_get_current() === 'uk') ||
             (Page::get_current()->id !== 'install') ) {
            $module = Module::get('translation_uk');
            $module->install();
        }
    }

    static function on_uninstall($event) {
        $module = Module::get('translation_uk');
        $module->uninstall();
        Locale::changes_store(['formats' => ['uk' => null]]);
    }

    static function on_enable($event) {
        if ( (Page::get_current()->id === 'install' && Language::code_get_current() === 'uk') ||
             (Page::get_current()->id !== 'install') ) {
            $module = Module::get('translation_uk');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = Module::get('translation_uk');
        $module->disable();
        if (Language::code_get_current() === 'uk') {
            $result = Locale::changes_store(['lang_code' => null]);
            if ($result) Language::code_set_current('en');
            if ($result) Message::insert('Language settings have been changed.'             );
            else         Message::insert('Language settings have not been changed!', 'error');
        }
    }

}
