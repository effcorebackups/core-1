<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Message;
use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('core');
        $module->install();
    }

    static function on_enable($event) {
        if (Module::is_installed('core')) {
            $module = Module::get('core');
            $module->enable();
        }
    }

    static function on_cron_run($event) {
        Message::cleaning();
    }

}
