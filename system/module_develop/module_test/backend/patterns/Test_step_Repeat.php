<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Repeat {

    public $id;
    public $quantity = 1;
    public $actions = [];

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        $quantity = $this->quantity instanceof Param_from_form ?
                    $this->quantity->get() :
                    $this->quantity;
        for ($i = 1; $i <= $quantity; $i++) {
            yield Test_message::send_dpath($dpath.'/i:'.$i);
            yield new Text('repeat %%_cur from %%_max', ['cur' => $i, 'max' => $quantity]);
            foreach ($this->actions as $c_row_id => $c_action) {
                if ($this->id) Token::insert('test_step__repeat_i__'.$this->id, 'text', $i, null, 'test');
                foreach ($c_action->run($test, $dpath.'/i:'.$i.'/'.$c_row_id) as $с_tick) {
                    yield $с_tick;
                }
            }
        }
    }

}
