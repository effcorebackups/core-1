<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Test_step_Check {

    public $check;
    public $where;
    public $match;
    public $on_success;
    public $on_failure;

    function run(&$test, $dpath) {
        yield new Text_simple('');
        yield Test_message::send_dpath($dpath);
        if ($this->check === 'token') {
            $is_regexp = is_string($this->match) && strlen($this->match) && $this->match[0] === '%';
            $where = Token::apply((string)$this->where);
            $match = Token::apply((string)$this->match);
            if ($is_regexp) $result = (bool)preg_match($match,    $where);
            else            $result =                  $match === $where;
            if ($result === true) { yield new Text('checking on "%%_operand_1" %%_operator "%%_operand_2"', ['operand_1' => $this->where, 'operand_2' => $this->match, 'operator' => $is_regexp ? '≈' : '=']); yield new Text('real checking on "%%_operand_real_1" %%_operator "%%_operand_real_2"', ['operand_real_1' => $where, 'operand_real_2' => $match, 'operator' => $is_regexp ? '≈' : '=']); yield new Text('result of checking is = "%%_result"', ['result' => (new Text('success'))->render()]); }
            if ($result !== true) { yield new Text('checking on "%%_operand_1" %%_operator "%%_operand_2"', ['operand_1' => $this->where, 'operand_2' => $this->match, 'operator' => $is_regexp ? '≈' : '=']); yield new Text('real checking on "%%_operand_real_1" %%_operator "%%_operand_real_2"', ['operand_real_1' => $where, 'operand_real_2' => $match, 'operator' => $is_regexp ? '≈' : '=']); yield new Text('result of checking is = "%%_result"', ['result' => (new Text('failure'))->render()]); }
            if ($result === true && isset($this->on_success)) foreach ($this->on_success as $c_row_id => $c_action) { yield new Text_simple(''); yield Test_message::send_dpath($dpath.'/on_success'); foreach ($c_action->run($test, $dpath.'/on_success/'.$c_row_id) as $c_tick) yield $c_tick; }
            if ($result !== true && isset($this->on_failure)) foreach ($this->on_failure as $c_row_id => $c_action) { yield new Text_simple(''); yield Test_message::send_dpath($dpath.'/on_failure'); foreach ($c_action->run($test, $dpath.'/on_failure/'.$c_row_id) as $c_tick) yield $c_tick; }
        }
    }

}
