<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Translation;

abstract class Events_Token {

    static function on_apply($name, $args) {
        switch ($name) {
            case 'translation':
                $code = $args->get(-1) && strlen($args->get(-1)) === 2 ?
                        $args->get(-1) : '';
                if ($args->get_count() === 1                ) return Translation::apply($args->get(0));
                if ($args->get_count()  >  1 && $code === '') return Translation::apply($args->get(0), $args->get_named_all());
                if ($args->get_count()  >  1 && $code !== '') return Translation::apply($args->get(0), $args->get_named_all(), $code);
                break;
        }
    }

}
