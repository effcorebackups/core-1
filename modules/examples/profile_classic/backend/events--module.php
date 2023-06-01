<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\color_preset;
use effcore\message;
use effcore\module;

abstract class events_module {

    static function on_install($event) {
        $module = module::get('profile_classic');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = module::get('profile_classic');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (module::is_installed('profile_classic')) {
            $result = color_preset::apply('original_classic');
            if ($result) message::insert('Color settings have been changed.'             );
            else         message::insert('Color settings have not been changed!', 'error');
            $module = module::get('profile_classic');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $result = color_preset::reset();
        if ($result) message::insert('Color settings have been changed.'             );
        else         message::insert('Color settings have not been changed!', 'error');
        $module = module::get('profile_classic');
        $module->disable();
    }

}
