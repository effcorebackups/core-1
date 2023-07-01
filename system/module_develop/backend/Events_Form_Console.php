<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use effcore\Console;
use effcore\Message;
use effcore\Module;
use effcore\Storage;

abstract class Events_Form_Console {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('page');
        $items['#visibility']->value_set($settings->console_visibility);
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $result = Storage::get('data')->changes_insert('page', 'update', 'settings/page/console_visibility', $items['#visibility']->value_get());
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                Console::init(true);
                break;
        }
    }

}
