<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Node;
use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Node {

    static function test_step_code__has_attribute_value(&$test, $dpath, &$c_results) {
        $node = new Node;
        $node->attribute_insert('data-str', '123');
        $node->attribute_insert('data-int',  123 );

        $data = [
            'attr_int__take_string__big'             => $node->has_attribute_value('data-int', '1234'),
            'attr_int__take_int__big'                => $node->has_attribute_value('data-int',  1234 ),
            'attr_int__take_string__middle'          => $node->has_attribute_value('data-int', '123' ),
            'attr_int__take_int__middle'             => $node->has_attribute_value('data-int',  123  ),
            'attr_int__take_string__small__at_start' => $node->has_attribute_value('data-int', '12'  ),
            'attr_int__take_int__small__at_start'    => $node->has_attribute_value('data-int',  12   ),
            'attr_int__take_string__small__at_end'   => $node->has_attribute_value('data-int',  '23' ),
            'attr_int__take_int__small__at_end'      => $node->has_attribute_value('data-int',   23  ),
            'attr_int__take_string_empty'            => $node->has_attribute_value('data-int', ''    ),
            'attr_str__take_string__big'             => $node->has_attribute_value('data-str', '1234'),
            'attr_str__take_int__big'                => $node->has_attribute_value('data-str',  1234 ),
            'attr_str__take_string__middle'          => $node->has_attribute_value('data-str', '123' ),
            'attr_str__take_int__middle'             => $node->has_attribute_value('data-str',  123  ),
            'attr_str__take_string__small__at_start' => $node->has_attribute_value('data-str', '12'  ),
            'attr_str__take_int__small__at_start'    => $node->has_attribute_value('data-str',  12   ),
            'attr_str__take_string__small__at_end'   => $node->has_attribute_value('data-str',  '23' ),
            'attr_str__take_int__small__at_end'      => $node->has_attribute_value('data-str',   23  ),
            'attr_str__take_string_empty'            => $node->has_attribute_value('data-str', ''    ),
            'attr_unknown'                           => $node->has_attribute_value('unknown' , '123' )
        ];

        $expected = [
            'attr_int__take_string__big'             => false,
            'attr_int__take_int__big'                => false,
            'attr_int__take_string__middle'          => true,
            'attr_int__take_int__middle'             => true,
            'attr_int__take_string__small__at_start' => false,
            'attr_int__take_int__small__at_start'    => false,
            'attr_int__take_string__small__at_end'   => false,
            'attr_int__take_int__small__at_end'      => false,
            'attr_int__take_string_empty'            => false,
            'attr_str__take_string__big'             => false,
            'attr_str__take_int__big'                => false,
            'attr_str__take_string__middle'          => true,
            'attr_str__take_int__middle'             => true,
            'attr_str__take_string__small__at_start' => false,
            'attr_str__take_int__small__at_start'    => false,
            'attr_str__take_string__small__at_end'   => false,
            'attr_str__take_int__small__at_end'      => false,
            'attr_str__take_string_empty'            => false,
            'attr_unknown'                           => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__has_attribute_value_contains(&$test, $dpath, &$c_results) {
        $node = new Node;
        $node->attribute_insert('data-str', 'abc 123 def');
        $node->attribute_insert('data-int',  123 );

        $data = [
            'attr_int__take_string__big'             => $node->has_attribute_value_contains('data-int', '1234'),
            'attr_int__take_int__big'                => $node->has_attribute_value_contains('data-int',  1234 ),
            'attr_int__take_string__middle'          => $node->has_attribute_value_contains('data-int', '123' ),
            'attr_int__take_int__middle'             => $node->has_attribute_value_contains('data-int',  123  ),
            'attr_int__take_string__small__at_start' => $node->has_attribute_value_contains('data-int', '12'  ),
            'attr_int__take_int__small__at_start'    => $node->has_attribute_value_contains('data-int',  12   ),
            'attr_int__take_string__small__at_end'   => $node->has_attribute_value_contains('data-int',  '23' ),
            'attr_int__take_int__small__at_end'      => $node->has_attribute_value_contains('data-int',   23  ),
            'attr_int__take_string_empty'            => $node->has_attribute_value_contains('data-int', ''    ),
            'attr_str__take_string__big'             => $node->has_attribute_value_contains('data-str', '1234'),
            'attr_str__take_int__big'                => $node->has_attribute_value_contains('data-str',  1234 ),
            'attr_str__take_string__middle'          => $node->has_attribute_value_contains('data-str', '123' ),
            'attr_str__take_int__middle'             => $node->has_attribute_value_contains('data-str',  123  ),
            'attr_str__take_string__small__at_start' => $node->has_attribute_value_contains('data-str', '12'  ),
            'attr_str__take_int__small__at_start'    => $node->has_attribute_value_contains('data-str',  12   ),
            'attr_str__take_string__small__at_end'   => $node->has_attribute_value_contains('data-str',  '23' ),
            'attr_str__take_int__small__at_end'      => $node->has_attribute_value_contains('data-str',   23  ),
            'attr_str__take_string_empty'            => $node->has_attribute_value_contains('data-str', ''    ),
            'attr_unknown'                           => $node->has_attribute_value_contains('unknown' , '123' )
        ];

        $expected = [
            'attr_int__take_string__big'             => false,
            'attr_int__take_int__big'                => false,
            'attr_int__take_string__middle'          => true ,
            'attr_int__take_int__middle'             => true ,
            'attr_int__take_string__small__at_start' => true ,
            'attr_int__take_int__small__at_start'    => true ,
            'attr_int__take_string__small__at_end'   => true ,
            'attr_int__take_int__small__at_end'      => true ,
            'attr_int__take_string_empty'            => false,
            'attr_str__take_string__big'             => false,
            'attr_str__take_int__big'                => false,
            'attr_str__take_string__middle'          => true ,
            'attr_str__take_int__middle'             => true ,
            'attr_str__take_string__small__at_start' => true ,
            'attr_str__take_int__small__at_start'    => true ,
            'attr_str__take_string__small__at_end'   => true ,
            'attr_str__take_int__small__at_end'      => true ,
            'attr_str__take_string_empty'            => false,
            'attr_unknown'                           => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__has_attribute_value_includes(&$test, $dpath, &$c_results) {
        $node = new Node;
        $node->attribute_insert('data-str', 'abc 123 def');
        $node->attribute_insert('data-int',  123 );

        $data = [
            'attr_int__take_string__big'             => $node->has_attribute_value_includes('data-int', '1234'),
            'attr_int__take_int__big'                => $node->has_attribute_value_includes('data-int',  1234 ),
            'attr_int__take_string__middle'          => $node->has_attribute_value_includes('data-int', '123' ),
            'attr_int__take_int__middle'             => $node->has_attribute_value_includes('data-int',  123  ),
            'attr_int__take_string__small__at_start' => $node->has_attribute_value_includes('data-int', '12'  ),
            'attr_int__take_int__small__at_start'    => $node->has_attribute_value_includes('data-int',  12   ),
            'attr_int__take_string__small__at_end'   => $node->has_attribute_value_includes('data-int',  '23' ),
            'attr_int__take_int__small__at_end'      => $node->has_attribute_value_includes('data-int',   23  ),
            'attr_int__take_string_empty'            => $node->has_attribute_value_includes('data-int', ''    ),
            'attr_str__take_string__big'             => $node->has_attribute_value_includes('data-str', '1234'),
            'attr_str__take_int__big'                => $node->has_attribute_value_includes('data-str',  1234 ),
            'attr_str__take_string__middle'          => $node->has_attribute_value_includes('data-str', '123' ),
            'attr_str__take_int__middle'             => $node->has_attribute_value_includes('data-str',  123  ),
            'attr_str__take_string__small__at_start' => $node->has_attribute_value_includes('data-str', '12'  ),
            'attr_str__take_int__small__at_start'    => $node->has_attribute_value_includes('data-str',  12   ),
            'attr_str__take_string__small__at_end'   => $node->has_attribute_value_includes('data-str',  '23' ),
            'attr_str__take_int__small__at_end'      => $node->has_attribute_value_includes('data-str',   23  ),
            'attr_str__take_string_empty'            => $node->has_attribute_value_includes('data-str', ''    ),
            'attr_unknown'                           => $node->has_attribute_value_includes('unknown' , '123' )
        ];

        $expected = [
            'attr_int__take_string__big'             => false,
            'attr_int__take_int__big'                => false,
            'attr_int__take_string__middle'          => true,
            'attr_int__take_int__middle'             => true,
            'attr_int__take_string__small__at_start' => false,
            'attr_int__take_int__small__at_start'    => false,
            'attr_int__take_string__small__at_end'   => false,
            'attr_int__take_int__small__at_end'      => false,
            'attr_int__take_string_empty'            => false,
            'attr_str__take_string__big'             => false,
            'attr_str__take_int__big'                => false,
            'attr_str__take_string__middle'          => true,
            'attr_str__take_int__middle'             => true,
            'attr_str__take_string__small__at_start' => false,
            'attr_str__take_int__small__at_start'    => false,
            'attr_str__take_string__small__at_end'   => false,
            'attr_str__take_int__small__at_end'      => false,
            'attr_str__take_string_empty'            => false,
            'attr_unknown'                           => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__has_attribute_value_starts(&$test, $dpath, &$c_results) {
        $node = new Node;
        $node->attribute_insert('data-str', '123');
        $node->attribute_insert('data-int',  123 );

        $data = [
            'attr_int__take_string__big'             => $node->has_attribute_value_starts('data-int', '1234'),
            'attr_int__take_int__big'                => $node->has_attribute_value_starts('data-int',  1234 ),
            'attr_int__take_string__middle'          => $node->has_attribute_value_starts('data-int', '123' ),
            'attr_int__take_int__middle'             => $node->has_attribute_value_starts('data-int',  123  ),
            'attr_int__take_string__small__at_start' => $node->has_attribute_value_starts('data-int', '12'  ),
            'attr_int__take_int__small__at_start'    => $node->has_attribute_value_starts('data-int',  12   ),
            'attr_int__take_string__small__at_end'   => $node->has_attribute_value_starts('data-int',  '23' ),
            'attr_int__take_int__small__at_end'      => $node->has_attribute_value_starts('data-int',   23  ),
            'attr_int__take_string_empty'            => $node->has_attribute_value_starts('data-int', ''    ),
            'attr_str__take_string__big'             => $node->has_attribute_value_starts('data-str', '1234'),
            'attr_str__take_int__big'                => $node->has_attribute_value_starts('data-str',  1234 ),
            'attr_str__take_string__middle'          => $node->has_attribute_value_starts('data-str', '123' ),
            'attr_str__take_int__middle'             => $node->has_attribute_value_starts('data-str',  123  ),
            'attr_str__take_string__small__at_start' => $node->has_attribute_value_starts('data-str', '12'  ),
            'attr_str__take_int__small__at_start'    => $node->has_attribute_value_starts('data-str',  12   ),
            'attr_str__take_string__small__at_end'   => $node->has_attribute_value_starts('data-str',  '23' ),
            'attr_str__take_int__small__at_end'      => $node->has_attribute_value_starts('data-str',   23  ),
            'attr_str__take_string_empty'            => $node->has_attribute_value_starts('data-str', ''    ),
            'attr_unknown'                           => $node->has_attribute_value_starts('unknown' , '123' )
        ];

        $expected = [
            'attr_int__take_string__big'             => false,
            'attr_int__take_int__big'                => false,
            'attr_int__take_string__middle'          => true,
            'attr_int__take_int__middle'             => true,
            'attr_int__take_string__small__at_start' => true,
            'attr_int__take_int__small__at_start'    => true,
            'attr_int__take_string__small__at_end'   => false,
            'attr_int__take_int__small__at_end'      => false,
            'attr_int__take_string_empty'            => false,
            'attr_str__take_string__big'             => false,
            'attr_str__take_int__big'                => false,
            'attr_str__take_string__middle'          => true,
            'attr_str__take_int__middle'             => true,
            'attr_str__take_string__small__at_start' => true,
            'attr_str__take_int__small__at_start'    => true,
            'attr_str__take_string__small__at_end'   => false,
            'attr_str__take_int__small__at_end'      => false,
            'attr_str__take_string_empty'            => false,
            'attr_unknown'                           => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__has_attribute_value_ends(&$test, $dpath, &$c_results) {
        $node = new Node;
        $node->attribute_insert('data-str', '123');
        $node->attribute_insert('data-int',  123 );

        $data = [
            'attr_int__take_string__big'             => $node->has_attribute_value_ends('data-int', '1234'),
            'attr_int__take_int__big'                => $node->has_attribute_value_ends('data-int',  1234 ),
            'attr_int__take_string__middle'          => $node->has_attribute_value_ends('data-int', '123' ),
            'attr_int__take_int__middle'             => $node->has_attribute_value_ends('data-int',  123  ),
            'attr_int__take_string__small__at_start' => $node->has_attribute_value_ends('data-int', '12'  ),
            'attr_int__take_int__small__at_start'    => $node->has_attribute_value_ends('data-int',  12   ),
            'attr_int__take_string__small__at_end'   => $node->has_attribute_value_ends('data-int',  '23' ),
            'attr_int__take_int__small__at_end'      => $node->has_attribute_value_ends('data-int',   23  ),
            'attr_int__take_string_empty'            => $node->has_attribute_value_ends('data-int', ''    ),
            'attr_str__take_string__big'             => $node->has_attribute_value_ends('data-str', '1234'),
            'attr_str__take_int__big'                => $node->has_attribute_value_ends('data-str',  1234 ),
            'attr_str__take_string__middle'          => $node->has_attribute_value_ends('data-str', '123' ),
            'attr_str__take_int__middle'             => $node->has_attribute_value_ends('data-str',  123  ),
            'attr_str__take_string__small__at_start' => $node->has_attribute_value_ends('data-str', '12'  ),
            'attr_str__take_int__small__at_start'    => $node->has_attribute_value_ends('data-str',  12   ),
            'attr_str__take_string__small__at_end'   => $node->has_attribute_value_ends('data-str',  '23' ),
            'attr_str__take_int__small__at_end'      => $node->has_attribute_value_ends('data-str',   23  ),
            'attr_str__take_string_empty'            => $node->has_attribute_value_ends('data-str', ''    ),
            'attr_unknown'                           => $node->has_attribute_value_ends('unknown' , '123' )
        ];

        $expected = [
            'attr_int__take_string__big'             => false,
            'attr_int__take_int__big'                => false,
            'attr_int__take_string__middle'          => true,
            'attr_int__take_int__middle'             => true,
            'attr_int__take_string__small__at_start' => false,
            'attr_int__take_int__small__at_start'    => false,
            'attr_int__take_string__small__at_end'   => true,
            'attr_int__take_int__small__at_end'      => true,
            'attr_int__take_string_empty'            => false,
            'attr_str__take_string__big'             => false,
            'attr_str__take_int__big'                => false,
            'attr_str__take_string__middle'          => true,
            'attr_str__take_int__middle'             => true,
            'attr_str__take_string__small__at_start' => false,
            'attr_str__take_int__small__at_start'    => false,
            'attr_str__take_string__small__at_end'   => true,
            'attr_str__take_int__small__at_end'      => true,
            'attr_str__take_string_empty'            => false,
            'attr_unknown'                           => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
