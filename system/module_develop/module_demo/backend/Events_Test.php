<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\Text;

abstract class Events_Test {

    static function test_step_code__demo_handler(&$test, $dpath, &$c_results, $param_1, $param_2, $param_3, $current_iteration) {
        $reports[] = new Text('%%_param = "%%_value"', ['param' => 'param_1', 'value' => $param_1]);
        $reports[] = new Text('%%_param = "%%_value"', ['param' => 'param_2', 'value' => $param_2]);
        $reports[] = new Text('%%_param = "%%_value"', ['param' => 'param_3', 'value' => $param_3]);
        $reports[] = new Text('%%_param = "%%_value"', ['param' => 'current_iteration', 'value' => $current_iteration]);
        $c_results['reports'][$dpath] = $reports;
    }

}
