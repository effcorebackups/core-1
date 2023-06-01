<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\profile_default;

use effcore\module;

abstract class events_module {

    static function on_install($event) {
        $module = module::get('profile_default');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = module::get('profile_default');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (module::is_installed('profile_default')) {
            $module = module::get('profile_default');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = module::get('profile_default');
        $module->disable();
    }

}
