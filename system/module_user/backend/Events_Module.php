<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Instance;
use effcore\Module;
use effcore\Request;
use effcore\Session;
use effcore\Storage;
use effcore\User;

abstract class Events_Module {

    static function on_install($event) {
        $module = Module::get('user');
        $module->install();
        if (count(Storage::get('sql')->errors) === 0) {
            $admin = new Instance('user', ['nickname' => 'Admin']);
            if ($admin->select()) {
                $admin->password_hash = User::password_hash(Request::value_get('password'));
                $admin->email         =                     Request::value_get('email'   );
                $admin->timezone      =                     Request::value_get('timezone');
                $admin->update();
            }
        }
    }

    static function on_enable($event) {
        if (Module::is_installed('user')) {
            $module = Module::get('user');
            $module->enable();
        }
    }

    static function on_cron_run($event) {
        Session::cleaning();
    }

}
