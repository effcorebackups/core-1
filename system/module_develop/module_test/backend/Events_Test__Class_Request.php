<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Request;
use effcore\Text;

abstract class Events_Test__Class_Request {

    ##########################################################################################
    ### $_POST: Request::sanitize_structure(), Request::value_get(), Request::values_get() ###
    ##########################################################################################

    static function test_step_code__sanitize_structure(&$test, $dpath, &$c_results) {
        global $_POST;

        $data = [
            'value_null' => null,
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_int_0' => 0,
            'value_int_1' => 1,
            'value_float_0_0' => 0.0,
            'value_float_1_0' => 1.0,
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_empty' => [],
            'value_array_null' => [null],
            'value_array_bool_true' => [true],
            'value_array_bool_false' => [false],
            'value_array_int_0' => [0],
            'value_array_int_1' => [1],
            'value_array_float_0_0' => [0.0],
            'value_array_float_1_0' => [1.0],
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_array_empty' => [[]],
            'value_array_wrong_keys' => [
                100 => 'option_1',
                200 => 'option_2'
            ],
            'select' => 'option_1',
            'select_multiple' => [
                0 => 'option_1',
                1 => 'option_2'
            ],
            'checkboxes' => [
                0 => 'checkbox_1',
                1 => 'checkbox_2'
            ]
        ];

        $expected = [
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_wrong_keys' => [
                0 => 'option_1',
                1 => 'option_2'
            ],
            'select' => 'option_1',
            'select_multiple' => [
                0 => 'option_1',
                1 => 'option_2'
            ],
            'checkboxes' => [
                0 => 'checkbox_1',
                1 => 'checkbox_2'
            ]
        ];

