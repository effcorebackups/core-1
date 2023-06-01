<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\form;
use effcore\module;
use effcore\page;

abstract class events_module {

    static function on_install($event) {
        $module = module::get('page');
        $module->install();
    }

    static function on_enable($event) {
        if (module::is_installed('page')) {
            $module = module::get('page');
            $module->enable();
        }
    }

    static function on_start($event) {
        return page::init_current()->render();
    }

    static function on_cron_run($event) {
        form::validation_cleaning();
    }

}
