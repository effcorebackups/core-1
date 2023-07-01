<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\profile_default;

use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('profile_default');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = Module::get('profile_default');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (Module::is_installed('profile_default')) {
            $module = Module::get('profile_default');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = Module::get('profile_default');
        $module->disable();
    }

}
