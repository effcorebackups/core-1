<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Return {

    public $value;

    function run(&$test, $dpath, &$c_results) {
        $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
        $c_results['reports'][$dpath][] = new Text('return = "%%_return"', ['return' => $this->value]);
        $c_results['return'] = $this->value;
    }

}
