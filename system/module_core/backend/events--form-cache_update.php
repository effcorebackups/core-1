<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\cache;
use effcore\message;

abstract class events_form_cache_update {

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'update':
                cache::update_global();
                message::insert('All caches was reset.');
                break;
        }
    }

}
