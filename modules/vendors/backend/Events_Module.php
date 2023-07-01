<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\vendors;

use effcore\Module;

abstract class Events_Module {

    static function on_enable($event) {
        $module = Module::get('vendors');
        $module->enable();
    }

    static function on_disable($event) {
        $module = Module::get('vendors');
        $module->disable();
    }

}
