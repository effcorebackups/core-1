<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('menu');
        $module->install();
    }

    static function on_enable($event) {
        if (Module::is_installed('menu')) {
            $module = Module::get('menu');
            $module->enable();
        }
    }

}
