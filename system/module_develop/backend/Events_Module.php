<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use effcore\Console;
use effcore\Frontend;
use effcore\Module;

abstract class Events_Module {

    static function on_enable($event) {
        $module = Module::get('develop');
        $module->enable();
    }

    static function on_disable($event) {
        $module = Module::get('develop');
        $module->disable();
    }

    static function on_start($event) {
        if (Console::visible_mode_get()) {
            if (!Frontend::select('page_all__console__develop')) {
                 Frontend::insert('page_all__console__develop', (object)['check' => 'url', 'where' => 'path', 'match' => '%^(?!/develop/).*$%'], 'styles', [
                    'path'       => '/system/module_develop/frontend/develop.cssd?page_id=%%_page_id_context',
                    'attributes' => ['rel' => 'stylesheet', 'media' => 'all'],
                    'weight'     => -500], 'console_style', 'develop'
                 );
            }
        }
    }

}
