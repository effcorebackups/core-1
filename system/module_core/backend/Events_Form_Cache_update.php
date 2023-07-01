<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Cache;
use effcore\Message;

abstract class Events_Form_Cache_update {

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'update':
                Cache::update_global();
                Message::insert('All caches was reset.');
                break;
        }
    }

}
