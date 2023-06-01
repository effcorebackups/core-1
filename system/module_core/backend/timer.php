<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class timer {

    static protected $data;

    static function tap($name) {
        static::$data[$name][] = microtime(true);
    }

    static function period_get($name, $a, $b, $format = true) {
        if ($a < 0) $a = count(static::$data[$name]) + $a;
        if ($b < 0) $b = count(static::$data[$name]) + $b;
        $result = abs(static::$data[$name][$b] -
                      static::$data[$name][$a]);
        if ($format)
             return number_format($result, 6);
        else return               $result;
    }

}
