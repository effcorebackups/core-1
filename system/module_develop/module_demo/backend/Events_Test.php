<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\Text;
use effcore\Token;

abstract class Events_Test {

    static function test_step_code__demo_handler(&$test, $dpath, $param_1, $param_2, $param_3, $current_iteration) {
        yield new Text('%%_param = "%%_value"', ['param' => 'param_1', 'value' => $param_1]);
        yield new Text('%%_param = "%%_value"', ['param' => 'param_2', 'value' => $param_2]);
        yield new Text('%%_param = "%%_value"', ['param' => 'param_3', 'value' => $param_3]);
        yield new Text('%%_param = "%%_value"', ['param' => 'current_iteration', 'value' => $current_iteration]);
        Token::insert('test_step_code__demo_handler__value', 'text', 'value from handler', null, 'demo');
    }

}
