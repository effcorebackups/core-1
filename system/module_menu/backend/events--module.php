<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\module;

abstract class events_module {

    static function on_install($event) {
        $module = module::get('menu');
        $module->install();
    }

    static function on_enable($event) {
        if (module::is_installed('menu')) {
            $module = module::get('menu');
            $module->enable();
        }
    }

}
