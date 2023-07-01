<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\poll;

use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('poll');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = Module::get('poll');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (Module::is_installed('poll')) {
            $module = Module::get('poll');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = Module::get('poll');
        $module->disable();
    }

}
