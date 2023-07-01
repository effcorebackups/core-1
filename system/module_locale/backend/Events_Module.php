<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Module;

abstract class Events_Module {

    static function on_enable($event) {
        $module = Module::get('locale');
        $module->enable();
    }

}
