<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\Color_preset;
use effcore\Message;
use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('profile_classic');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = Module::get('profile_classic');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (Module::is_installed('profile_classic')) {
            $result = Color_preset::apply('original_classic');
            if ($result) Message::insert('Color settings have been changed.'             );
            else         Message::insert('Color settings have not been changed!', 'error');
            $module = Module::get('profile_classic');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $result = Color_preset::reset();
        if ($result) Message::insert('Color settings have been changed.'             );
        else         Message::insert('Color settings have not been changed!', 'error');
        $module = Module::get('profile_classic');
        $module->disable();
    }

}
