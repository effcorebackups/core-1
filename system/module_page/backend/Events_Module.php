<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Form;
use effcore\Module;
use effcore\Page;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('page');
        $module->install();
    }

    static function on_enable($event) {
        if (Module::is_installed('page')) {
            $module = Module::get('page');
            $module->enable();
        }
    }

    static function on_start($event) {
        return Page::init_current()->render();
    }

    static function on_cron_run($event) {
        Form::validation_cleaning();
    }

}
