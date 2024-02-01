<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Field_Hidden;
use effcore\Field;
use effcore\Request;
use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Request {

    static function test_step_code__name_get(&$test, $dpath, &$c_results) {
        $data = [
            'name_00' => '',
            'name_01' => 'name_01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => 'name_10[0]',
            'name_11' => 'name_11[0][1]',
            'name_12' => 'name_12[0][1][2]',
            'name_13' => 'name_13[0][1][2][3]',
            'name_14' => 'name_14[0][1][2][3][4]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => 'name_20[0]'.'[]',
            'name_21' => 'name_21[0][1]'.'[]',
            'name_22' => 'name_22[0][1][2]'.'[]',
            'name_23' => 'name_23[0][1][2][3]'.'[]',
            'name_24' => 'name_24[0][1][2][3][4]'.'[]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => 'name_30[1]',
            'name_31' => 'name_31[1][2]',
            'name_32' => 'name_32[1][2][3]',
            'name_33' => 'name_33[1][2][3][4]',
            'name_34' => 'name_34[1][2][3][4][5]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => 'name_40[1]'.'[]',
            'name_41' => 'name_41[1][2]'.'[]',
            'name_42' => 'name_42[1][2][3]'.'[]',
            'name_43' => 'name_43[1][2][3][4]'.'[]',
            'name_44' => 'name_44[1][2][3][4][5]'.'[]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => 'name_50[a]',
            'name_51' => 'name_51[a][b]',
            'name_52' => 'name_52[a][b][c]',
            'name_53' => 'name_53[a][b][c][d]',
            'name_54' => 'name_54[a][b][c][d][e]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => 'name_60[a]'.'[]',
            'name_61' => 'name_61[a][b]'.'[]',
            'name_62' => 'name_62[a][b][c]'.'[]',
            'name_63' => 'name_63[a][b][c][d]'.'[]',
            'name_64' => 'name_64[a][b][c][d][e]'.'[]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => 'name_70[]',
            'name_71' => 'name_71[][]',
            'name_72' => 'name_72[][][]',
            'name_73' => 'name_73[][][][]',
            'name_74' => 'name_74[][][][][]',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => 'name_80[][b][][1][]',
        ];

        $trim = true;

        foreach ($data as $c_row_id => $c_value) {
            $c_field = new Field;
            $c_field->build();
            $c_field->name_set($c_value);
            $c_expected = $c_value === '' ? '' : $c_row_id;
            $c_gotten = $c_field->name_get($trim);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $trim = false;

        foreach ($data as $c_row_id => $c_value) {
            $c_field = new Field;
            $c_field->build();
            $c_field->name_set($c_value);
            $c_expected = $c_value;
            $c_gotten = $c_field->name_get($trim);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $trim = true;

        foreach ($data as $c_row_id => $c_value) {
            $c_field = new Field_Hidden;
            $c_field->name_set($c_value);
            $c_expected = $c_value === '' ? '' : $c_row_id;
            $c_gotten = $c_field->name_get($trim);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field_Hidden->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field_Hidden->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $trim = false;

        foreach ($data as $c_row_id => $c_value) {
            $c_field = new Field_Hidden;
            $c_field->name_set($c_value);
            $c_expected = $c_value;
            $c_gotten = $c_field->name_get($trim);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field_Hidden->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Field_Hidden->name_get(trim = '.($trim ? 'true' : 'false').'): '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__value_get(&$test, $dpath, &$c_results) {
        global $_GET;
        $ORIGINAL = $_GET;

        $data = [
            /* name_00 = ''                  */  'name_00' => '',
            /* name_01 = 01                  */  'name_01' => '01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_10[0] = 10               */  'name_10' => [0 => '10'],
            /* name_11[0][1] = 11            */  'name_11' => [0 => [1 => '11']],
            /* name_12[0][1][2] = 12         */  'name_12' => [0 => [1 => [2 => '12']]],
            /* name_13[0][1][2][3] = 13      */  'name_13' => [0 => [1 => [2 => [3 => '13']]]],
            /* name_14[0][1][2][3][4] = 14   */  'name_14' => [0 => [1 => [2 => [3 => [4 => '14']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_20[0][] = 20             */  'name_20' => [0 => [0 => '20']],
            /* name_21[0][1][] = 21          */  'name_21' => [0 => [1 => [0 => '21']]],
            /* name_22[0][1][2][] = 22       */  'name_22' => [0 => [1 => [2 => [0 => '22']]]],
            /* name_23[0][1][2][3][] = 23    */  'name_23' => [0 => [1 => [2 => [3 => [0 => '23']]]]],
            /* name_24[0][1][2][3][4][] = 24 */  'name_24' => [0 => [1 => [2 => [3 => [4 => [0 => '24']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_30[1] = 30               */  'name_30' => [1 => '30'],
            /* name_31[1][2] = 31            */  'name_31' => [1 => [2 => '31']],
            /* name_32[1][2][3] = 32         */  'name_32' => [1 => [2 => [3 => '32']]],
            /* name_33[1][2][3][4] = 33      */  'name_33' => [1 => [2 => [3 => [4 => '33']]]],
            /* name_34[1][2][3][4][5] = 34   */  'name_34' => [1 => [2 => [3 => [4 => [5 => '34']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_40[1][] = 40             */  'name_40' => [1 => [0 => '40']],
            /* name_41[1][2][] = 41          */  'name_41' => [1 => [2 => [0 => '41']]],
            /* name_42[1][2][3][] = 42       */  'name_42' => [1 => [2 => [3 => [0 => '42']]]],
            /* name_43[1][2][3][4][] = 43    */  'name_43' => [1 => [2 => [3 => [4 => [0 => '43']]]]],
            /* name_44[1][2][3][4][5][] = 44 */  'name_44' => [1 => [2 => [3 => [4 => [5 => [0 => '44']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_50[a] = 50               */  'name_50' => ['a' => '50'],
            /* name_51[a][b] = 51            */  'name_51' => ['a' => ['b' => '51']],
            /* name_52[a][b][c] = 52         */  'name_52' => ['a' => ['b' => ['c' => '52']]],
            /* name_53[a][b][c][d] = 53      */  'name_53' => ['a' => ['b' => ['c' => ['d' => '53']]]],
            /* name_54[a][b][c][d][e] = 54   */  'name_54' => ['a' => ['b' => ['c' => ['d' => ['e' => '54']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_60[a][] = 60             */  'name_60' => ['a' => [0 => '60']],
            /* name_61[a][b][] = 61          */  'name_61' => ['a' => ['b' => [0 => '61']]],
            /* name_62[a][b][c][] = 62       */  'name_62' => ['a' => ['b' => ['c' => [0 => '62']]]],
            /* name_63[a][b][c][d][] = 63    */  'name_63' => ['a' => ['b' => ['c' => ['d' => [0 => '63']]]]],
            /* name_64[a][b][c][d][e][] = 64 */  'name_64' => ['a' => ['b' => ['c' => ['d' => ['e' => [0 => '64']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_70[] = 70                */  'name_70' => [0 => '70'],
            /* name_71[][] = 71              */  'name_71' => [0 => [0 => '71']],
            /* name_72[][][] = 72            */  'name_72' => [0 => [0 => [0 => '72']]],
            /* name_73[][][][] = 73          */  'name_73' => [0 => [0 => [0 => [0 => '73']]]],
            /* name_74[][][][][] = 74        */  'name_74' => [0 => [0 => [0 => [0 => [0 => '74']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_80[][b][][1][] = 80      */  'name_80' => [0 => ['b' => [0 => [1 => [0 => '80']]]]]
        ];

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $number = 0;
        $strict = true;

        $expected = [
            'name_00' => '',
            'name_01' => '01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => '10',
            'name_11' => '',
            'name_12' => '',
            'name_13' => '',
            'name_14' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => '',
            'name_21' => '',
            'name_22' => '',
            'name_23' => '',
            'name_24' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => '',
            'name_31' => '',
            'name_32' => '',
            'name_33' => '',
            'name_34' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => '',
            'name_41' => '',
            'name_42' => '',
            'name_43' => '',
            'name_44' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => '',
            'name_51' => '',
            'name_52' => '',
            'name_53' => '',
            'name_54' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => '',
            'name_61' => '',
            'name_62' => '',
            'name_63' => '',
            'name_64' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => '70',
            'name_71' => '',
            'name_72' => '',
            'name_73' => '',
            'name_74' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => ''
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, $number, '_GET', '', $strict);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $number = 0;
        $strict = false;

        $expected = [
            'name_00' => '',
            'name_01' => '01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => '10',
            'name_11' => [1 => '11'],
            'name_12' => [1 => [2 => '12']],
            'name_13' => [1 => [2 => [3 => '13']]],
            'name_14' => [1 => [2 => [3 => [4 => '14']]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => [0 => '20'],
            'name_21' => [1 => [0 => '21']],
            'name_22' => [1 => [2 => [0 => '22']]],
            'name_23' => [1 => [2 => [3 => [0 => '23']]]],
            'name_24' => [1 => [2 => [3 => [4 => [0 => '24']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => '',
            'name_31' => '',
            'name_32' => '',
            'name_33' => '',
            'name_34' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => '',
            'name_41' => '',
            'name_42' => '',
            'name_43' => '',
            'name_44' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => '',
            'name_51' => '',
            'name_52' => '',
            'name_53' => '',
            'name_54' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => '',
            'name_61' => '',
            'name_62' => '',
            'name_63' => '',
            'name_64' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => '70',
            'name_71' => [0 => '71'],
            'name_72' => [0 => [0 => '72']],
            'name_73' => [0 => [0 => [0 => '73']]],
            'name_74' => [0 => [0 => [0 => [0 => '74']]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => ['b' => [0 => [1 => [0 => '80']]]]
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, $number, '_GET', '', $strict);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $number = 1;
        $strict = true;

        $expected = [
            'name_00' => '',
            'name_01' => '01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => '',
            'name_11' => '',
            'name_12' => '',
            'name_13' => '',
            'name_14' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => '',
            'name_21' => '',
            'name_22' => '',
            'name_23' => '',
            'name_24' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => '30',
            'name_31' => '',
            'name_32' => '',
            'name_33' => '',
            'name_34' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => '',
            'name_41' => '',
            'name_42' => '',
            'name_43' => '',
            'name_44' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => '',
            'name_51' => '',
            'name_52' => '',
            'name_53' => '',
            'name_54' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => '',
            'name_61' => '',
            'name_62' => '',
            'name_63' => '',
            'name_64' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => '',
            'name_71' => '',
            'name_72' => '',
            'name_73' => '',
            'name_74' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => ''
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, $number, '_GET', '', $strict);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $number = 1;
        $strict = false;

        $expected = [
            'name_00' => '',
            'name_01' => '01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => '',
            'name_11' => '',
            'name_12' => '',
            'name_13' => '',
            'name_14' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => '',
            'name_21' => '',
            'name_22' => '',
            'name_23' => '',
            'name_24' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => '30',
            'name_31' => [2 => '31'],
            'name_32' => [2 => [3 => '32']],
            'name_33' => [2 => [3 => [4 => '33']]],
            'name_34' => [2 => [3 => [4 => [5 => '34']]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => [0 => '40'],
            'name_41' => [2 => [0 => '41']],
            'name_42' => [2 => [3 => [0 => '42']]],
            'name_43' => [2 => [3 => [4 => [0 => '43']]]],
            'name_44' => [2 => [3 => [4 => [5 => [0 => '44']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => '',
            'name_51' => '',
            'name_52' => '',
            'name_53' => '',
            'name_54' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => '',
            'name_61' => '',
            'name_62' => '',
            'name_63' => '',
            'name_64' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => '',
            'name_71' => '',
            'name_72' => '',
            'name_73' => '',
            'name_74' => '',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => ''
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, $number, '_GET', '', $strict);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        ###############################
        ### transpositions in array ###
        ###############################

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
            'transposition_5' => [0 => '' , 1 => '' ],
            'transposition_6' => [0 => 'X', 1 => '' ],
            'transposition_7' => [          1 => 'Y'],
            'transposition_8' => [0 => '' , 1 => 'Y'],
            'transposition_9' => [0 => 'X', 1 => 'Y']
        ];

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $number = 0;

        $expected = [
            'transposition_1' => '',
            'transposition_2' => '',
            'transposition_3' => 'X',
            'transposition_4' => '',
            'transposition_5' => '',
            'transposition_6' => 'X',
            'transposition_7' => '',
            'transposition_8' => '',
            'transposition_9' => 'X'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, $number, '_GET');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $number = 1;

        $expected = [
            'transposition_1' => '',
            'transposition_2' => '',
            'transposition_3' => '',
            'transposition_4' => '',
            'transposition_5' => '',
            'transposition_6' => '',
            'transposition_7' => 'Y',
            'transposition_8' => 'Y',
            'transposition_9' => 'Y'
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::value_get($c_row_id, $number, '_GET');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::value_get('.$c_row_id.', number = '.$number.')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        $_GET = $ORIGINAL;
    }

    static function test_step_code__values_get(&$test, $dpath, &$c_results) {
        global $_GET;
        $ORIGINAL = $_GET;

        $data = [
            /* name_00 = ''                  */  'name_00' => '',
            /* name_01 = 01                  */  'name_01' => '01',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_10[0] = 10               */  'name_10' => [0 => '10'],
            /* name_11[0][1] = 11            */  'name_11' => [0 => [1 => '11']],
            /* name_12[0][1][2] = 12         */  'name_12' => [0 => [1 => [2 => '12']]],
            /* name_13[0][1][2][3] = 13      */  'name_13' => [0 => [1 => [2 => [3 => '13']]]],
            /* name_14[0][1][2][3][4] = 14   */  'name_14' => [0 => [1 => [2 => [3 => [4 => '14']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_20[0][] = 20             */  'name_20' => [0 => [0 => '20']],
            /* name_21[0][1][] = 21          */  'name_21' => [0 => [1 => [0 => '21']]],
            /* name_22[0][1][2][] = 22       */  'name_22' => [0 => [1 => [2 => [0 => '22']]]],
            /* name_23[0][1][2][3][] = 23    */  'name_23' => [0 => [1 => [2 => [3 => [0 => '23']]]]],
            /* name_24[0][1][2][3][4][] = 24 */  'name_24' => [0 => [1 => [2 => [3 => [4 => [0 => '24']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_30[1] = 30               */  'name_30' => [1 => '30'],
            /* name_31[1][2] = 31            */  'name_31' => [1 => [2 => '31']],
            /* name_32[1][2][3] = 32         */  'name_32' => [1 => [2 => [3 => '32']]],
            /* name_33[1][2][3][4] = 33      */  'name_33' => [1 => [2 => [3 => [4 => '33']]]],
            /* name_34[1][2][3][4][5] = 34   */  'name_34' => [1 => [2 => [3 => [4 => [5 => '34']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_40[1][] = 40             */  'name_40' => [1 => [0 => '40']],
            /* name_41[1][2][] = 41          */  'name_41' => [1 => [2 => [0 => '41']]],
            /* name_42[1][2][3][] = 42       */  'name_42' => [1 => [2 => [3 => [0 => '42']]]],
            /* name_43[1][2][3][4][] = 43    */  'name_43' => [1 => [2 => [3 => [4 => [0 => '43']]]]],
            /* name_44[1][2][3][4][5][] = 44 */  'name_44' => [1 => [2 => [3 => [4 => [5 => [0 => '44']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_50[a] = 50               */  'name_50' => ['a' => '50'],
            /* name_51[a][b] = 51            */  'name_51' => ['a' => ['b' => '51']],
            /* name_52[a][b][c] = 52         */  'name_52' => ['a' => ['b' => ['c' => '52']]],
            /* name_53[a][b][c][d] = 53      */  'name_53' => ['a' => ['b' => ['c' => ['d' => '53']]]],
            /* name_54[a][b][c][d][e] = 54   */  'name_54' => ['a' => ['b' => ['c' => ['d' => ['e' => '54']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_60[a][] = 60             */  'name_60' => ['a' => [0 => '60']],
            /* name_61[a][b][] = 61          */  'name_61' => ['a' => ['b' => [0 => '61']]],
            /* name_62[a][b][c][] = 62       */  'name_62' => ['a' => ['b' => ['c' => [0 => '62']]]],
            /* name_63[a][b][c][d][] = 63    */  'name_63' => ['a' => ['b' => ['c' => ['d' => [0 => '63']]]]],
            /* name_64[a][b][c][d][e][] = 64 */  'name_64' => ['a' => ['b' => ['c' => ['d' => ['e' => [0 => '64']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_70[] = 70                */  'name_70' => [0 => '70'],
            /* name_71[][] = 71              */  'name_71' => [0 => [0 => '71']],
            /* name_72[][][] = 72            */  'name_72' => [0 => [0 => [0 => '72']]],
            /* name_73[][][][] = 73          */  'name_73' => [0 => [0 => [0 => [0 => '73']]]],
            /* name_74[][][][][] = 74        */  'name_74' => [0 => [0 => [0 => [0 => [0 => '74']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            /* name_80[][b][][1][] = 80      */  'name_80' => [0 => ['b' => [0 => [1 => [0 => '80']]]]]
        ];

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $strict = true;

        $expected = [
            'name_00' => [0 => ''],
            'name_01' => [0 => '01'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => [0 => '10'],
            'name_11' => [],
            'name_12' => [],
            'name_13' => [],
            'name_14' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => [],
            'name_21' => [],
            'name_22' => [],
            'name_23' => [],
            'name_24' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => [1 => '30'],
            'name_31' => [],
            'name_32' => [],
            'name_33' => [],
            'name_34' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => [],
            'name_41' => [],
            'name_42' => [],
            'name_43' => [],
            'name_44' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => ['a' => '50'],
            'name_51' => [],
            'name_52' => [],
            'name_53' => [],
            'name_54' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => [],
            'name_61' => [],
            'name_62' => [],
            'name_63' => [],
            'name_64' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => [0 => '70'],
            'name_71' => [],
            'name_72' => [],
            'name_73' => [],
            'name_74' => [],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => []
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::values_get($c_row_id, '_GET', [], $strict);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::values_get('.$c_row_id.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::values_get('.$c_row_id.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $strict = false;

        $expected = [
            'name_00' => [0 => ''],
            'name_01' => [0 => '01'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_10' => [0 => '10'],
            'name_11' => [0 => [1 => '11']],
            'name_12' => [0 => [1 => [2 => '12']]],
            'name_13' => [0 => [1 => [2 => [3 => '13']]]],
            'name_14' => [0 => [1 => [2 => [3 => [4 => '14']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_20' => [0 => [0 => '20']],
            'name_21' => [0 => [1 => [0 => '21']]],
            'name_22' => [0 => [1 => [2 => [0 => '22']]]],
            'name_23' => [0 => [1 => [2 => [3 => [0 => '23']]]]],
            'name_24' => [0 => [1 => [2 => [3 => [4 => [0 => '24']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_30' => [1 => '30'],
            'name_31' => [1 => [2 => '31']],
            'name_32' => [1 => [2 => [3 => '32']]],
            'name_33' => [1 => [2 => [3 => [4 => '33']]]],
            'name_34' => [1 => [2 => [3 => [4 => [5 => '34']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_40' => [1 => [0 => '40']],
            'name_41' => [1 => [2 => [0 => '41']]],
            'name_42' => [1 => [2 => [3 => [0 => '42']]]],
            'name_43' => [1 => [2 => [3 => [4 => [0 => '43']]]]],
            'name_44' => [1 => [2 => [3 => [4 => [5 => [0 => '44']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_50' => ['a' => '50'],
            'name_51' => ['a' => ['b' => '51']],
            'name_52' => ['a' => ['b' => ['c' => '52']]],
            'name_53' => ['a' => ['b' => ['c' => ['d' => '53']]]],
            'name_54' => ['a' => ['b' => ['c' => ['d' => ['e' => '54']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_60' => ['a' => [0 => '60']],
            'name_61' => ['a' => ['b' => [0 => '61']]],
            'name_62' => ['a' => ['b' => ['c' => [0 => '62']]]],
            'name_63' => ['a' => ['b' => ['c' => ['d' => [0 => '63']]]]],
            'name_64' => ['a' => ['b' => ['c' => ['d' => ['e' => [0 => '64']]]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_70' => [0 => '70'],
            'name_71' => [0 => [0 => '71']],
            'name_72' => [0 => [0 => [0 => '72']]],
            'name_73' => [0 => [0 => [0 => [0 => '73']]]],
            'name_74' => [0 => [0 => [0 => [0 => [0 => '74']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'name_80' => [0 => ['b' => [0 => [1 => [0 => '80']]]]]
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::values_get($c_row_id, '_GET', [], $strict);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::values_get('.$c_row_id.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::values_get('.$c_row_id.', strict = '.($strict ? 'true' : 'false').')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        ###############################
        ### transpositions in array ###
        ###############################

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
            'transposition_5' => [0 => '' , 1 => '' ],
            'transposition_6' => [0 => 'X', 1 => '' ],
            'transposition_7' => [          1 => 'Y'],
            'transposition_8' => [0 => '' , 1 => 'Y'],
            'transposition_9' => [0 => 'X', 1 => 'Y']
        ];

        $expected = [
            'transposition_1' => [                  ],
            'transposition_2' => [0 => ''           ],
            'transposition_3' => [0 => 'X'          ],
            'transposition_4' => [          1 => '' ],
            'transposition_5' => [0 => '' , 1 => '' ],
            'transposition_6' => [0 => 'X', 1 => '' ],
            'transposition_7' => [          1 => 'Y'],
            'transposition_8' => [0 => '' , 1 => 'Y'],
            'transposition_9' => [0 => 'X', 1 => 'Y']
        ];

        foreach ($data as $c_row_id => $c_value) {
            $_GET = [$c_row_id => $c_value];
            $c_expected = $expected[$c_row_id];
            $c_gotten = Request::values_get($c_row_id, '_GET');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::values_get('.$c_row_id.')', 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'Request::values_get('.$c_row_id.')', 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        $_GET = $ORIGINAL;
    }

    ############################################
    ### $_GET: Request::sanitize_structure() ###
    ############################################

    static function test_step_code__sanitize_structure(&$test, $dpath, &$c_results) {
        global $_POST;
        $ORIGINAL = $_POST;

        $data = [
            'value_null'         => null,
            'value_bool_true'    => true,
            'value_bool_false'   => false,
            'value_int_0'        => 0,
            'value_int_1'        => 1,
            'value_float_0_0'    => 0.0,
            'value_float_1_0'    => 1.0,
            'value_string_empty' => '',
            'value_string_0'     => '0',
            'value_string_1'     => '1',
            'value_string_X'     => 'X',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_empty'        => [],
            'value_array_null'         => [null],
            'value_array_bool_true'    => [true],
            'value_array_bool_false'   => [false],
            'value_array_int_0'        => [0],
            'value_array_int_1'        => [1],
            'value_array_float_0_0'    => [0.0],
            'value_array_float_1_0'    => [1.0],
            'value_array_string_empty' => [''],
            'value_array_string_0'     => ['0'],
            'value_array_string_1'     => ['1'],
            'value_array_string_X'     => ['X'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_array_empty'        => [[]],
            'value_array_array_null'         => [[null]],
            'value_array_array_bool_true'    => [[true]],
            'value_array_array_bool_false'   => [[false]],
            'value_array_array_int_0'        => [[0]],
            'value_array_array_int_1'        => [[1]],
            'value_array_array_float_0_0'    => [[0.0]],
            'value_array_array_float_1_0'    => [[1.0]],
            'value_array_array_string_empty' => [['']],
            'value_array_array_string_0'     => [['0']],
            'value_array_array_string_1'     => [['1']],
            'value_array_array_string_X'     => [['X']],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_array_array_empty'        => [[[]]],
            'value_array_array_array_null'         => [[[null]]],
            'value_array_array_array_bool_true'    => [[[true]]],
            'value_array_array_array_bool_false'   => [[[false]]],
            'value_array_array_array_int_0'        => [[[0]]],
            'value_array_array_array_int_1'        => [[[1]]],
            'value_array_array_array_float_0_0'    => [[[0.0]]],
            'value_array_array_array_float_1_0'    => [[[1.0]]],
            'value_array_array_array_string_empty' => [[['']]],
            'value_array_array_array_string_0'     => [[['0']]],
            'value_array_array_array_string_1'     => [[['1']]],
            'value_array_array_array_string_X'     => [[['X']]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_array_array_array_empty'        => [[[[]]]],
            'value_array_array_array_array_null'         => [[[[null]]]],
            'value_array_array_array_array_bool_true'    => [[[[true]]]],
            'value_array_array_array_array_bool_false'   => [[[[false]]]],
            'value_array_array_array_array_int_0'        => [[[[0]]]],
            'value_array_array_array_array_int_1'        => [[[[1]]]],
            'value_array_array_array_array_float_0_0'    => [[[[0.0]]]],
            'value_array_array_array_array_float_1_0'    => [[[[1.0]]]],
            'value_array_array_array_array_string_empty' => [[[['']]]],
            'value_array_array_array_array_string_0'     => [[[['0']]]],
            'value_array_array_array_array_string_1'     => [[[['1']]]],
            'value_array_array_array_array_string_X'     => [[[['X']]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_array_array_array_array_empty'        => [[[[[]]]]],
            'value_array_array_array_array_array_null'         => [[[[[null]]]]],
            'value_array_array_array_array_array_bool_true'    => [[[[[true]]]]],
            'value_array_array_array_array_array_bool_false'   => [[[[[false]]]]],
            'value_array_array_array_array_array_int_0'        => [[[[[0]]]]],
            'value_array_array_array_array_array_int_1'        => [[[[[1]]]]],
            'value_array_array_array_array_array_float_0_0'    => [[[[[0.0]]]]],
            'value_array_array_array_array_array_float_1_0'    => [[[[[1.0]]]]],
            'value_array_array_array_array_array_string_empty' => [[[[['']]]]],
            'value_array_array_array_array_array_string_0'     => [[[[['0']]]]],
            'value_array_array_array_array_array_string_1'     => [[[[['1']]]]],
            'value_array_array_array_array_array_string_X'     => [[[[['X']]]]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_inconsistent_keys' => [
                100          => 'option_1',
                200          => 'option_2',
                'string_key' => 'option_3'
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
            'value_string_0'      => '0',
            'value_string_1'      => '1',
            'value_string_X'      => 'X',
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_string_empty' => [''],
            'value_array_string_0'     => ['0'],
            'value_array_string_1'     => ['1'],
            'value_array_string_X'     => ['X'],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_array_string_empty' => [['']],
            'value_array_array_string_0'     => [['0']],
            'value_array_array_string_1'     => [['1']],
            'value_array_array_string_X'     => [['X']],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_array_array_string_empty' => [[['']]],
            'value_array_array_array_string_0'     => [[['0']]],
            'value_array_array_array_string_1'     => [[['1']]],
            'value_array_array_array_string_X'     => [[['X']]],
            # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
            'value_array_inconsistent_keys' => [
                100          => 'option_1',
                200          => 'option_2',
                'string_key' => 'option_3'
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
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        $_POST = $ORIGINAL;
    }

    ##########################################################################
    ### $_FILES: Request::sanitize_structure_FILES(), Request::files_get() ###
    ##########################################################################

    static function test_step_code__sanitize_structure_files(&$test, $dpath, &$c_results) {
        global $_FILES;
        $_FILES_ORIGINAL = $_FILES;

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
            $c_gotten = Request::sanitize_structure_FILES();
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

        $_FILES = $_FILES_ORIGINAL;
    }

    static function test_step_code__files_get(&$test, $dpath, &$c_results) {
        global $_FILES;
        $_FILES_ORIGINAL = $_FILES;

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
                'name'     => [0 => 'file1.png'       , 1 => 'file2.png'],
                'type'     => [0 => 'image/png'       , 1 => 'image/png'],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx', 1 => '/tmp/phpyyyyyyyy'],
                'error'    => [0 => 0                 , 1 => 0],
                'size'     => [0 => 1000              , 1 => 1000]
            ],
            # input[name="file[]",value="",multiple="multiple"]
            # input[name="file[]",value="",multiple="multiple"]
            'field_array__no_file1_multiple__no_file2_multiple' => [
                'name'     => [0 => ''                , 1 => ''],
                'type'     => [0 => ''                , 1 => ''],
                'tmp_name' => [0 => ''                , 1 => ''],
                'error'    => [0 => UPLOAD_ERR_NO_FILE, 1 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 0                 , 1 => 0]
            ],
            # input[name="file[]",value="file1.png",multiple="multiple"]
            # input[name="file[]",value="",         multiple="multiple"]
            'field_array__file1_multiple__no_file2_multiple' => [
                'name'     => [0 => 'file1.png'       , 1 => ''],
                'type'     => [0 => 'image/png'       , 1 => ''],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx', 1 => ''],
                'error'    => [0 => 0                 , 1 => UPLOAD_ERR_NO_FILE],
                'size'     => [0 => 1000              , 1 => 0]
            ],
            # input[name="file[]",value="",         multiple="multiple"]
            # input[name="file[]",value="file2.png",multiple="multiple"]
            'field_array__no_file_1_multiple__file2_multiple' => [
                'name'     => [0 => ''                , 1 => 'file2.png'],
                'type'     => [0 => ''                , 1 => 'image/png'],
                'tmp_name' => [0 => ''                , 1 => '/tmp/phpyyyyyyyy'],
                'error'    => [0 => UPLOAD_ERR_NO_FILE, 1 => 0],
                'size'     => [0 => 0                 , 1 => 1000]
            ],
            # input[name="file[]",value="file1.png",multiple="multiple"]
            # input[name="file[]",value="file2.png",multiple="multiple"]
            'field_array__file1_multiple__file2_multiple' => [
                'name'     => [0 => 'file1.png'       , 1 => 'file2.png'],
                'type'     => [0 => 'image/png'       , 1 => 'image/png'],
                'tmp_name' => [0 => '/tmp/phpxxxxxxxx', 1 => '/tmp/phpyyyyyyyy'],
                'error'    => [0 => 0                 , 1 => 0],
                'size'     => [0 => 1000              , 1 => 1000]
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
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        $_FILES = $_FILES_ORIGINAL;
    }

    ######################################
    ### Request::web_server_get_info() ###
    ######################################

    static function test_step_code__web_server_get_info(&$test, $dpath, &$c_results) {
        $data = [
            'nginx'       => (array)Request::web_server_get_info('nginx/1.1X.X'),
            'apache_nix'  => (array)Request::web_server_get_info('Apache/2.4.XX (Unix) LibreSSL/2.2.X PHP/5.6.XX'),
            'apache_win'  => (array)Request::web_server_get_info('Apache/2.4.XX (Win32) OpenSSL/1.X.X PHP/5.6.XX'),
            'iis'         => (array)Request::web_server_get_info('Microsoft-IIS/7.5'),
            'lighttpd'    => (array)Request::web_server_get_info('lighttpd/1.X.XX'),
            'unknown_nix' => (array)Request::web_server_get_info('Unknown/1.0.XX (Linux)'),
            'unknown'     => (array)Request::web_server_get_info('Unknown server v-1-0'),
            'unknown_utf' => (array)Request::web_server_get_info('Сервер v-1-0'),
        ];

        $expected = [
            'nginx'       => ['name' => 'nginx'               , 'version' => '1.1X.X'],
            'apache_nix'  => ['name' => 'apache'              , 'version' => '2.4.XX'],
            'apache_win'  => ['name' => 'apache'              , 'version' => '2.4.XX'],
            'iis'         => ['name' => 'microsoft-iis'       , 'version' => '7.5'],
            'lighttpd'    => ['name' => 'lighttpd'            , 'version' => '1.X.XX'],
            'unknown_nix' => ['name' => 'unknown'             , 'version' => '1.0.XX'],
            'unknown'     => ['name' => 'unknown server v-1-0', 'version' => ''],
            'unknown_utf' => ['name' => 'сервер v-1-0'        , 'version' => ''],
        ];

        foreach ($data as $c_row_id => $c_field) {
            $c_expected = $expected[$c_row_id];
            $c_gotten = $data[$c_row_id];
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
