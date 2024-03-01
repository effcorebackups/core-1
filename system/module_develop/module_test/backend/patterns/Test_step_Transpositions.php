<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Transpositions {

    public $actions_before;
    public $actions = [];
    public $actions_after;

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        $row_ids = array_keys($this->actions);
        $actions_count = count($this->actions);
        $max = 2 ** $actions_count;
        for ($i = 1; $i < $max; $i++) {
            $c_bits = str_pad(decbin($i), $actions_count, '0', STR_PAD_LEFT);
            $c_total_state = str_replace(['0', '1'], ['□', '▣'], $c_bits);

            yield new Text_simple('');
            yield Test_message::send_dpath($dpath.'/i:'.$i);
            yield new Text('TRANSPOSITION %%_cur FROM %%_max | %%_state', ['cur' => $i + 1, 'max' => $max, 'state' => $c_total_state]);

            # insert dynamic tokens
            for ($j = $actions_count - 1; $j >= 0; $j--) {
                $c_row_id = $row_ids[$actions_count - 1 - $j];
                $c_state = $i >> $j & 1;
                Token::insert('test_step__transpositions__is_active__'.$c_row_id, 'text', $c_state, null, 'test');
            }

            # run "actions_before"
            if ($this->actions_before) {
                yield new Text_simple('');
                yield Test_message::send_dpath($dpath.'/i:'.$i.'/actions_before');
                yield new Text('action "%%_name" will be started', ['name' => 'actions_before']);
                foreach ($this->actions_before as $c_row_id => $c_action) {
                    foreach ($c_action->run($test, $dpath.'/i:'.$i.'/actions_before/'.$c_row_id) as $c_tick) {
                        yield $c_tick;
                    }
                }
            }

            # run each "action"
            if ($this->actions) {
                yield new Text_simple('');
                yield Test_message::send_dpath($dpath.'/i:'.$i.'/actions');
                yield new Text('action "%%_name" will be started', ['name' => 'actions']);
                for ($j = $actions_count - 1; $j >= 0; $j--) {
                    if ($i >> $j & 1) {
                        $c_row_id = $row_ids[$actions_count - 1 - $j];
                        yield new Text_simple('');
                        yield Test_message::send_dpath($dpath.'/i:'.$i.'/actions/j:'.$j);
                        foreach ($this->actions[$c_row_id]->run($test, $dpath.'/i:'.$i.'/actions/j:'.$j.'/'.$c_row_id) as $c_tick) {
                            yield $c_tick;
                        }
                    }
                }
            }

            # run "actions_after"
            if ($this->actions_after) {
                yield new Text_simple('');
                yield Test_message::send_dpath($dpath.'/i:'.$i.'/actions_after');
                yield new Text('action "%%_name" will be started', ['name' => 'actions_after']);
                foreach ($this->actions_after as $c_row_id => $c_action) {
                    foreach ($c_action->run($test, $dpath.'/'.$i.'/actions_after/'.$c_row_id) as $c_tick) {
                        yield $c_tick;
                    }
                }
            }
        }
    }

}
