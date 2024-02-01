<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Title {

    public $title;
    public $args = [];
    public $is_apply_tokens = true;

    function run(&$test, $dpath, &$c_results) {
        $c_results['reports'][$dpath]['dpath'] = '### dpath: '.$dpath;
        $c_results['reports'][$dpath][] = $this->title instanceof Text ?
                                          $this->title->render() :
                                          $this->title;
    }

}
