<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use effcore\Console;
use effcore\Message;
use effcore\Module;

abstract class Events_Form_Console {

    static function on_init($event, $form, $items) {
        $settings = Module::settings_get('page');
        $items['#visibility']->value_set($settings->console_visibility);
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                if ($items['#visibility']->value_get() === Console::IS_VISIBLE_FOR_NOBODY)
                     $result = Console::changes_store(null);
                else $result = Console::changes_store($items['#visibility']->value_get());
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                Console::visible_mode_get(true);
                break;
        }
    }

}
