<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\DIR_ROOT;
use effcore\Core;
use effcore\Test_feed__Core__Select_recursive;
use effcore\Test_feed__Core__Serialize;
use effcore\Test;
use effcore\Text_RAW;
use effcore\Text_simple;
use effcore\Text;
use stdCLass;

abstract class Events_Test__Class_Core {

    static function test_step_code__gettype(&$test, $dpath) {
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

        foreach ($data as $c_row_id => $с_received) {
            $c_expected = $expected[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__in_array(&$test, $dpath) {
        $data = [
            'value_string_empty' => Core::in_array(''  , ['']),
            'value_null'         => Core::in_array(null, ['']),
            'value_int_0'        => Core::in_array(0   , ['']),
            'value_string_0'     => Core::in_array('0' , [''])
        ];

        $expected = [
            'value_string_empty' => true,
            'value_null' => true,
            'value_int_0' => false,
            'value_string_0' => false
        ];

        foreach ($data as $c_row_id => $с_received) {
            $c_expected = $expected[$c_row_id];
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__sort(&$test, $dpath) {
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

        $received = Core::array_sort_by_number($data, 'weight', Core::SORT_ASC);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
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

        $received = Core::array_sort_by_number($data, 'weight', Core::SORT_DSC);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'array_sort_by_number:Core::SORT_ASC', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__fractional_part_length_get(&$test, $dpath) {
        $data = [
            ['value' => ''                        , 'no_zeros' => false, 'expected' => 0 ],
            ['value' => '100'                     , 'no_zeros' => false, 'expected' => 0 ],
            ['value' => '0'                       , 'no_zeros' => false, 'expected' => 0 ],
            ['value' => '0.00100'                 , 'no_zeros' => false, 'expected' => 5 ],
            ['value' => '123456789.1'             , 'no_zeros' => false, 'expected' => 1 ],
            ['value' => '123456789.12'            , 'no_zeros' => false, 'expected' => 2 ],
            ['value' => '123456789.123'           , 'no_zeros' => false, 'expected' => 3 ],
            ['value' => '123456789.1234'          , 'no_zeros' => false, 'expected' => 4 ],
            ['value' => '123456789.12345'         , 'no_zeros' => false, 'expected' => 5 ],
            ['value' => '123456789.123456'        , 'no_zeros' => false, 'expected' => 6 ],
            ['value' => '123456789.1234567'       , 'no_zeros' => false, 'expected' => 7 ],
            ['value' => '123456789.12345678'      , 'no_zeros' => false, 'expected' => 8 ],
            ['value' => '123456789.123456789'     , 'no_zeros' => false, 'expected' => 9 ],
            ['value' => '123456789.100000'        , 'no_zeros' => false, 'expected' => 6 ],
            ['value' => '123456789.1200000'       , 'no_zeros' => false, 'expected' => 7 ],
            ['value' => '123456789.12300000'      , 'no_zeros' => false, 'expected' => 8 ],
            ['value' => '123456789.123400000'     , 'no_zeros' => false, 'expected' => 9 ],
            ['value' => '123456789.1234500000'    , 'no_zeros' => false, 'expected' => 10],
            ['value' => '123456789.12345600000'   , 'no_zeros' => false, 'expected' => 11],
            ['value' => '123456789.123456700000'  , 'no_zeros' => false, 'expected' => 12],
            ['value' => '123456789.1234567800000' , 'no_zeros' => false, 'expected' => 13],
            ['value' => '123456789.12345678900000', 'no_zeros' => false, 'expected' => 14],
            ['value' => ''                        , 'no_zeros' => true , 'expected' => 0 ],
            ['value' => '100'                     , 'no_zeros' => true , 'expected' => 0 ],
            ['value' => '0'                       , 'no_zeros' => true , 'expected' => 0 ],
            ['value' => '0.00100'                 , 'no_zeros' => true , 'expected' => 3 ],
            ['value' => '123456789.1'             , 'no_zeros' => true , 'expected' => 1 ],
            ['value' => '123456789.12'            , 'no_zeros' => true , 'expected' => 2 ],
            ['value' => '123456789.123'           , 'no_zeros' => true , 'expected' => 3 ],
            ['value' => '123456789.1234'          , 'no_zeros' => true , 'expected' => 4 ],
            ['value' => '123456789.12345'         , 'no_zeros' => true , 'expected' => 5 ],
            ['value' => '123456789.123456'        , 'no_zeros' => true , 'expected' => 6 ],
            ['value' => '123456789.1234567'       , 'no_zeros' => true , 'expected' => 7 ],
            ['value' => '123456789.12345678'      , 'no_zeros' => true , 'expected' => 8 ],
            ['value' => '123456789.123456789'     , 'no_zeros' => true , 'expected' => 9 ],
            ['value' => '123456789.100000'        , 'no_zeros' => true , 'expected' => 1 ],
            ['value' => '123456789.1200000'       , 'no_zeros' => true , 'expected' => 2 ],
            ['value' => '123456789.12300000'      , 'no_zeros' => true , 'expected' => 3 ],
            ['value' => '123456789.123400000'     , 'no_zeros' => true , 'expected' => 4 ],
            ['value' => '123456789.1234500000'    , 'no_zeros' => true , 'expected' => 5 ],
            ['value' => '123456789.12345600000'   , 'no_zeros' => true , 'expected' => 6 ],
            ['value' => '123456789.123456700000'  , 'no_zeros' => true , 'expected' => 7 ],
            ['value' => '123456789.1234567800000' , 'no_zeros' => true , 'expected' => 8 ],
            ['value' => '123456789.12345678900000', 'no_zeros' => true , 'expected' => 9 ],
            ['value' => 100                       , 'no_zeros' => false, 'expected' => 40],
            ['value' => 0                         , 'no_zeros' => false, 'expected' => 40],
            ['value' => 0.00100                   , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1               , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.12              , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.123             , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1234            , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.12345           , 'no_zeros' => false, 'expected' => 40],
            ['value' =>  23456789.123456          , 'no_zeros' => false, 'expected' => 40],
            ['value' =>   3456789.1234567         , 'no_zeros' => false, 'expected' => 40],
            ['value' =>     56789.12345678        , 'no_zeros' => false, 'expected' => 40],
            ['value' =>      6789.123456789       , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.100000          , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1200000         , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.12300000        , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.123400000       , 'no_zeros' => false, 'expected' => 40],
            ['value' => 123456789.1234500000      , 'no_zeros' => false, 'expected' => 40],
            ['value' =>  23456789.12345600000     , 'no_zeros' => false, 'expected' => 40],
            ['value' =>   3456789.123456700000    , 'no_zeros' => false, 'expected' => 40],
            ['value' =>    456789.1234567800000   , 'no_zeros' => false, 'expected' => 40],
            ['value' =>     56789.12345678900000  , 'no_zeros' => false, 'expected' => 40],
            ['value' =>      6789.123456789000000 , 'no_zeros' => false, 'expected' => 40],
            ['value' => 100                       , 'no_zeros' => true , 'expected' => 0 ],
            ['value' => 0                         , 'no_zeros' => true , 'expected' => 0 ],
            ['value' => 0.00100                   , 'no_zeros' => true , 'expected' => 3 ],
            ['value' => 123456789.1               , 'no_zeros' => true , 'expected' => 1 ],
            ['value' => 123456789.12              , 'no_zeros' => true , 'expected' => 2 ],
            ['value' => 123456789.123             , 'no_zeros' => true , 'expected' => 3 ],
            ['value' => 123456789.1234            , 'no_zeros' => true , 'expected' => 4 ],
            ['value' => 123456789.12345           , 'no_zeros' => true , 'expected' => 5 ],
            ['value' =>  23456789.123456          , 'no_zeros' => true , 'expected' => 6 ],
            ['value' =>   3456789.1234567         , 'no_zeros' => true , 'expected' => 7 ],
            ['value' =>     56789.12345678        , 'no_zeros' => true , 'expected' => 8 ],
            ['value' =>      6789.123456789       , 'no_zeros' => true , 'expected' => 9 ],
            ['value' => 123456789.100000          , 'no_zeros' => true , 'expected' => 1 ],
            ['value' => 123456789.1200000         , 'no_zeros' => true , 'expected' => 2 ],
            ['value' => 123456789.12300000        , 'no_zeros' => true , 'expected' => 3 ],
            ['value' => 123456789.123400000       , 'no_zeros' => true , 'expected' => 4 ],
            ['value' => 123456789.1234500000      , 'no_zeros' => true , 'expected' => 5 ],
            ['value' =>  23456789.12345600000     , 'no_zeros' => true , 'expected' => 6 ],
            ['value' =>   3456789.123456700000    , 'no_zeros' => true , 'expected' => 7 ],
            ['value' =>    456789.1234567800000   , 'no_zeros' => true , 'expected' => 8 ],
            ['value' =>     56789.12345678900000  , 'no_zeros' => true , 'expected' => 9 ],
            ['value' =>      6789.123456789000000 , 'no_zeros' => true , 'expected' => 9 ]
        ];

        foreach ($data as $c_row_id => $c_info) {
            $c_is_no_zeros = $c_info['no_zeros'];
            $c_expected = $c_info['expected'];
            $c_value = $c_info['value'];
            $с_received = Core::fractional_part_length_get($c_value, $c_is_no_zeros);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value.' (no_zeros = '.($c_is_no_zeros ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value.' (no_zeros = '.($c_is_no_zeros ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__exponencial_string_normalize(&$test, $dpath) {
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
            $с_received = Core::exponencial_string_normalize($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__data_stringify(&$test, $dpath) {
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
            'array_with_object' => [
                /* 0 => */  100,
                /* 1 => */ '200',
                'item3' => (object)['prop1' =>  1, 'prop2' => '2', 'prop3' => 'value3'],
                'item4' => '400',
                'item5' => 'value500'],
            'object_with_array' => (object)[
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
            'array_with_object' => "[".
                "0 => 100, ".
                "1 => '200', ".
                "'item3' => (object)['prop1' => 1, 'prop2' => '2', 'prop3' => 'value3'], ".
                "'item4' => '400', ".
                "'item5' => 'value500']",
            'object_with_array' => "(object)[".
                "'prop1' => 1, ".
                "'prop2' => '2', ".
                "'prop3' => [0 => 100, 1 => '200', 'item3' => '300', 'item4' => 'value400'], ".
                "'prop4' => 'value4']"
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $с_received = Core::data_stringify($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__bytes_to_abbreviated(&$test, $dpath) {
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
            $с_received = Core::bytes_to_abbreviated($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $expected[$c_row_id], 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $expected[$c_row_id], 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__abbreviated_to_bytes(&$test, $dpath) {

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
            $с_received = Core::abbreviated_to_bytes($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
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
            $с_received = Core::abbreviated_to_bytes($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__data_serialize(&$test, $dpath) {

        $feed_class_1 = new Test_feed__Core__Serialize;
        $feed_class_1->prop_string = 'new string 1';

        $feed_class_2 = new Test_feed__Core__Serialize;
        $feed_class_2->prop_string = 'new string 2';

        $data_raw = [
            'string'        => 'string',
            'integer'       => 123,
            'float'         => 0.000001,
            'boolean_true'  => true,
            'boolean_false' => false,
            'null'          => null,
            'object_simple' => new stdCLass,
            'object'        => $feed_class_1,
            'array_empty'   => [],
            'array'         => [
                null                  => 'key null',
                'string'              => 'key string',
                123                   => 'key integer',
              # 0.000001              => 'key float',
              # true                  => 'key boolean:true',
              # false                 => 'key boolean:false',
                'value_string'        => 'string',
                'value_integer'       => 123,
                'value_float'         => 0.000001,
                'value_boolean_true'  => true,
                'value_boolean_false' => false,
                'value_null'          => null,
                'value_array_empty'   => [],
                'value_object_simple' => new stdCLass,
                'value_object'        => $feed_class_2
            ]
        ];

        $data = [
            'value_string'        => $data_raw['string'],
            'value_integer'       => $data_raw['integer'],
            'value_float'         => $data_raw['float'],
            'value_boolean_true'  => $data_raw['boolean_true'],
            'value_boolean_false' => $data_raw['boolean_false'],
            'value_null'          => $data_raw['null'],
            'value_object_simple' => $data_raw['object_simple'],
            'value_object'        => $data_raw['object'],
            'value_array'         => $data_raw['array'],
            'value_mixed'         => $data_raw
        ];

        ###############################################################
        ### is_optimized = false (working like standard serialize() ###
        ###############################################################

        $expected = [
            'value_string'        => serialize($data_raw['string']),
            'value_integer'       => serialize($data_raw['integer']),
            'value_float'         => serialize($data_raw['float']),
            'value_boolean_true'  => serialize($data_raw['boolean_true']),
            'value_boolean_false' => serialize($data_raw['boolean_false']),
            'value_null'          => serialize($data_raw['null']),
            'value_object_simple' => serialize($data_raw['object_simple']),
            'value_object'        => serialize($data_raw['object']),
            'value_array'         => serialize($data_raw['array']),
            'value_mixed'         => serialize($data_raw)
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $с_received = Core::data_serialize($c_value, false);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = false)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = false)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        ###########################
        ### is_optimized = true ###
        ###########################

        $expected = [
            'value_string'        => serialize($data_raw['string']),
            'value_integer'       => serialize($data_raw['integer']),
            'value_boolean_true'  => serialize($data_raw['boolean_true']),
            'value_boolean_false' => serialize($data_raw['boolean_false']),
            'value_null'          => serialize($data_raw['null']),
            'value_object_simple' => serialize($data_raw['object_simple']),
            'value_float'         => 'd:0.000001;',
            'value_object' => 'O:34:"effcore\Test_feed__Core__Serialize":1:{'.
                's:11:"prop_string";s:12:"new string 1";'.
            '}',
            'value_array' =>
                'a:12:{'.
                    's:0:"";s:8:"key null";'.
                    's:6:"string";s:10:"key string";'.
                    'i:123;s:11:"key integer";'.
                    's:12:"value_string";s:6:"string";'.
                    's:13:"value_integer";i:123;'.
                    's:11:"value_float";d:0.000001;'.
                    's:18:"value_boolean_true";b:1;'.
                    's:19:"value_boolean_false";b:0;'.
                    's:10:"value_null";N;'.
                    's:17:"value_array_empty";a:0:{}'.
                    's:19:"value_object_simple";O:8:"stdClass":0:{}'.
                    's:12:"value_object";O:34:"effcore\Test_feed__Core__Serialize":1:{'.
                        's:11:"prop_string";s:12:"new string 2";'.
                    '}'.
                '}',
            'value_mixed' =>
                'a:10:{'.
                    's:6:"string";s:6:"string";'.
                    's:7:"integer";i:123;'.
                    's:5:"float";d:0.000001;'.
                    's:12:"boolean_true";b:1;'.
                    's:13:"boolean_false";b:0;'.
                    's:4:"null";N;'.
                    's:13:"object_simple";O:8:"stdClass":0:{}'.
                    's:6:"object";O:34:"effcore\Test_feed__Core__Serialize":1:{'.
                        's:11:"prop_string";s:12:"new string 1";'.
                    '}'.
                    's:11:"array_empty";a:0:{}'.
                    's:5:"array";a:12:{'.
                        's:0:"";s:8:"key null";'.
                        's:6:"string";s:10:"key string";'.
                        'i:123;s:11:"key integer";'.
                        's:12:"value_string";s:6:"string";'.
                        's:13:"value_integer";i:123;'.
                        's:11:"value_float";d:0.000001;'.
                        's:18:"value_boolean_true";b:1;'.
                        's:19:"value_boolean_false";b:0;'.
                        's:10:"value_null";N;'.
                        's:17:"value_array_empty";a:0:{}'.
                        's:19:"value_object_simple";O:8:"stdClass":0:{}'.
                        's:12:"value_object";O:34:"effcore\Test_feed__Core__Serialize":1:{'.
                            's:11:"prop_string";s:12:"new string 2";'.
                        '}'.
                    '}'.
                '}'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $с_received = Core::data_serialize($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = true)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = true)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }

        ##############################################
        ### is_optimized = false | is_ksort = true ###
        ##############################################

        $expected = [
            'value_string'        => serialize($data_raw['string']),
            'value_integer'       => serialize($data_raw['integer']),
            'value_boolean_true'  => serialize($data_raw['boolean_true']),
            'value_boolean_false' => serialize($data_raw['boolean_false']),
            'value_null'          => serialize($data_raw['null']),
            'value_object_simple' => serialize($data_raw['object_simple']),
            'value_float'         => serialize($data_raw['float']),
            'value_object' =>
                'O:34:"effcore\Test_feed__Core__Serialize":7:{'.
                    's:10:"prop_array";a:10:{'.
                        's:0:"";s:8:"key null";'.
                        'i:123;s:11:"key integer";'.
                        's:6:"string";s:10:"key string";'.
                        's:17:"value_array_empty";a:0:{}'.
                        's:19:"value_boolean_false";b:0;'.
                        's:18:"value_boolean_true";b:1;'.
                        's:11:"value_float";d:1.0E-6;'.
                        's:13:"value_integer";i:123;'.
                        's:10:"value_null";N;'.
                        's:12:"value_string";s:6:"string";'.
                    '}'.
                    's:18:"prop_boolean_false";b:0;'.
                    's:17:"prop_boolean_true";b:1;'.
                    's:10:"prop_float";d:1.0E-6;'.
                    's:12:"prop_integer";i:123;'.
                    's:9:"prop_null";N;'.
                    's:11:"prop_string";'.
                    's:12:"new string 1";'.
                '}',
            'value_array' =>
                'a:12:{'.
                    's:0:"";s:8:"key null";'.
                    'i:123;s:11:"key integer";'.
                    's:6:"string";s:10:"key string";'.
                    's:17:"value_array_empty";a:0:{}'.
                    's:19:"value_boolean_false";b:0;'.
                    's:18:"value_boolean_true";b:1;'.
                    's:11:"value_float";d:1.0E-6;'.
                    's:13:"value_integer";i:123;'.
                    's:10:"value_null";N;'.
                    's:12:"value_object";O:34:"effcore\Test_feed__Core__Serialize":7:{'.
                        's:10:"prop_array";a:10:{'.
                            's:0:"";s:8:"key null";'.
                            'i:123;s:11:"key integer";'.
                            's:6:"string";s:10:"key string";'.
                            's:17:"value_array_empty";a:0:{}'.
                            's:19:"value_boolean_false";b:0;'.
                            's:18:"value_boolean_true";b:1;'.
                            's:11:"value_float";d:1.0E-6;'.
                            's:13:"value_integer";i:123;'.
                            's:10:"value_null";N;'.
                            's:12:"value_string";s:6:"string";'.
                        '}'.
                        's:18:"prop_boolean_false";b:0;'.
                        's:17:"prop_boolean_true";b:1;'.
                        's:10:"prop_float";d:1.0E-6;'.
                        's:12:"prop_integer";i:123;'.
                        's:9:"prop_null";N;'.
                        's:11:"prop_string";s:12:"new string 2";'.
                    '}'.
                    's:19:"value_object_simple";O:8:"stdClass":0:{}'.
                    's:12:"value_string";s:6:"string";'.
                '}',
            'value_mixed' =>
                'a:10:{'.
                    's:5:"array";a:12:{'.
                        's:0:"";s:8:"key null";'.
                        'i:123;s:11:"key integer";'.
                        's:6:"string";s:10:"key string";'.
                        's:17:"value_array_empty";a:0:{}'.
                        's:19:"value_boolean_false";b:0;'.
                        's:18:"value_boolean_true";b:1;'.
                        's:11:"value_float";d:1.0E-6;'.
                        's:13:"value_integer";i:123;'.
                        's:10:"value_null";N;'.
                        's:12:"value_object";O:34:"effcore\Test_feed__Core__Serialize":7:{'.
                            's:10:"prop_array";a:10:{'.
                                's:0:"";s:8:"key null";'.
                                'i:123;s:11:"key integer";'.
                                's:6:"string";s:10:"key string";'.
                                's:17:"value_array_empty";a:0:{}'.
                                's:19:"value_boolean_false";b:0;'.
                                's:18:"value_boolean_true";b:1;'.
                                's:11:"value_float";d:1.0E-6;'.
                                's:13:"value_integer";i:123;'.
                                's:10:"value_null";N;'.
                                's:12:"value_string";s:6:"string";'.
                            '}'.
                            's:18:"prop_boolean_false";b:0;'.
                            's:17:"prop_boolean_true";b:1;'.
                            's:10:"prop_float";d:1.0E-6;'.
                            's:12:"prop_integer";i:123;'.
                            's:9:"prop_null";N;'.
                            's:11:"prop_string";s:12:"new string 2";'.
                        '}'.
                        's:19:"value_object_simple";O:8:"stdClass":0:{}'.
                        's:12:"value_string";s:6:"string";'.
                    '}'.
                    's:11:"array_empty";a:0:{}'.
                    's:13:"boolean_false";b:0;'.
                    's:12:"boolean_true";b:1;'.
                    's:5:"float";d:1.0E-6;'.
                    's:7:"integer";i:123;'.
                    's:4:"null";N;'.
                    's:6:"object";O:34:"effcore\Test_feed__Core__Serialize":7:{'.
                        's:10:"prop_array";a:10:{'.
                            's:0:"";s:8:"key null";'.
                            'i:123;s:11:"key integer";'.
                            's:6:"string";s:10:"key string";'.
                            's:17:"value_array_empty";a:0:{}'.
                            's:19:"value_boolean_false";b:0;'.
                            's:18:"value_boolean_true";b:1;'.
                            's:11:"value_float";d:1.0E-6;'.
                            's:13:"value_integer";i:123;'.
                            's:10:"value_null";N;'.
                            's:12:"value_string";s:6:"string";'.
                        '}'.
                        's:18:"prop_boolean_false";b:0;'.
                        's:17:"prop_boolean_true";b:1;'.
                        's:10:"prop_float";d:1.0E-6;'.
                        's:12:"prop_integer";i:123;'.
                        's:9:"prop_null";N;'.
                        's:11:"prop_string";s:12:"new string 1";'.
                    '}'.
                    's:13:"object_simple";O:8:"stdClass":0:{}'.
                    's:6:"string";s:6:"string";'.
                '}'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected[$c_row_id];
            $с_received = Core::data_serialize($c_value, false, true);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = false, ksort = true)', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id.' (is_optimized = false, ksort = true)', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__arrobj_select_values_recursive(&$test, $dpath) {
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

        $received = array_keys(Core::arrobj_select_values_recursive($data));
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = false)', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = false)', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
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

        $received = array_keys(Core::arrobj_select_values_recursive($data, true));
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = true)', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* (is_parent_at_last = true)', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__data_to_attributes(&$test, $dpath) {
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
            'value_raw'                => new Text_RAW('some_raw_text'),
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
                    'some_raw_text '.
                    'value_object_text="some translated text" '.
                    'value_object_text_simple="some raw text" '.
                    'value_object_empty="__NO_RENDERER__" '.
                    'value_object="__NO_RENDERER__" '.
                    'value_object_nested="__NO_RENDERER__" '.
                    'value_resource="__UNSUPPORTED_TYPE__" '.
                    'value_array_nested="nested text true false 456 0.000002 value_nested_boolean_true some nested translated text some nested raw text __NO_RENDERER__ __NO_RENDERER__ __NO_RENDERER__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__"';

        $received = Core::data_to_attributes($data);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '*', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_for_XML = 'value_string="text" '.
                    'value_string_empty="" '.
                    'value_string_true="true" '.
                    'value_string_false="false" '.
                    'value_integer="123" '.
                    'value_float="0.000001" '.
                    'value_boolean_true="value_boolean_true" '.
                    'some_raw_text '.
                    'value_object_text="some translated text" '.
                    'value_object_text_simple="some raw text" '.
                    'value_object_empty="__NO_RENDERER__" '.
                    'value_object="__NO_RENDERER__" '.
                    'value_object_nested="__NO_RENDERER__" '.
                    'value_resource="__UNSUPPORTED_TYPE__" '.
                    'value_array_nested="nested text true false 456 0.000002 value_nested_boolean_true some nested translated text some nested raw text __NO_RENDERER__ __NO_RENDERER__ __NO_RENDERER__ __UNSUPPORTED_TYPE__ __UNSUPPORTED_TYPE__"';

        $received = Core::data_to_attributes($data, true);
        $result = $received === $expected_for_XML;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* + XML', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => '* + XML', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected_for_XML)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    # hex ip to ip platform differences:
    # ┌──────────────────────────────────┬─────────────────────────────────────────┐
    # │ hex-ip                           │                                      ip │
    # ├──────────────────────────────────┼─────────────────────────────────────────┤
    # │ 00000000000000000000000000000000 │                                      :: │
    # │ 0000000000000000000000000000000f │                              ::0.0.0.15 │
    # │ 000000000000000000000000000000ff │                             ::0.0.0.255 │
    # │ 00000000000000000000000000000fff │                            ::0.0.15.255 │
    # │ 0000000000000000000000000000ffff │                           ::0.0.255.255 │
    # │ 000000000000000000000000000fffff │                          ::0.15.255.255 │
    # │ 00000000000000000000000000ffffff │                         ::0.255.255.255 │
    # │ 0000000000000000000000000fffffff │                        ::15.255.255.255 │
    # │ 000000000000000000000000ffffffff │                       ::255.255.255.255 │
    # │ 00000000000000000000000fffffffff │                           ::f:ffff:ffff │
    # │ 0000000000000000000000ffffffffff │                          ::ff:ffff:ffff │
    # │ 000000000000000000000fffffffffff │                         ::fff:ffff:ffff │
    # │ 00000000000000000000ffffffffffff │                  ::ffff:255.255.255.255 │
    # │ 0000000000000000000fffffffffffff │                      ::f:ffff:ffff:ffff │
    # │ 000000000000000000ffffffffffffff │                     ::ff:ffff:ffff:ffff │
    # │ 00000000000000000fffffffffffffff │                    ::fff:ffff:ffff:ffff │
    # │ 0000000000000000ffffffffffffffff │                   ::ffff:ffff:ffff:ffff │
    # │ 000000000000000fffffffffffffffff │                 ::f:ffff:ffff:ffff:ffff │
    # │ 00000000000000ffffffffffffffffff │                ::ff:ffff:ffff:ffff:ffff │
    # │ 0000000000000fffffffffffffffffff │               ::fff:ffff:ffff:ffff:ffff │
    # │ 000000000000ffffffffffffffffffff │              ::ffff:ffff:ffff:ffff:ffff │
    # │ 00000000000fffffffffffffffffffff │            ::f:ffff:ffff:ffff:ffff:ffff │
    # │ 0000000000ffffffffffffffffffffff │           ::ff:ffff:ffff:ffff:ffff:ffff │
    # │ 000000000fffffffffffffffffffffff │          ::fff:ffff:ffff:ffff:ffff:ffff │
    # │ 00000000ffffffffffffffffffffffff │         ::ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 0000000fffffffffffffffffffffffff │       ::f:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 000000ffffffffffffffffffffffffff │      ::ff:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 00000fffffffffffffffffffffffffff │     ::fff:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 0000ffffffffffffffffffffffffffff │    ::ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 000fffffffffffffffffffffffffffff │    f:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 00ffffffffffffffffffffffffffffff │   ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ 0fffffffffffffffffffffffffffffff │  fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    # │ ffffffffffffffffffffffffffffffff │ ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff │
    # └──────────────────────────────────┴─────────────────────────────────────────┘
    #
    #            ::ffff ==          ::0.0.255.255 == 0000000000000000000000000000ffff
    #       ::ffff:ffff ==      ::255.255.255.255 == 000000000000000000000000ffffffff
    #  ::ffff:ffff:ffff == ::ffff:255.255.255.255 == 00000000000000000000ffffffffffff

    static function test_step_code__ip_to_hex(&$test, $dpath) {
        $data = [
                                            '0.0.0.0' => '00000000',
                                            '0.0.0.1' => '00000001',
                                          '127.0.0.0' => '7f000000',
                                          '127.0.0.1' => '7f000001',
                                    '255.255.255.255' => 'ffffffff',
                                                 '::' => '00000000000000000000000000000000',
                                                '::1' => '00000000000000000000000000000001',
                                             '::0001' => '00000000000000000000000000000001',
                     '::ffff' /* → ::0.0.255.255 → */ => '0000000000000000000000000000ffff',
                                      '::0.0.255.255' => '0000000000000000000000000000ffff',
            '::ffff:ffff' /* → ::255.255.255.255 → */ => '000000000000000000000000ffffffff',
                                  '::255.255.255.255' => '000000000000000000000000ffffffff',
            '::ffff:ffff:ffff' /* → ::ffff:255.255.255.255 → */ => '00000000000000000000ffffffffffff',
                             '::ffff:255.255.255.255' => '00000000000000000000ffffffffffff',
                              '::ffff:ffff:ffff:ffff' => '0000000000000000ffffffffffffffff',
                         '::ffff:ffff:ffff:ffff:ffff' => '000000000000ffffffffffffffffffff',
                    '::ffff:ffff:ffff:ffff:ffff:ffff' => '00000000ffffffffffffffffffffffff',
               '::ffff:ffff:ffff:ffff:ffff:ffff:ffff' => Core::is_Win() ? '' : '0000ffffffffffffffffffffffffffff',
            'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' => 'ffffffffffffffffffffffffffffffff',
                                         'ffff::ffff' => 'ffff000000000000000000000000ffff',
                                    'ffff::ffff:ffff' => 'ffff00000000000000000000ffffffff',
                                    'ffff:ffff::ffff' => 'ffffffff00000000000000000000ffff'
        ];

        foreach ($data as $c_value => $c_expected) {
            $с_received = Core::ip_to_hex($c_value, null, false);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__hex_to_ip(&$test, $dpath) {
        $data = [
                                    '00000000' => ['0.0.0.0'],
                                    '00000001' => ['0.0.0.1'],
                                    '7f000000' => ['127.0.0.0'],
                                    '7f000001' => ['127.0.0.1'],
                                    'ffffffff' => ['255.255.255.255'],
            '00000000000000000000000000000000' => ['::'],
            '0000000000000000000000000000000f' => ['::f', '::0.0.0.15'],
            '000000000000000000000000000000ff' => ['::ff', '::0.0.0.255'],
            '00000000000000000000000000000fff' => ['::fff', '::0.0.15.255'],
            '0000000000000000000000000000ffff' => ['::ffff', '::0.0.255.255'],
            '000000000000000000000000000fffff' => ['::f:ffff', '::0.15.255.255'],
            '00000000000000000000000000ffffff' => ['::ff:ffff', '::0.255.255.255'],
            '0000000000000000000000000fffffff' => ['::fff:ffff', '::15.255.255.255'],
            '000000000000000000000000ffffffff' => ['::ffff:ffff', '::255.255.255.255'],
            '00000000000000000000ffffffffffff' => ['::ffff:255.255.255.255'],
            '0000000000000000ffffffffffffffff' => ['::ffff:ffff:ffff:ffff'],
            '000000000000ffffffffffffffffffff' => ['::ffff:ffff:ffff:ffff:ffff'],
            '00000000ffffffffffffffffffffffff' => ['::ffff:ffff:ffff:ffff:ffff:ffff'],
            '0000ffffffffffffffffffffffffffff' => ['0:ffff:ffff:ffff:ffff:ffff:ffff:ffff', '::ffff:ffff:ffff:ffff:ffff:ffff:ffff'],
            '00000000000000000000000000000001' => ['::1'],
            'ffff000000000000000000000000ffff' => ['ffff::ffff'],
            'ffff00000000000000000000ffffffff' => ['ffff::ffff:ffff'],
            'ffffffff00000000000000000000ffff' => ['ffff:ffff::ffff'],
            'ffffffffffffffffffffffffffffffff' => ['ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'],
        ];

        foreach ($data as $c_value => $c_expected) {
            $с_received = Core::hex_to_ip($c_value);
            $c_result = Core::in_array($с_received, $c_expected);
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_value, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__html_entity_encode_total(&$test, $dpath) {
        $data = [
            '<',
            '>',
            'a',
            'z',
            'A',
            'Z',
            'а',
            'я',
            'А',
            'Я',
            '😀'
        ];

        $expected = [
            '&lt;',
            '&gt;',
            '&#97;',
            '&#122;',
            '&#65;',
            '&#90;',
            '&#1072;',
            '&#1103;',
            '&#1040;',
            '&#1071;',
            '&#128512;'
        ];

        foreach ($data as $c_key => $c_symbol) {
            $c_value = $c_symbol;
            $c_expected = $expected[$c_key];
            $с_received = Core::html_entity_encode_total($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_symbol, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_symbol, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__html_entity_decode_total(&$test, $dpath) {
        $data = [
            '&amp;',
            '&quot;',
            '&apos;',
            '&lt;',
            '&gt;',
            '&plus;',
            '&comma;',
            '&excl;',
            '&dollar;',
            '&lpar;',
            '&ncedil;',
            '&euro;',
            '&#97;',
            '&#122;',
            '&#65;',
            '&#90;',
            '&#1072;',
            '&#1103;',
            '&#1040;',
            '&#1071;',
            '&#128512;',
            '&#128512',
            '&#x1F600;',
            '&#x1F600',
            '&#x1f600;',
            '&#x1f600',
            '&#xfffff',
            '&#x00000000000000000000000000000000000fffff',
            '&#999999',
            '&#00000000000000000000000000000000000999999'
        ];

        $expected = [
            '&',
            '"',
            "'",
            '<',
            '>',
            '+',
            ',',
            '!',
            '$',
            '(',
            'ņ',
            '€',
            'a',
            'z',
            'A',
            'Z',
            'а',
            'я',
            'А',
            'Я',
            '😀',
            '😀',
            '😀',
            '😀',
            '😀',
            '😀',
            "\u{fffff}",
            "\u{fffff}",
            "\u{f423f}",
            "\u{f423f}"
        ];

        foreach ($data as $c_key => $c_symbol) {
            $c_value = $c_symbol;
            $c_expected = $expected[$c_key];
            $с_received = Core::html_entity_decode_total($c_value);
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_symbol, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_symbol, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

    static function test_step_code__generate_numerical_suffix(&$test, $dpath) {
        $data = [
            'x' => 'value x',
            'y' => 'value y',
        ];

        $expected = [
            'x'  => 'value x',
            'y'  => 'value y',
            'x2' => 'value x #2',
            'y2' => 'value y #2',
            'x3' => 'value x #3',
            'y3' => 'value y #3',
            'x4' => 'value x #4',
            'y4' => 'value y #4',
        ];

        $received = $data;

        $suffix = Core::generate_numerical_suffix('x', array_keys($received));  $received['x'.$suffix] = 'value x #2';
        $suffix = Core::generate_numerical_suffix('y', array_keys($received));  $received['y'.$suffix] = 'value y #2';
        $suffix = Core::generate_numerical_suffix('x', array_keys($received));  $received['x'.$suffix] = 'value x #3';
        $suffix = Core::generate_numerical_suffix('y', array_keys($received));  $received['y'.$suffix] = 'value y #3';
        $suffix = Core::generate_numerical_suffix('x', array_keys($received));  $received['x'.$suffix] = 'value x #4';
        $suffix = Core::generate_numerical_suffix('y', array_keys($received));  $received['y'.$suffix] = 'value y #4';

        $c_result = $received === $expected;
        if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'generate_numerical_suffix', 'result' => (new Text('success'))->render()]);
        if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'generate_numerical_suffix', 'result' => (new Text('failure'))->render()]);
        if ($c_result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

}
