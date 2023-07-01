<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\captcha;

use effcore\Captcha;
use effcore\Module;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('captcha');
        $module->install();
    }

    static function on_uninstall($event) {
        $module = Module::get('captcha');
        $module->uninstall();
    }

    static function on_enable($event) {
        if (Module::is_installed('captcha')) {
            $module = Module::get('captcha');
            $module->enable();
        }
    }

    static function on_disable($event) {
        $module = Module::get('captcha');
        $module->disable();
    }

    static function on_cron_run($event) {
        Captcha::cleaning();
    }

}
