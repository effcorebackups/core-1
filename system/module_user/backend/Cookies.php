<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Cookies {

    static function parse($string) {
        $result = [];
        foreach (explode('; ', $string) as $c_part) {
            $c_matches = [];
            preg_match('%^(?<name>[^=]+)='.
                         '(?<value>.*)$%S', $c_part, $c_matches);
            if ($c_matches)
                $result[$c_matches['name']] =
                        $c_matches['value'];
        }
        return $result;
    }

    static function render($data = []) {
        $result = [];
        foreach ($data as $c_key => $c_value)
            $result[]= $c_key.'='.$c_value;
        return implode('; ', $result);
    }

}
