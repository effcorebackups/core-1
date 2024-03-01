<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use effcore\Test;

#[\AllowDynamicProperties]

class Test_step_Return {

    public $value;

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        if     ($this->value === Test::SUCCESSFUL) yield new Text('return = "%%_return"', ['return' => 'true']);
        elseif ($this->value === Test::FAILED    ) yield new Text('return = "%%_return"', ['return' => 'false']);
        else                                       yield new Text('return = "%%_return"', ['return' => $this->value]);
        yield $this->value;
    }

}
