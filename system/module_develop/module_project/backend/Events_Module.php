<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\project;

use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('project');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = Module::get('project');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (Module::is_installed('project')) {
            $module = Module::get('project');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = Module::get('project');
        $module->disable();
    }

}
