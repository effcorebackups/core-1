<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\Color_profile;
use effcore\Message;
use effcore\Module;

abstract class Events_Module {

    const COLOR_PROFILE = 'classic_violet';

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
            $result = Color_profile::set_current(static::COLOR_PROFILE);
            if ($result) Message::insert('Color profile was activated.'             );
            else         Message::insert('Color profile was not activated!', 'error');
            $module = Module::get('profile_classic');
            $module->enable();
        }
    }

    static function on_disable($event) {
        if (Color_profile::get_current()->id === static::COLOR_PROFILE) {
            $result = Color_profile::set_current(Color_profile::PROFILE_DEFAULT);
            if ($result) Message::insert('Default color profile was activated.'             );
            else         Message::insert('Default color profile was not activated!', 'error');
        }
        $module = Module::get('profile_classic');
        $module->disable();
    }

}
