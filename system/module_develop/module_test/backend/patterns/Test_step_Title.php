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

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        yield $this->title instanceof Text ?
              $this->title : new Text($this->title);
    }

}
