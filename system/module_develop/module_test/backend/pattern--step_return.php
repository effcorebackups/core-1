<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class step_return {

    public $value;

    function run(&$test, $dpath, &$c_results) {
        $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
        $c_results['reports'][$dpath][] = new text('return = "%%_return"', ['return' => $this->value]);
        $c_results['return'] = $this->value;
    }

}
