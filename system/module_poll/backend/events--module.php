<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\poll;

use effcore\module;

abstract class events_module {

    static function on_install($event) {
        $module = module::get('poll');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = module::get('poll');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (module::is_installed('poll')) {
            $module = module::get('poll');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = module::get('poll');
        $module->disable();
    }

}
