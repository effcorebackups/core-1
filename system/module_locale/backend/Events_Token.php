<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Translation;

abstract class Events_Token {

    static function on_apply($name, $args) {
        switch ($name) {
            case 'return_translation':
                if ($args->get_count() === 1) return Translation::apply($args->get(0));
                if ($args->get_count()  >  1) return Translation::apply($args->get(0), $args->get_named_all());
                break;
        }
    }

}
