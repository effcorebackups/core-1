<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Test_step_Actions {

    function run(&$test, $dpath, &$c_results) {
        $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
        foreach ($this->actions as $c_dpath_in_cycle => $c_step) {
            $c_step->run($test, $dpath.'/'.$c_dpath_in_cycle, $c_results);
            if (array_key_exists('return', $c_results)) {
                return;
            }
        }
    }

}
