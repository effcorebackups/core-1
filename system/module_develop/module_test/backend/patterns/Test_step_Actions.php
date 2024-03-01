<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Actions {

    public $actions;

    function run(&$test, $dpath) {
        foreach ($this->actions as $c_row_id => $c_step) {
            foreach ($c_step->run($test, $dpath.'/'.$c_row_id) as $с_tick) {
                yield $с_tick;
            }
        }
    }

}