        foreach ($data as $c_row_id => $c_field) {
            $_POST = [$c_row_id => $c_field];
            $c_expected = array_key_exists($c_row_id, $expected) === false ? [] : [$c_row_id => $expected[$c_row_id]];
            $c_gotten = Request::sanitize_structure('_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__sanitize_structure_and_value_get(&$test, $dpath, &$c_results) {
        global $_POST;

        $data = [
            'value_null' => null,
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_int_0' => 0,
            'value_int_1' => 1,
            'value_float_0_0' => 0.0,
            'value_float_1_0' => 1.0,
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_empty' => [],
            'value_array_null' => [null],
            'value_array_bool_true' => [true],
            'value_array_bool_false' => [false],
            'value_array_int_0' => [0],
            'value_array_int_1' => [1],
            'value_array_float_0_0' => [0.0],
            'value_array_float_1_0' => [1.0],
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_array_empty' => [[]],
            'value_array_array_null' => [[null]],
            'value_array_array_bool_true' => [[true]],
            'value_array_array_bool_false' => [[false]],
            'value_array_array_int_0' => [[0]],
            'value_array_array_int_1' => [[1]],
            'value_array_array_float_0_0' => [[0.0]],
            'value_array_array_float_1_0' => [[1.0]],
            'value_array_array_string_empty' => [['']],
            'value_array_array_string_0' => [['0']],
            'value_array_array_string_1' => [['1']],
            'value_array_array_string_X' => [['X']],
            'value_array_array_array_empty' => [[[]]]
        ];

        ##################
        ### number = 0 ###
        ##################

        $expected = [
            'value_null' => '',
            'value_bool_true' => '',
            'value_bool_false' => '',
            'value_int_0' => '',
            'value_int_1' => '',
            'value_float_0_0' => '',
            'value_float_1_0' => '',
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_empty' => '',
            'value_array_null' => '',
            'value_array_bool_true' => '',
            'value_array_bool_false' => '',
            'value_array_int_0' => '',
            'value_array_int_1' => '',
            'value_array_float_0_0' => '',
            'value_array_float_1_0' => '',
            'value_array_string_empty' => '',
            'value_array_string_0' => '0',
            'value_array_string_1' => '1',
            'value_array_string_X' => 'X',
            'value_array_array_empty' => '',
            'value_array_array_null' => '',
            'value_array_array_bool_true' => '',
            'value_array_array_bool_false' => '',
            'value_array_array_int_0' => '',
            'value_array_array_int_1' => '',
            'value_array_array_float_0_0' => '',
            'value_array_array_float_1_0' => '',
            'value_array_array_string_empty' => '',
            'value_array_array_string_0' => '',
            'value_array_array_string_1' => '',
            'value_array_array_string_X' => '',
            'value_array_array_array_empty' => '',
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_POST = [$c_row_id => $c_value];
            $_POST = Request::sanitize_structure('_POST');
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, 0, '_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 0)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 0)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        ##################
        ### number = 1 ###
        ##################

        $expected = [
            'value_null' => '',
            'value_bool_true' => '',
            'value_bool_false' => '',
            'value_int_0' => '',
            'value_int_1' => '',
            'value_float_0_0' => '',
            'value_float_1_0' => '',
            'value_string_empty' => '',
            'value_string_0' => '0', # $number is ignored on 1-st level
            'value_string_1' => '1', # $number is ignored on 1-st level
            'value_string_X' => 'X', # $number is ignored on 1-st level
            'value_array_empty' => '',
            'value_array_null' => '',
            'value_array_bool_true' => '',
            'value_array_bool_false' => '',
            'value_array_int_0' => '',
            'value_array_int_1' => '',
            'value_array_float_0_0' => '',
            'value_array_float_1_0' => '',
            'value_array_string_empty' => '',
            'value_array_string_0' => '',
            'value_array_string_1' => '',
            'value_array_string_X' => '',
            'value_array_array_empty' => '',
            'value_array_array_null' => '',
            'value_array_array_bool_true' => '',
            'value_array_array_bool_false' => '',
            'value_array_array_int_0' => '',
            'value_array_array_int_1' => '',
            'value_array_array_float_0_0' => '',
            'value_array_array_float_1_0' => '',
            'value_array_array_string_empty' => '',
            'value_array_array_string_0' => '',
            'value_array_array_string_1' => '',
            'value_array_array_string_X' => '',
            'value_array_array_array_empty' => ''
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_POST = [$c_row_id => $c_value];
            $_POST = Request::sanitize_structure('_POST');
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, 1, '_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 1)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 1)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ┌─────────────────────────┐
        # │ transpositions in array │
        # ├────────────┬────────────┤
        # │ undefined  │ undefined  │
        # │ ''         │ undefined  │
        # │ 'value'    │ undefined  │
        # ├────────────┼────────────┤
        # │ undefined  │ ''         │
        # │ ''         │ ''         │
        # │ 'value'    │ ''         │
        # ├────────────┼────────────┤
        # │ undefined  │ 'value'    │
        # │ ''         │ 'value'    │
        # │ 'value'    │ 'value'    │
        # └────────────┴────────────┘

        $data = [
            'transposition_1' => [                  ],
            'transposition_2' => [0 => ''           ],
            'transposition_3' => [0 => 'X'          ],
            'transposition_4' => [          1 => '' ],
            'transposition_5' => [0 => '',  1 => '' ],
            'transposition_6' => [0 => 'X', 1 => '' ],
            'transposition_7' => [          1 => 'Y'],
            'transposition_8' => [0 => '',  1 => 'Y'],
            'transposition_9' => [0 => 'X', 1 => 'Y']
        ];

        ##################
        ### number = 0 ###
        ##################

        $expected = [
            'transposition_1' => '',
            'transposition_2' => '',
            'transposition_3' => 'X',
            'transposition_4' => '', # index '1' convert to '0' after sanitization
            'transposition_5' => '',
            'transposition_6' => 'X',
            'transposition_7' => 'Y', # index '1' convert to '0' after sanitization
            'transposition_8' => '',
            'transposition_9' => 'X'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_POST = [$c_row_id => $c_value];
            $_POST = Request::sanitize_structure('_POST');
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, 0, '_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 0)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 0)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        ##################
        ### number = 1 ###
        ##################

        $expected = [
            'transposition_1' => '',
            'transposition_2' => '',
            'transposition_3' => '',
            'transposition_4' => '',
            'transposition_5' => '',
            'transposition_6' => '',
            'transposition_7' => '',
            'transposition_8' => 'Y',
            'transposition_9' => 'Y'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_POST = [$c_row_id => $c_value];
            $_POST = Request::sanitize_structure('_POST');
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, 1, '_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 1)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (number = 1)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__sanitize_structure_and_values_get(&$test, $dpath, &$c_results) {
        global $_POST;

        $data = [
            'value_null' => null,
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_int_0' => 0,
            'value_int_1' => 1,
            'value_float_0_0' => 0.0,
            'value_float_1_0' => 1.0,
            'value_string_empty' => '',
            'value_string_0' => '0',
            'value_string_1' => '1',
            'value_string_X' => 'X',
            'value_array_empty' => [],
            'value_array_null' => [null],
            'value_array_bool_true' => [true],
            'value_array_bool_false' => [false],
            'value_array_int_0' => [0],
            'value_array_int_1' => [1],
            'value_array_float_0_0' => [0.0],
            'value_array_float_1_0' => [1.0],
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_array_empty' => [[]],
            'value_array_array_null' => [[null]],
            'value_array_array_bool_true' => [[true]],
            'value_array_array_bool_false' => [[false]],
            'value_array_array_int_0' => [[0]],
            'value_array_array_int_1' => [[1]],
            'value_array_array_float_0_0' => [[0.0]],
            'value_array_array_float_1_0' => [[1.0]],
            'value_array_array_string_empty' => [['']],
            'value_array_array_string_0' => [['0']],
            'value_array_array_string_1' => [['1']],
            'value_array_array_string_X' => [['X']],
            'value_array_array_array_empty' => [[[]]]
        ];

        $expected = [
            'value_null' => [],
            'value_bool_true' => [],
            'value_bool_false' => [],
            'value_int_0' => [],
            'value_int_1' => [],
            'value_float_0_0' => [],
            'value_float_1_0' => [],
            'value_string_empty' => [''],
            'value_string_0' => ['0'],
            'value_string_1' => ['1'],
            'value_string_X' => ['X'],
            'value_array_empty' => [],
            'value_array_null' => [],
            'value_array_bool_true' => [],
            'value_array_bool_false' => [],
            'value_array_int_0' => [],
            'value_array_int_1' => [],
            'value_array_float_0_0' => [],
            'value_array_float_1_0' => [],
            'value_array_string_empty' => [''],
            'value_array_string_0' => ['0'],
            'value_array_string_1' => ['1'],
            'value_array_string_X' => ['X'],
            'value_array_array_empty' => [],
            'value_array_array_null' => [],
            'value_array_array_bool_true' => [],
            'value_array_array_bool_false' => [],
            'value_array_array_int_0' => [],
            'value_array_array_int_1' => [],
            'value_array_array_float_0_0' => [],
            'value_array_array_float_1_0' => [],
            'value_array_array_string_empty' => [],
            'value_array_array_string_0' => [],
            'value_array_array_string_1' => [],
            'value_array_array_string_X' => [],
            'value_array_array_array_empty' => []
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_POST = [$c_row_id => $c_value];
            $_POST = Request::sanitize_structure('_POST');
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::values_get($c_row_id, '_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ┌─────────────────────────┐
        # │ transpositions in array │
        # ├────────────┬────────────┤
        # │ undefined  │ undefined  │
        # │ ''         │ undefined  │
        # │ 'value'    │ undefined  │
        # ├────────────┼────────────┤
        # │ undefined  │ ''         │
        # │ ''         │ ''         │
        # │ 'value'    │ ''         │
        # ├────────────┼────────────┤
        # │ undefined  │ 'value'    │
        # │ ''         │ 'value'    │
        # │ 'value'    │ 'value'    │
        # └────────────┴────────────┘

        $data = [
            'transposition_1' => [                  ],
            'transposition_2' => [0 => ''           ],
            'transposition_3' => [0 => 'X'          ],
            'transposition_4' => [          1 => '' ],
            'transposition_5' => [0 => '',  1 => '' ],
            'transposition_6' => [0 => 'X', 1 => '' ],
            'transposition_7' => [          1 => 'Y'],
            'transposition_8' => [0 => '',  1 => 'Y'],
            'transposition_9' => [0 => 'X', 1 => 'Y']
        ];

        $expected = [
            'transposition_1' => [],
            'transposition_2' => [''],
            'transposition_3' => ['X'],
            'transposition_4' => [''], # index '1' convert to '0' after sanitization
            'transposition_5' => ['', ''],
            'transposition_6' => ['X', ''],
            'transposition_7' => ['Y'], # index '1' convert to '0' after sanitization
            'transposition_8' => ['', 'Y'],
            'transposition_9' => ['X', 'Y']
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_POST = [$c_row_id => $c_value];
            $_POST = Request::sanitize_structure('_POST');
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::values_get($c_row_id, '_POST');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    ##########################################################################
    ### $_FILES: Request::sanitize_structure_files(), Request::files_get() ###
    ##########################################################################

    static function test_step_code__sanitize_structure_files(&$test, $dpath, &$c_results) {
        global $_FILES;

        $data = [
            'file' => [
                'name'     => 'text.txt',
                'type'     => 'text/plain',
                'tmp_name' => '/private/var/tmp/php000000',
                'error'    => 0,
                'size'     => 3
            ],
            'file_multiple' => [
                'name'     => [0 => 'test.png'],
                'type'     => [0 => 'image/png'],
                'tmp_name' => [0 => '/private/var/tmp/php000000'],
                'error'    => [0 => 0],
                'size'     => [0 => 6270]
            ],
            'file_error' => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_NO_FILE,
                'size'     => 0
            ],
            'file_error_multiple' => [
                'name'     => [0 => ''],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 0]
            ],
            'file_error_structure_1' => [
                'key' => false /* !(string|int) */
            ],
            'file_error_structure_2' => [
                0 /* !(string) */ => 'value'
            ],
            'file_error_structure_3' => [
                'key' => [
                    0 => false /* !(string|int) */
                ]
            ],
            'file_error_structure_4' => [
                'key' => [
                    'key' /* !(int) */ => 'value'
                ]
            ]
        ];

        $expected = [
            'file' => [
                'name'     => 'text.txt',
                'type'     => 'text/plain',
                'tmp_name' => '/private/var/tmp/php000000',
                'error'    => 0,
                'size'     => 3
            ],
            'file_multiple' => [
                'name'     => [0 => 'test.png'],
                'type'     => [0 => 'image/png'],
                'tmp_name' => [0 => '/private/var/tmp/php000000'],
                'error'    => [0 => 0],
                'size'     => [0 => 6270]
            ],
            'file_error' => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_NO_FILE,
                'size'     => 0
            ],
            'file_error_multiple' => [
                'name'     => [0 => ''],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 0]
            ]
        ];

        foreach ($data as $c_row_id => $c_field) {
            $_FILES = [$c_row_id => $c_field];
            $c_expected = array_key_exists($c_row_id, $expected) === false ? [] : [$c_row_id => $expected[$c_row_id]];
            $c_gotten = Request::sanitize_structure_files();
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__files_get(&$test, $dpath, &$c_results) {
        global $_FILES;

        $data = [
            'field_undefined' => [],
            # input[name="file",value="file.png"]
            'field_string__file' => [
                'name'     => 'file.png',
                'type'     => 'image/png',
                'tmp_name' => '/tmp/phpxxxxxxxx',
                'error'    => 0,
                'size'     => 1000
            ],
            # input[name="file[]",value="file.png"]
            'field_array__file' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => 'image/png'],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx'],
                'error'    => [0 => 0],
                'size'     => [0 => 1000]
            ],
            # input[name="file[]",value="file1.png,file2.png",multiple="multiple"]
            'field_array__file1_file2_multiple' => [
                'name'     => [0 => 'file1.png',        1 => 'file2.png'],
                'type'     => [0 => 'image/png',        1 => 'image/png'],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx', 1 => '/tmp/phpyyyyyyyy'],
                'error'    => [0 => 0,                  1 => 0],
                'size'     => [0 => 1000,               1 => 1000]
            ],
            # input[name="file[]",value="",multiple="multiple"]
            # input[name="file[]",value="",multiple="multiple"]
            'field_array__no_file1_multiple__no_file2_multiple' => [
                'name'     => [0 => '',                 1 => ''],
                'type'     => [0 => '',                 1 => ''],
                'tmp_name' => [0 => '',                 1 => ''],
                'error'    => [0 => UPLOAD_ERR_NO_FILE, 1 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 0,                  1 => 0]
            ],
            # input[name="file[]",value="file1.png",multiple="multiple"]
            # input[name="file[]",value="",         multiple="multiple"]
            'field_array__file1_multiple__no_file2_multiple' => [
                'name'     => [0 => 'file1.png',        1 => ''],
                'type'     => [0 => 'image/png',        1 => ''],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx', 1 => ''],
                'error'    => [0 => 0,                  1 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 1000,               1 => 0]
            ],
            # input[name="file[]",value="",         multiple="multiple"]
            # input[name="file[]",value="file2.png",multiple="multiple"]
            'field_array__no_file_1_multiple__file2_multiple' => [
                'name'     => [0 => '',                 1 => 'file2.png'],
                'type'     => [0 => '',                 1 => 'image/png'],
                'tmp_name' => [0 => '',                 1 => '/tmp/phpyyyyyyyy'],
                'error'    => [0 => UPLOAD_ERR_NO_FILE, 1 => 0],
                'size'     => [0 => 0,                  1 => 1000]
            ],
            # input[name="file[]",value="file1.png",multiple="multiple"]
            # input[name="file[]",value="file2.png",multiple="multiple"]
            'field_array__file1_multiple__file2_multiple' => [
                'name'     => [0 => 'file1.png',        1 => 'file2.png'],
                'type'     => [0 => 'image/png',        1 => 'image/png'],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx', 1 => '/tmp/phpyyyyyyyy'],
                'error'    => [0 => 0,                  1 => 0],
                'size'     => [0 => 1000,               1 => 1000]
            ],
            'field_error_1' => [
                'name'     => 'file.png',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_INI_SIZE,
                'size'     => 0
            ],
            'field_error_2' => [
                'name'     => 'file.png',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_FORM_SIZE,
                'size'     => 0
            ],
            'field_error_3' => [
                'name'     => 'file.png',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_PARTIAL,
                'size'     => 0
            ],
            'field_error_4' => [
                'name'     => '',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_NO_FILE,
                'size'     => 0
            ],
            'field_error_6' => [
                'name'     => 'file.png',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_NO_TMP_DIR,
                'size'     => 0
            ],
            'field_error_7' => [
                'name'     => 'file.png',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_CANT_WRITE,
                'size'     => 0
            ],
            'field_error_8' => [
                'name'     => 'file.png',
                'type'     => '',
                'tmp_name' => '',
                'error'    => UPLOAD_ERR_EXTENSION,
                'size'     => 0
            ],
            'field_error_1_array' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_INI_SIZE],
                'size'     => [0 => 0]
            ],
            'field_error_2_array' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_FORM_SIZE],
                'size'     => [0 => 0]
            ],
            'field_error_3_array' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_PARTIAL],
                'size'     => [0 => 0]
            ],
            'field_error_4_array' => [
                'name'     => [0 => ''],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 0]
            ],
            'field_error_6_array' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_NO_TMP_DIR],
                'size'     => [0 => 0]
            ],
            'field_error_7_array' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_CANT_WRITE],
                'size'     => [0 => 0]
            ],
            'field_error_8_array' => [
                'name'     => [0 => 'file.png'],
                'type'     => [0 => ''],
                'tmp_name' => [0 => ''],
                'error'    => [0 => UPLOAD_ERR_EXTENSION],
                'size'     => [0 => 0]
            ]
        ];

        $expected = [
            'field_undefined' => [],
            'field_string__file' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpxxxxxxxx',
                    'error'    => 0
                ]
            ],
            'field_array__file' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpxxxxxxxx',
                    'error'    => 0
                ]
            ],
            'field_array__file1_file2_multiple' => [
                (object)[
                    'file'     => 'file1.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpxxxxxxxx',
                    'error'    => 0],
                (object)[
                    'file'     => 'file2.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpyyyyyyyy',
                    'error'    => 0
                ]
            ],
            'field_array__no_file1_multiple__no_file2_multiple' => [],
            'field_array__file1_multiple__no_file2_multiple' => [
                (object)[
                    'file'     => 'file1.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpxxxxxxxx',
                    'error'    => 0
                ]
            ],
            'field_array__no_file_1_multiple__file2_multiple' => [
                1 => (object)[
                    'file'     => 'file2.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpyyyyyyyy',
                    'error'    => 0
                ]
            ],
            'field_array__file1_multiple__file2_multiple' => [
                (object)[
                    'file'     => 'file1.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpxxxxxxxx',
                    'error'    => 0],
                (object)[
                    'file'     => 'file2.png',
                    'mime'     => 'image/png',
                    'size'     => 1000,
                    'path_tmp' => '/tmp/phpyyyyyyyy',
                    'error'    => 0
                ]
            ],
            'field_error_1' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_INI_SIZE
                ]
            ],
            'field_error_2' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_FORM_SIZE
                ]
            ],
            'field_error_3' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_PARTIAL
                ]
            ],
            'field_error_4' => [],
            'field_error_6' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_NO_TMP_DIR
                ]
            ],
            'field_error_7' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_CANT_WRITE
                ]
            ],
            'field_error_8' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_EXTENSION
                ]
            ],
            'field_error_1_array' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_INI_SIZE
                ]
            ],
            'field_error_2_array' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_FORM_SIZE
                ]
            ],
            'field_error_3_array' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_PARTIAL
                ]
            ],
            'field_error_4_array' => [],
            'field_error_6_array' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_NO_TMP_DIR
                ]
            ],
            'field_error_7_array' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_CANT_WRITE
                ]
            ],
            'field_error_8_array' => [
                (object)[
                    'file'     => 'file.png',
                    'mime'     => '',
                    'size'     => 0,
                    'path_tmp' => '',
                    'error'    => UPLOAD_ERR_EXTENSION
                ]
            ]
        ];

        foreach ($data as $c_row_id => $c_field) {
            $_FILES = [$c_row_id => $c_field];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::files_get($c_row_id, 'stdClass');
            $c_result = $c_gotten == $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    ####################################
    ### Request::software_get_info() ###
    ####################################

    static function test_step_code__software_get_info(&$test, $dpath, &$c_results) {
        $data = [
            'nginx'       => (array)Request::software_get_info('nginx/1.1X.X'),
            'apache_nix'  => (array)Request::software_get_info('Apache/2.4.XX (Unix) LibreSSL/2.2.X PHP/5.6.XX'),
            'apache_win'  => (array)Request::software_get_info('Apache/2.4.XX (Win32) OpenSSL/1.X.X PHP/5.6.XX'),
            'iis'         => (array)Request::software_get_info('Microsoft-IIS/7.5'),
            'lighttpd'    => (array)Request::software_get_info('lighttpd/1.X.XX'),
            'unknown_nix' => (array)Request::software_get_info('Unknown/1.0.XX (Linux)'),
            'unknown'     => (array)Request::software_get_info('Unknown server v-1-0')
        ];

        $expected = [
            'nginx'       => ['name' => 'nginx',                'version' => '1.1X.X'],
            'apache_nix'  => ['name' => 'apache',               'version' => '2.4.XX'],
            'apache_win'  => ['name' => 'apache',               'version' => '2.4.XX'],
            'iis'         => ['name' => 'microsoft-iis',        'version' => '7.5'],
            'lighttpd'    => ['name' => 'lighttpd',             'version' => '1.X.XX'],
            'unknown_nix' => ['name' => 'unknown',              'version' => '1.0.XX'],
            'unknown'     => ['name' => 'unknown server v-1-0', 'version' => '']
        ];

        foreach ($data as $c_row_id => $c_field) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = $data[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($c_expected))]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($c_gotten))]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
