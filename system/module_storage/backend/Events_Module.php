<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('storage');
        $module->install();
    }

    static function on_enable($event) {
        if (Module::is_installed('storage')) {
            $module = Module::get('storage');
            $module->enable();
        }
    }

}
