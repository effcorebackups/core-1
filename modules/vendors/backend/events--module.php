<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\vendors;

use effcore\module;

abstract class events_module {

    static function on_enable($event) {
        $module = module::get('vendors');
        $module->enable();
    }

    static function on_disable($event) {
        $module = module::get('vendors');
        $module->disable();
    }

}
