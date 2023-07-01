<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use effcore\Core;
use effcore\Test;
use effcore\Test_feed__Core__Select_recursive;
use effcore\Test_feed__Core__Serialize;
use effcore\Text;
use effcore\Text_simple;
use stdCLass;

abstract class Events_Test__Class_Core {

    static function test_step_code__gettype(&$test, $dpath, &$c_results) {
        $data = [
            'value_string'             => Core::gettype('string'),
            'value_int'                => Core::gettype(123),
            'value_float'              => Core::gettype(0.000001),
            'value_bool_true'          => Core::gettype(true),
            'value_bool_false'         => Core::gettype(false),
            'value_null'               => Core::gettype(null),
            'value_array_empty'        => Core::gettype([]),
            'value_class_std'          => Core::gettype(new stdCLass),
            'value_class_std_is_full'  => Core::gettype(new stdCLass, false),
            'value_class_test'         => Core::gettype(new Test),
            'value_class_test_is_full' => Core::gettype(new Test, false)
        ];

        $expected = [
            'value_string' => 'string',
            'value_int' => 'integer',
            'value_float' => 'double',
            'value_bool_true' => 'boolean',
            'value_bool_false' => 'boolean',
            'value_null' => 'null',
            'value_array_empty' => 'array',
            'value_class_std' => 'object:\\stdClass',
            'value_class_std_is_full' => 'object',
            'value_class_test' => 'object:Test',
            'value_class_test_is_full' => 'object'
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__in_array(&$test, $dpath, &$c_results) {
        $data = [
            'value_string_empty' => Core::in_array('',   ['']),
            'value_null'         => Core::in_array(null, ['']),
            'value_int_0'        => Core::in_array(0,    ['']),
            'value_string_0'     => Core::in_array('0',  [''])
        ];

        $expected = [
            'value_string_empty' => true,
            'value_null' => true,
            'value_int_0' => false,
            'value_string_0' => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__sort(&$test, $dpath, &$c_results) {
        $data = [
            'a' => ['weight' =>  4],
            'b' => ['weight' => 10],
            'c' => ['weight' => 10],
            'd' => ['weight' => 10],
            'e' => ['weight' =>  4]
        ];

        $expected = [
            'b' => ['weight' => 10],
            'c' => ['weight' => 10],
            'd' => ['weight' => 10],
            'a' => ['weight' =>  4],
            'e' => ['weight' =>  4]
        ];

        $gotten = Core::array_sort_by_number($data, 'weight', Core::SORT_ASC);
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($expected))]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($gotten))]);
            $c_results['return'] = 0;
            return;
        }

        $data = [
            'a' => ['weight' =>  4],
            'b' => ['weight' => 10],
            'c' => ['weight' => 10],
            'd' => ['weight' => 10],
            'e' => ['weight' =>  4]
        ];

        $expected = [
            'a' => ['weight' =>  4],
            'e' => ['weight' =>  4],
            'b' => ['weight' => 10],
            'c' => ['weight' => 10],
            'd' => ['weight' => 10]
        ];

        $gotten = Core::array_sort_by_number($data, 'weight', Core::SORT_DSC);
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($expected))]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($gotten))]);
            $c_results['return'] = 0;
            return;
        }
    }

    static function test_step_code__fractional_part_length_get(&$test, $dpath, &$c_results) {
        $data = [
            ['value' => '',                         'no_zeros' => false, 'expected' => 0],
            ['value' => '100',                      'no_zeros' => false, 'expected' => 0],
            ['value' => '0',                        'no_zeros' => false, 'expected' => 0],
            ['value' => '0.00100',                  'no_zeros' => false, 'expected' => 5],
            ['value' => '123456789.1',              'no_zeros' => false, 'expected' => 1],
            ['value' => '123456789.12',             'no_zeros' => false, 'expected' => 2],
            ['value' => '123456789.123',            'no_zeros' => false, 'expected' => 3],
            ['value' => '123456789.1234',           'no_zeros' => false, 'expected' => 4],
            ['value' => '123456789.12345',          'no_zeros' => false, 'expected' => 5],
            ['value' => '123456789.123456',         'no_zeros' => false, 'expected' => 6],
            ['value' => '123456789.1234567',        'no_zeros' => false, 'expected' => 7],
            ['value' => '123456789.12345678',       'no_zeros' => false, 'expected' => 8],
            ['value' => '123456789.123456789',      'no_zeros' => false, 'expected' => 9],
            ['value' => '123456789.100000',         'no_zeros' => false, 'expected' => 6],
            ['value' => '123456789.1200000',        'no_zeros' => false, 'expected' => 7],
            ['value' => '123456789.12300000',       'no_zeros' => false, 'expected' => 8],
            ['value' => '123456789.123400000',      'no_zeros' => false, 'expected' => 9],
            ['value' => '123456789.1234500000',     'no_zeros' => false, 'expected' => 10],
            ['value' => '123456789.12345600000',    'no_zeros' => false, 'expected' => 11],
            ['value' => '123456789.123456700000',   'no_zeros' => false, 'expected' => 12],
            ['value' => '123456789.1234567800000',  'no_zeros' => false, 'expected' => 13],
            ['value' => '123456789.12345678900000', 'no_zeros' => false, 'expected' => 14],
            ['value' => '',                         'no_zeros' => true,  'expected' => 0],
            ['value' => '100',                      'no_zeros' => true,  'expected' => 0],
            ['value' => '0',                        'no_zeros' => true,  'expected' => 0],
            ['value' => '0.00100',                  'no_zeros' => true,  'expected' => 3],
            ['value' => '123456789.1',              'no_zeros' => true,  'expected' => 1],
            ['value' => '123456789.12',             'no_zeros' => true,  'expected' => 2],
            ['value' => '123456789.123',            'no_zeros' => true,  'expected' => 3],
            ['value' => '123456789.1234',           'no_zeros' => true,  'expected' => 4],
            ['value' => '123456789.12345',          'no_zeros' => true,  'expected' => 5],
            ['value' => '123456789.123456',         'no_zeros' => true,  'expected' => 6],
            ['value' => '123456789.1234567',        'no_zeros' => true,  'expected' => 7],
            ['value' => '123456789.12345678',       'no_zeros' => true,  'expected' => 8],
            ['value' => '123456789.123456789',      'no_zeros' => true,  'expected' => 9],
            ['value' => '123456789.100000',         'no_zeros' => true,  'expected' => 1],
            ['value' => '123456789.1200000',        'no_zeros' => true,  'expected' => 2],
            ['value' => '123456789.12300000',       'no_zeros' => true,  'expected' => 3],
            ['value' => '123456789.123400000',      'no_zeros' => true,  'expected' => 4],
            ['value' => '123456789.1234500000',     'no_zeros' => true,  'expected' => 5],
            ['value' => '123456789.12345600000',    'no_zeros' => true,  'expected' => 6],
            ['value' => '123456789.123456700000',   'no_zeros' => true,  'expected' => 7],
            ['value' => '123456789.1234567800000',  'no_zeros' => true,  'expected' => 8],
            ['value' => '123456789.12345678900000', 'no_zeros' => true,  'expected' => 9],
            ['value' => 100,                        'no_zeros' => false, 'expected' => 40],
            ['value' => 0,                          'no_zeros' => false, 'expected' => 40],
            ['value' => 0.00100,                    'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1,                'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.12,               'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.123,              'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1234,             'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.12345,            'no_zeros' => false, 'expected' => 40],
            ['value' =>  23456789.123456,           'no_zeros' => false, 'expected' => 40],
            ['value' =>   3456789.1234567,          'no_zeros' => false, 'expected' => 40],
            ['value' =>     56789.12345678,         'no_zeros' => false, 'expected' => 40],
            ['value' =>      6789.123456789,        'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.100000,           'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1200000,          'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.12300000,         'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.123400000,        'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1234500000,       'no_zeros' => false, 'expected' => 40],
            ['value' =>  23456789.12345600000,      'no_zeros' => false, 'expected' => 40],
            ['value' =>   3456789.123456700000,     'no_zeros' => false, 'expected' => 40],
            ['value' =>    456789.1234567800000,    'no_zeros' => false, 'expected' => 40],
            ['value' =>     56789.12345678900000,   'no_zeros' => false, 'expected' => 40],
            ['value' =>      6789.123456789000000,  'no_zeros' => false, 'expected' => 40],
            ['value' => 100,                        'no_zeros' => true,  'expected' => 0],
            ['value' => 0,                          'no_zeros' => true,  'expected' => 0],
            ['value' => 0.00100,                    'no_zeros' => true,  'expected' => 3],
            ['value' => 123456789.1,                'no_zeros' => true,  'expected' => 1],
            ['value' => 123456789.12,               'no_zeros' => true,  'expected' => 2],
            ['value' => 123456789.123,              'no_zeros' => true,  'expected' => 3],
            ['value' => 123456789.1234,             'no_zeros' => true,  'expected' => 4],
            ['value' => 123456789.12345,            'no_zeros' => true,  'expected' => 5],
            ['value' =>  23456789.123456,           'no_zeros' => true,  'expected' => 6],
            ['value' =>   3456789.1234567,          'no_zeros' => true,  'expected' => 7],
            ['value' =>     56789.12345678,         'no_zeros' => true,  'expected' => 8],
            ['value' =>      6789.123456789,        'no_zeros' => true,  'expected' => 9],
            ['value' => 123456789.100000,           'no_zeros' => true,  'expected' => 1],
            ['value' => 123456789.1200000,          'no_zeros' => true,  'expected' => 2],
            ['value' => 123456789.12300000,         'no_zeros' => true,  'expected' => 3],
            ['value' => 123456789.123400000,        'no_zeros' => true,  'expected' => 4],
            ['value' => 123456789.1234500000,       'no_zeros' => true,  'expected' => 5],
            ['value' =>  23456789.12345600000,      'no_zeros' => true,  'expected' => 6],
            ['value' =>   3456789.123456700000,     'no_zeros' => true,  'expected' => 7],
            ['value' =>    456789.1234567800000,    'no_zeros' => true,  'expected' => 8],
            ['value' =>     56789.12345678900000,   'no_zeros' => true,  'expected' => 9],
            ['value' =>      6789.123456789000000,  'no_zeros' => true,  'expected' => 9]
        ];

        foreach ($data as $c_row_id => $c_info) {
            $c_is_no_zeros = $c_info['no_zeros'];
            $c_expected = $c_info['expected'];
            $c_value = $c_info['value'];
            $c_gotten = Core::fractional_part_length_get($c_value, $c_is_no_zeros);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value.' (no_zeros = '.($c_is_no_zeros ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value.' (no_zeros = '.($c_is_no_zeros ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__exponencial_string_normalize(&$test, $dpath, &$c_results) {
        $data = [
            'value_int' => '123',
            'value_int_negative' => -123,
            'value_float' => 1.23,
            'value_float_negative' => -1.23,
            'value_int_exponential_small' => '1.23e-6',
            'value_int_exponential_big' => '1.23e6',
            'value_int_octal' => '0123',
            'value_int_binary' => '0b101',
            'value_int_hexadecimal' => '0x123',
            'value_int_prefix' => 'а123',
            'value_int_suffix' => '123а',
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_null' => null,
            'value_array_empty' => []
        ];

        $expected = [
            'value_int' => '123',
            'value_int_negative' => -123,
            'value_float' => 1.23,
            'value_float_negative' => -1.23,
            'value_int_exponential_small' => '0.00000123',
            'value_int_exponential_big' => '1230000',
            'value_int_octal' => '0123',
            'value_int_binary' => '0b101',
            'value_int_hexadecimal' => '0x123',
            'value_int_prefix' => 'а123',
            'value_int_suffix' => '123а',
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_null' => null,
            'value_array_empty' => []
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::exponencial_string_normalize($c_value);
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

    static function test_step_code__data_stringify(&$test, $dpath, &$c_results) {
        $data = [
            'value_int_negative' => -1,
            'value_float_negative' => -1.1,
            'value_float' => 0.0000001,
            'value_int_exponential' => 123e1,
            'value_int_hexadecimal' => 0x123,
            'value_int_octal' => 01234,
            'value_int_binary' => 0b101,
            'value_int_prefix' => 'а123',
            'value_int_suffix' => '123а',
            'value_bool_true' => true,
            'value_bool_false' => false,
            'value_null' => null,
            'array' => [
                /* 0 => */  100,
                /* 1 => */ '200',
                'item3' =>  300,
                'item4' => '400',
                'item5' => 'value500'],
            'object' => (object)[
                'prop1' =>  100,
                'prop2' => '200',
                'prop3' => 'value300'],
            'array_ws_object' => [
                /* 0 => */  100,
                /* 1 => */ '200',
                'item3' => (object)['prop1' =>  1, 'prop2' => '2', 'prop3' => 'value3'],
                'item4' => '400',
                'item5' => 'value500'],
            'object_ws_array' => (object)[
                'prop1' =>  1,
                'prop2' => '2',
                'prop3' => [100, '200', 'item3' => '300', 'item4' => 'value400'],
                'prop4' => 'value4'
            ]
        ];

        $expected = [
            'value_int_negative' => '-1',
            'value_float_negative' => '-1.1',
            'value_float' => '0.0000001',
            'value_int_exponential' => '1230',
            'value_int_hexadecimal' => '291',
            'value_int_octal' => '668',
            'value_int_binary' => '5',
            'value_int_prefix' => "'а123'",
            'value_int_suffix' => "'123а'",
            'value_bool_true' => 'true',
            'value_bool_false' => 'false',
            'value_null' => 'null',
            'array' => "[".
                "0 => 100, ".
                "1 => '200', ".
                "'item3' => 300, ".
                "'item4' => '400', ".
                "'item5' => 'value500']",
            'object' => "(object)[".
                "'prop1' => 100, ".
                "'prop2' => '200', ".
                "'prop3' => 'value300']",
            'array_ws_object' => "[".
                "0 => 100, ".
                "1 => '200', ".
                "'item3' => (object)['prop1' => 1, 'prop2' => '2', 'prop3' => 'value3'], ".
                "'item4' => '400', ".
                "'item5' => 'value500']",
            'object_ws_array' => "(object)[".
                "'prop1' => 1, ".
                "'prop2' => '2', ".
                "'prop3' => [0 => 100, 1 => '200', 'item3' => '300', 'item4' => 'value400'], ".
                "'prop4' => 'value4']"
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::data_stringify($c_value);
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

    static function test_step_code__bytes_to_abbreviated(&$test, $dpath, &$c_results) {
        $data = [
            0,
            1,
            64,
            512,
            1024 + 0,
            1024 - 1,
            1024 + 1,
            1024 * 2 + 0,
            1024 * 2 - 1,
            1024 * 2 + 1,
            1024 * 1024 + 0,
            1024 * 1024 - 1,
            1024 * 1024 + 1,
            1024 * 1024 - 1024,
            1024 * 1024 + 1024,
            1024 * 1024 * 2 + 0,
            1024 * 1024 * 2 - 1,
            1024 * 1024 * 2 + 1,
            1024 * 1024 * 2 - 1024,
            1024 * 1024 * 2 + 1024
        ];

        $expected = [
            '0B',
            '1B',
            '64B',
            '512B',
            '1K',
            '1023B',
            '1025B',
            '2K',
            '2047B',
            '2049B',
            '1M',
            '1048575B',
            '1048577B',
            '1023K',
            '1025K',
            '2M',
            '2097151B',
            '2097153B',
            '2047K',
            '2049K'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::bytes_to_abbreviated($c_value);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected[$c_row_id], 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $expected[$c_row_id], 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__abbreviated_to_bytes(&$test, $dpath, &$c_results) {

        # ─────────────────────────────────────────────────────────────────────
        # nK
        # nB | nnB | nnnnB
        # ─────────────────────────────────────────────────────────────────────

        $data = [
            '0B',
            '8B',
            '64B',
            '512B',
            '1023B',
            '1024B',
            '1025B',
            '1K',
            '1536B',
            '2K',
            '2560B'
        ];

        $expected = [
            0,
            8,
            64,
            512,
            1023,
            1024,
            1025,
            1024, # == 1K   == 1024b
            1536, # == 1.5K == (1024b + 512b)
            2048, # == 2K   == 2048b
            2560  # == 2.5K == (2048b + 512b)
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::abbreviated_to_bytes($c_value);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ─────────────────────────────────────────────────────────────────────
        # nM
        # nK | nnK | nnnK | nnnnK
        # nB | nnB | nnnB | nnnnB | nnnnnB | nnnnnnB …
        # ─────────────────────────────────────────────────────────────────────

        $data = [
            '8K',
            '64K',
            '512K',
            '1023K',
            '1024K',
            '1025K',
            '1M',
            '1536K',
            '2M',
            '2560K',
            '1073741824B'
        ];

        $expected = [
            8192,
            65536,
            524288,
            1047552,
            1048576,
            1049600,
            1048576,   # == 1M   == 1024k == (1024b * 1024b)
            1572864,   # == 1.5M == (1024k + 512k) == (1024b * 1024b + 1024b * 512b)
            2097152,   # == 2M   == 2048k == (1024k * 2) == (1024b * 1024b * 2)
            2621440,   # == 2.5M == 2048k + 512k
            1073741824 # == 1G
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::abbreviated_to_bytes($c_value);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__data_serialize(&$test, $dpath, &$c_results) {
        $value_string        = 'string';
        $value_integer       = 123;
        $value_float         = 0.000001;
        $value_boolean_true  = true;
        $value_boolean_false = false;
        $value_null          = null;
        $value_object_simple = new stdCLass;
        $value_object        = new Test_feed__Core__Serialize;
        $value_array_empty   = [];
        $value_array         = [
            null                  => 'key null',
            'string'              => 'key string',
            123                   => 'key integer',
          # 0.000001              => 'key float',
          # true                  => 'key boolean:true',
          # false                 => 'key boolean:false',
            'value_string'        => $value_string,
            'value_integer'       => $value_integer,
            'value_float'         => $value_float,
            'value_boolean_true'  => $value_boolean_true,
            'value_boolean_false' => $value_boolean_false,
            'value_null'          => $value_null,
            'value_array_empty'   => $value_array_empty,
            'value object simple' => new stdCLass,
            'value object'        => new Test_feed__Core__Serialize
        ];

        $data = [
            'value_string'        => $value_string,
            'value_integer'       => $value_integer,
            'value_float'         => $value_float,
            'value_boolean_true'  => $value_boolean_true,
            'value_boolean_false' => $value_boolean_false,
            'value_null'          => $value_null,
            'value_object_simple' => $value_object_simple,
            'value_object'        => $value_object,
            'value_array'         => $value_array
        ];

        ###############################################################
        ### is_optimized = false (working like standard serialize() ###
        ###############################################################

        $expected = [
            'value_string'        => serialize($value_string),
            'value_integer'       => serialize($value_integer),
            'value_float'         => serialize($value_float),
            'value_boolean_true'  => serialize($value_boolean_true),
            'value_boolean_false' => serialize($value_boolean_false),
            'value_null'          => serialize($value_null),
            'value_object_simple' => serialize($value_object_simple),
            'value_object'        => serialize($value_object),
            'value_array'         => serialize($value_array)
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::data_serialize($c_value, false);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = false)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = false)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        ###########################
        ### is_optimized = true ###
        ###########################

        $expected = [
            'value_string'        => 's:6:"string";',
            'value_integer'       => 'i:123;',
            'value_float'         => 'd:0.000001;',
            'value_boolean_true'  => 'b:1;',
            'value_boolean_false' => 'b:0;',
            'value_null'          => 'N;',
            'value_object_simple' => 'O:8:"stdClass":0:{}',
            'value_object'        => 'O:34:"effcore\Test_feed__Core__Serialize":0:{}',
            'value_array'         => 'a:12:{s:0:"";s:8:"key null";s:6:"string";s:10:"key string";i:123;s:11:"key integer";s:12:"value_string";s:6:"string";s:13:"value_integer";i:123;s:11:"value_float";d:0.000001;s:18:"value_boolean_true";b:1;s:19:"value_boolean_false";b:0;s:10:"value_null";N;s:17:"value_array_empty";a:0:{}s:19:"value object simple";O:8:"stdClass":0:{}s:12:"value object";O:34:"effcore\Test_feed__Core__Serialize":0:{}}'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = Core::data_serialize($c_value);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = true)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = true)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__arrobj_select_values_recursive(&$test, $dpath, &$c_results) {
        $data['type_1']['module_1']['row_id_1'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_1-row_id_1']);
        $data['type_1']['module_1']['row_id_2'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_1-row_id_2']);
        $data['type_1']['module_1']['row_id_3'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_1-row_id_3']);
        $data['type_1']['module_2']['row_id_4'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_2-row_id_4']);
        $data['type_1']['module_2']['row_id_5'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_2-row_id_5']);
        $data['type_1']['module_2']['row_id_6'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_2-row_id_6']);
        $data['type_1']['module_3']['row_id_7'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_3-row_id_7']);
        $data['type_1']['module_3']['row_id_8'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_3-row_id_8']);
        $data['type_1']['module_3']['row_id_9'] = new Test_feed__Core__Select_recursive(['id' => 'type_1-module_3-row_id_9', 'children' => [
            'child_1' => new Test_feed__Core__Select_recursive(['id' => 'type_1-module_3-row_id_9-children-child_1']),
            'child_2' => new Test_feed__Core__Select_recursive(['id' => 'type_1-module_3-row_id_9-children-child_2']),
            'child_3' => new Test_feed__Core__Select_recursive(['id' => 'type_1-module_3-row_id_9-children-child_3']),
        ]]);

        #################################
        ### is_parent_at_last = false ###
        #################################

        $expected = [
            'type_1',
            'type_1/module_1',
            'type_1/module_1/row_id_1',
            'type_1/module_1/row_id_1/id',
            'type_1/module_1/row_id_1/children',
            'type_1/module_1/row_id_2',
            'type_1/module_1/row_id_2/id',
            'type_1/module_1/row_id_2/children',
            'type_1/module_1/row_id_3',
            'type_1/module_1/row_id_3/id',
            'type_1/module_1/row_id_3/children',
            'type_1/module_2',
            'type_1/module_2/row_id_4',
            'type_1/module_2/row_id_4/id',
            'type_1/module_2/row_id_4/children',
            'type_1/module_2/row_id_5',
            'type_1/module_2/row_id_5/id',
            'type_1/module_2/row_id_5/children',
            'type_1/module_2/row_id_6',
            'type_1/module_2/row_id_6/id',
            'type_1/module_2/row_id_6/children',
            'type_1/module_3',
            'type_1/module_3/row_id_7',
            'type_1/module_3/row_id_7/id',
            'type_1/module_3/row_id_7/children',
            'type_1/module_3/row_id_8',
            'type_1/module_3/row_id_8/id',
            'type_1/module_3/row_id_8/children',
            'type_1/module_3/row_id_9',
            'type_1/module_3/row_id_9/id',
            'type_1/module_3/row_id_9/children',
            'type_1/module_3/row_id_9/children/child_1',
            'type_1/module_3/row_id_9/children/child_1/id',
            'type_1/module_3/row_id_9/children/child_1/children',
            'type_1/module_3/row_id_9/children/child_2',
            'type_1/module_3/row_id_9/children/child_2/id',
            'type_1/module_3/row_id_9/children/child_2/children',
            'type_1/module_3/row_id_9/children/child_3',
            'type_1/module_3/row_id_9/children/child_3/id',
            'type_1/module_3/row_id_9/children/child_3/children'
        ];

        $gotten = array_keys(Core::arrobj_select_values_recursive($data));
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = false)', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = false)', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($expected))]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($gotten))]);
            $c_results['return'] = 0;
            return;
        }

        ################################
        ### is_parent_at_last = true ###
        ################################

        $expected = [
            'type_1/module_1/row_id_1/id',
            'type_1/module_1/row_id_1/children',
            'type_1/module_1/row_id_1',
            'type_1/module_1/row_id_2/id',
            'type_1/module_1/row_id_2/children',
            'type_1/module_1/row_id_2',
            'type_1/module_1/row_id_3/id',
            'type_1/module_1/row_id_3/children',
            'type_1/module_1/row_id_3',
            'type_1/module_1',
            'type_1/module_2/row_id_4/id',
            'type_1/module_2/row_id_4/children',
            'type_1/module_2/row_id_4',
            'type_1/module_2/row_id_5/id',
            'type_1/module_2/row_id_5/children',
            'type_1/module_2/row_id_5',
            'type_1/module_2/row_id_6/id',
            'type_1/module_2/row_id_6/children',
            'type_1/module_2/row_id_6',
            'type_1/module_2',
            'type_1/module_3/row_id_7/id',
            'type_1/module_3/row_id_7/children',
            'type_1/module_3/row_id_7',
            'type_1/module_3/row_id_8/id',
            'type_1/module_3/row_id_8/children',
            'type_1/module_3/row_id_8',
            'type_1/module_3/row_id_9/id',
            'type_1/module_3/row_id_9/children/child_1/id',
            'type_1/module_3/row_id_9/children/child_1/children',
            'type_1/module_3/row_id_9/children/child_1',
            'type_1/module_3/row_id_9/children/child_2/id',
            'type_1/module_3/row_id_9/children/child_2/children',
            'type_1/module_3/row_id_9/children/child_2',
            'type_1/module_3/row_id_9/children/child_3/id',
            'type_1/module_3/row_id_9/children/child_3/children',
            'type_1/module_3/row_id_9/children/child_3',
            'type_1/module_3/row_id_9/children',
            'type_1/module_3/row_id_9',
            'type_1/module_3',
            'type_1'
        ];

        $gotten = array_keys(Core::arrobj_select_values_recursive($data, true));
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = true)', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = true)', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded(serialize($expected))]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded(serialize($gotten))]);
            $c_results['return'] = 0;
            return;
        }
    }

    static function test_step_code__data_to_attributes(&$test, $dpath, &$c_results) {
        $data = [
            'value_string'             => 'text',
            'value_string_empty'       => '',
            'value_string_true'        => 'true',
            'value_string_false'       => 'false',
            'value_integer'            => 123,
            'value_float'              => 0.000001,
            'value_boolean_true'       => true,
            'value_boolean_false'      => false,
            'value_null'               => null,
            'value_object_text'        => new Text('some translated text'),
            'value_object_text_simple' => new Text_simple('some raw text'),
            'value_object_empty'       => (object)[],
            'value_object'             => (object)['property_1' => 'value 1', 'property_2' => 'value 2', 'property_3' => 'value 3'],
            'value_object_nested'      => (object)[0 => (object)[0 => 'nested value']],
            'value_resource'           => fopen(DIR_ROOT.'license.md', 'r'),
            'value_array_empty'        => [],
            'value_array_nested'       => [
                'value_nested_string'             => 'nested text',
                'value_nested_string_empty'       => '',
                'value_nested_string_true'        => 'true',
                'value_nested_string_false'       => 'false',
                'value_nested_integer'            => 456,
                'value_nested_float'              => 0.000002,
                'value_nested_boolean_true'       => true,
                'value_nested_boolean_false'      => false,
                'value_nested_null'               => null,
                'value_nested_object_text'        => new Text('some nested translated text'),
                'value_nested_object_text_simple' => new Text_simple('some nested raw text'),
                'value_nested_object_empty'       => (object)[],
                'value_nested_object'             => (object)['property_4' => 'value 4', 'property_5' => 'value 5', 'property_6' => 'value 6'],
                'value_nested_object_nested'      => (object)[0 => (object)[0 => 'nested/nested value']],
                'value_nested_resource'           => fopen(DIR_ROOT.'license.md', 'r'),
                'value_nested_array_empty'        => []
            ]
        ];

        $expected = 'value_string="text" '.
                    'value_string_empty="" '.
                    'value_string_true="true" '.
                    'value_string_false="false" '.
                    'value_integer="123" '.
                    'value_float="0.000001" '.
                    'value_boolean_true '.
                    'value_object_text="some translated text" '.
                    'value_object_text_simple="some raw text" '.
                    'value_object_empty="__NO_RENDERER__" '.
                    'value_object="__NO_RENDERER__" '.
                    'value_object_nested="__NO_RENDERER__" '.
                    'value_resource="__UNSUPPORTED_TYPE__" '.
                    'value_array_nested="nested text true false 456 0.000002 __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__"';

        $gotten = Core::data_to_attributes($data);
        $result = $gotten === $expected;
        if ($result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('success'))->render()]);
        if ($result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($expected)]);
            $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($gotten)]);
            $c_results['return'] = 0;
            return;
        }
    }

    static function test_step_code__validate_range(&$test, $dpath, &$c_results) {
        $data = [
            '0.000002 & -2' => Core::validate_range('-1', '1', '0.000002', '-2'),
            '0.000002 & -1' => Core::validate_range('-1', '1', '0.000002', '-1'),
            '0.000002 & +0' => Core::validate_range('-1', '1', '0.000002',  '0'),
            '0.000002 & +1' => Core::validate_range('-1', '1', '0.000002',  '1'),
            '0.000002 & +2' => Core::validate_range('-1', '1', '0.000002',  '2'),
            '0.000003 & -2' => Core::validate_range('-1', '1', '0.000003', '-2'),
            '0.000003 & -1' => Core::validate_range('-1', '1', '0.000003', '-1'),
            '0.000003 & +0' => Core::validate_range('-1', '1', '0.000003',  '0'),
            '0.000003 & +1' => Core::validate_range('-1', '1', '0.000003',  '1'),
            '0.000003 & +2' => Core::validate_range('-1', '1', '0.000003',  '2')
        ];

        $expected = [
            '0.000002 & -2' => false,
            '0.000002 & -1' => true,
            '0.000002 & +0' => true,
            '0.000002 & +1' => true,
            '0.000002 & +2' => false,
            '0.000003 & -2' => false,
            '0.000003 & -1' => true,
            '0.000003 & +0' => false,
            '0.000003 & +1' => true,
            '0.000003 & +2' => false
        ];

        foreach ($data as $c_row_id => $c_gotten) {
            $c_expected = $expected[$c_row_id];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_expected ? 'true' : 'false']);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => $c_gotten ? 'true' : 'false']);
                $c_results['return'] = 0;
                return;
            }
        }

        ###########################################
        ### test for range: -1000000 … +1000000 ###
        ###########################################

        $f_min  = '-1.0';
        $f_max  =  '1.0';
        $f_step =  '0.000003';
        $i_min  =  -1000000;
        $i_max  =  +1000000;
        $i_step =  +3;

        for ($i = 0; $i < 1000000; $i++) {
            $c_f_value = bcmul($i, '0.000001', 6);
            $c_gotten_i = Core::validate_range($f_min, $f_max, $f_step, $c_f_value);
            $c_gotten_f = Core::validate_range($i_min, $i_max, $i_step, $i);
            $c_gotten_alternative = (($i - $i_min) % $i_step) === 0;
            if ($c_gotten_i !== $c_gotten_alternative ||
                $c_gotten_f !== $c_gotten_alternative) {
                $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'range: -1000000 … +1000000', 'result' => (new Text('failure'))->render()]);
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_gotten_i]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"',    ['value' => $c_gotten_alternative]);
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => $c_gotten_f]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"',    ['value' => $c_gotten_alternative]);
                $c_results['reports'][$dpath][] = $c_f_value.' | f_min = '    .$f_min.
                                                             ' | f_max = '    .$f_max.
                                                             ' | f_step = '   .$f_step.
                                                             ' | c_f_value = '.$c_f_value.
                                                             ' | i_min = '    .$i_min.
                                                             ' | i_max = '    .$i_max.
                                                             ' | i_step = '   .$i_step.
                                                             ' | i = '        .$i;
                $c_results['return'] = 0;
                return;
            }
        }

        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', [
            'id' => 'range: -1000000 … +1000000', 'result' => (new Text('success')
        )->render()]);
    }

    static function test_step_code__validate_mime_type(&$test, $dpath, &$c_results) {
        $data = [
            'application/atom+xml',
            'application/EDI-X12',
            'application/EDIFACT',
            'application/font-sfnt',
            'application/font-woff',
            'application/font-woff2',
            'application/java-archive',
            'application/javascript',
            'application/json',
            'application/msword',
            'application/octet-stream',
            'application/ogg',
            'application/pdf',
            'application/rss+xml',
            'application/rtf',
            'application/vnd.google-earth.kml+xml',
            'application/vnd.mozilla.xul+xml',
            'application/vnd.ms-excel',
            'application/vnd.ms-fontobject',
            'application/vnd.ms-powerpoint',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/x-7z-compressed',
            'application/x-bzip',
            'application/x-bzip2',
            'application/x-compressed',
            'application/x-dvi',
            'application/x-font-opentype',
            'application/x-font-ttf',
            'application/x-gzip',
            'application/x-iwork-keynote-sffkey',
            'application/x-iwork-numbers-sffnumbers',
            'application/x-iwork-pages-sffpages',
            'application/x-javascript',
            'application/x-latex',
            'application/x-newton-compatible-pkg',
            'application/x-pkcs12',
            'application/x-pkcs7-certificates',
            'application/x-pkcs7-certreqresp',
            'application/x-pkcs7-mime',
            'application/x-pkcs7-signature',
            'application/x-rar-compressed',
            'application/x-redhat-package-manager',
            'application/x-shockwave-flash',
            'application/x-stuffit',
            'application/x-tar',
            'application/x-www-form-urlencoded',
            'application/x-x509-ca-cert',
            'application/xhtml+xml',
            'application/zip',
            'audio/aac',
            'audio/aiff',
            'audio/flac',
            'audio/midi',
            'audio/mpeg',
            'audio/ogg',
            'audio/wav',
            'audio/x-m4a',
            'audio/x-ms-wma',
            'image/gif',
            'image/jpeg',
            'image/png',
            'image/svg+xml',
            'image/tiff',
            'image/webp',
            'image/x-icon',
            'image/x-ms-bmp',
            'multipart/alternative',
            'multipart/encrypted',
            'multipart/form-data',
            'multipart/mixed',
            'multipart/related',
            'multipart/signed',
            'text/cmd',
            'text/css',
            'text/csv',
            'text/html',
            'text/javascript',
            'text/markdown',
            'text/php',
            'text/plain',
            'text/x-jquery-tmpl',
            'text/xml',
            'video/3gpp',
            'video/3gpp2',
            'video/mp4',
            'video/mpeg',
            'video/ogg',
            'video/quicktime',
            'video/webm',
            'video/x-flv',
            'video/x-m4v',
            'video/x-matroska',
            'video/x-ms-wmv',
            'video/x-msvideo'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $c_value;
            $c_gotten = Core::validate_mime_type($c_value);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: "%%_value"',  ['value' => Core::return_encoded($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: "%%_value"', ['value' => Core::return_encoded($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
