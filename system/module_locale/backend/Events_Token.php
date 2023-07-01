<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\locale;

use effcore\Translation;

abstract class Events_Token {

    static function on_apply($name, $args = []) {
        switch ($name) {
            case 'return_translation':
                if (count($args) === 1) return Translation::apply($args[0]);
                if (count($args)  >  1) {
                    $real_args = [];
                    for ($i = 1; $i < count($args); $i++) {
                        $c_result = explode('=', $args[$i]);
                        if (count($c_result) === 2)
                            $real_args[$c_result[0]] =
                                       $c_result[1]; }
                    return Translation::apply($args[0], $real_args);
                }
                break;
        }
    }

}
