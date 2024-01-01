<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\NL;
use effcore\Test;
use effcore\Text;
use effcore\Translation;

abstract class Events_Test__Class_Translation {

    static function test_step_code__apply(&$test, $dpath, &$c_results) {
        $data = [
            'ok'             => ['gotten' => Translation::apply('%%_number sec.',                    ['number' => 1], 'ru'), 'expected' => '1 сек.'                              ],
            'error'          => ['gotten' => Translation::apply('%%_number sec.',                    [             ], 'ru'), 'expected' => '%%_number сек.'                      ],
            'plural-ok'      => ['gotten' => Translation::apply('%%_number file%%_plural(number|s)', ['number' => 1], 'ru'), 'expected' => '1 файл'                              ],
            'plural-error-1' => ['gotten' => Translation::apply('%%_number file%%_plural(number)',   ['number' => 1], 'ru'), 'expected' => '1 file%%_plural(number)'             ],
            'plural-error-2' => ['gotten' => Translation::apply('%%_number file%%_plural',           ['number' => 1], 'ru'), 'expected' => '1 file%%_plural'                     ],
            'plural-error-3' => ['gotten' => Translation::apply('%%_number file%%_plural(number|s)', [             ], 'ru'), 'expected' => '%%_number файл%%_plural(number|ov-a)'],
            'plural-error-4' => ['gotten' => Translation::apply('%%_number file%%_plural(number)',   [             ], 'ru'), 'expected' => '%%_number file%%_plural(number)'     ],
            'plural-error-5' => ['gotten' => Translation::apply('%%_number file%%_plural',           [             ], 'ru'), 'expected' => '%%_number file%%_plural'             ],
        ];

        foreach ($data as $c_row_id => $c_info) {
            $c_expected = $c_info['expected'];
            $c_gotten = $c_info['gotten'];
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

    static function test_step_code__apply__pieces(&$test, $dpath, &$c_results) {

        # ──────────────────────────────────────────────────────────
        # nominative, middle:
        # ┌──────────┬─────────┬───────────┬────────────┬──────────┐
        # │ 0 штук   │ 10 штук │ 20 штук   │ 100 штук   │ 110 штук │
        # │ 1 штук а │ 11 штук │ 21 штук а │ 101 штук а │ 111 штук │
        # │ 2 штук и │ 12 штук │ 22 штук и │ 102 штук и │ 112 штук │
        # │ 3 штук и │ 13 штук │ 23 штук и │ 103 штук и │ 113 штук │
        # │ 4 штук и │ 14 штук │ 24 штук и │ 104 штук и │ 114 штук │
        # │ 5 штук   │ 15 штук │ 25 штук   │ 105 штук   │ 115 штук │
        # │ 6 штук   │ 16 штук │ 26 штук   │ 106 штук   │ 116 штук │
        # │ 7 штук   │ 17 штук │ 27 штук   │ 107 штук   │ 117 штук │
        # │ 8 штук   │ 18 штук │ 28 штук   │ 108 штук   │ 118 штук │
        # │ 9 штук   │ 19 штук │ 29 штук   │ 109 штук   │ 119 штук │
        # └──────────┴─────────┴───────────┴────────────┴──────────┘

        # │   1 штук а
        # │  11 штук
        # │  21 штук а
        # │  31 штук а
        # │  41 штук а
        # --------------
        # │ 101 штук а
        # │ 111 штук
        # │ 121 штук а
        # │ 131 штук а
        # │ 141 штук а

        # │   2 штук и
        # │   3 штук и
        # │   4 штук и
        # | ------------
        # │  12 штук
        # │  13 штук
        # │  14 штук
        # │  22 штук и
        # │  23 штук и
        # │  24 штук и
        # │  32 штук и
        # │  33 штук и
        # │  34 штук и
        # | ------------
        # │ 102 штук и
        # │ 103 штук и
        # │ 104 штук и
        # │ 112 штук
        # │ 113 штук
        # │ 114 штук
        # │ 122 штук и
        # │ 123 штук и
        # │ 124 штук и
        # │ 132 штук и
        # │ 133 штук и
        # │ 134 штук и

        $base_word = 'штук';

        $expected = [
              0 => $base_word,
              1 => $base_word.'а',
              2 => $base_word.'и',
              3 => $base_word.'и',
              4 => $base_word.'и',
              5 => $base_word,
              6 => $base_word,
              7 => $base_word,
              8 => $base_word,
              9 => $base_word,
             10 => $base_word,
             11 => $base_word,
             12 => $base_word,
             13 => $base_word,
             14 => $base_word,
             15 => $base_word,
             16 => $base_word,
             17 => $base_word,
             18 => $base_word,
             19 => $base_word,
             20 => $base_word,
             21 => $base_word.'а',
             22 => $base_word.'и',
             23 => $base_word.'и',
             24 => $base_word.'и',
             25 => $base_word,
             26 => $base_word,
             27 => $base_word,
             28 => $base_word,
             29 => $base_word,
            100 => $base_word,
            101 => $base_word.'а',
            102 => $base_word.'и',
            103 => $base_word.'и',
            104 => $base_word.'и',
            105 => $base_word,
            106 => $base_word,
            107 => $base_word,
            108 => $base_word,
            109 => $base_word,
            110 => $base_word,
            111 => $base_word,
            112 => $base_word,
            113 => $base_word,
            114 => $base_word,
            115 => $base_word,
            116 => $base_word,
            117 => $base_word,
            118 => $base_word,
            119 => $base_word
        ];

        $formula = '%^(?<variant_1>1|.*[^1]1)$|'.
                    '^(?<variant_2>[234]|.*[^1][234])$%S';

        $matches = [
            'variant_1' => 'а',
            'variant_2' => 'и'
        ];

        foreach ($expected as $c_num => $c_expected) {
            $c_gotten = $base_word;
            $c_preg_result = [];
            preg_match($formula, (string)$c_num, $c_preg_result);
            if (isset($c_preg_result['variant_1']) && strlen($c_preg_result['variant_1'])) $c_gotten.= $matches['variant_1'];
            if (isset($c_preg_result['variant_2']) && strlen($c_preg_result['variant_2'])) $c_gotten.= $matches['variant_2'];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_num.' '.$c_gotten, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_num.' '.$c_gotten, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        foreach ($expected as $c_num => $c_expected) {
            $c_gotten = Translation::apply('%%_number piece%%_plural(number|s)', ['number' => $c_num], 'ru');
            $c_expected = $c_num.' '.$c_expected;
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__apply__seconds(&$test, $dpath, &$c_results) {

        # ─────────────────────────────────────────────────────────────────────
        # nominative, feminine:
        # ┌────────────┬───────────┬─────────────┬──────────────┬────────────┐
        # │ 0 секунд   │ 10 секунд │ 20 секунд   │ 100 секунд   │ 110 секунд │
        # │ 1 секунд а │ 11 секунд │ 21 секунд а │ 101 секунд а │ 111 секунд │
        # │ 2 секунд ы │ 12 секунд │ 22 секунд ы │ 102 секунд ы │ 112 секунд │
        # │ 3 секунд ы │ 13 секунд │ 23 секунд ы │ 103 секунд ы │ 113 секунд │
        # │ 4 секунд ы │ 14 секунд │ 24 секунд ы │ 104 секунд ы │ 114 секунд │
        # │ 5 секунд   │ 15 секунд │ 25 секунд   │ 105 секунд   │ 115 секунд │
        # │ 6 секунд   │ 16 секунд │ 26 секунд   │ 106 секунд   │ 116 секунд │
        # │ 7 секунд   │ 17 секунд │ 27 секунд   │ 107 секунд   │ 117 секунд │
        # │ 8 секунд   │ 18 секунд │ 28 секунд   │ 108 секунд   │ 118 секунд │
        # │ 9 секунд   │ 19 секунд │ 29 секунд   │ 109 секунд   │ 119 секунд │
        # └────────────┴───────────┴─────────────┴──────────────┴────────────┘

        # │   1 секунд а
        # │  11 секунд
        # │  21 секунд а
        # │  31 секунд а
        # │  41 секунд а
        # --------------
        # │ 101 секунд а
        # │ 111 секунд
        # │ 121 секунд а
        # │ 131 секунд а
        # │ 141 секунд а

        # │   2 секунд ы
        # │   3 секунд ы
        # │   4 секунд ы
        # | ------------
        # │  12 секунд
        # │  13 секунд
        # │  14 секунд
        # │  22 секунд ы
        # │  23 секунд ы
        # │  24 секунд ы
        # │  32 секунд ы
        # │  33 секунд ы
        # │  34 секунд ы
        # | ------------
        # │ 102 секунд ы
        # │ 103 секунд ы
        # │ 104 секунд ы
        # │ 112 секунд
        # │ 113 секунд
        # │ 114 секунд
        # │ 122 секунд ы
        # │ 123 секунд ы
        # │ 124 секунд ы
        # │ 132 секунд ы
        # │ 133 секунд ы
        # │ 134 секунд ы

        $base_word = 'секунд';

        $expected = [
              0 => $base_word,
              1 => $base_word.'а',
              2 => $base_word.'ы',
              3 => $base_word.'ы',
              4 => $base_word.'ы',
              5 => $base_word,
              6 => $base_word,
              7 => $base_word,
              8 => $base_word,
              9 => $base_word,
             10 => $base_word,
             11 => $base_word,
             12 => $base_word,
             13 => $base_word,
             14 => $base_word,
             15 => $base_word,
             16 => $base_word,
             17 => $base_word,
             18 => $base_word,
             19 => $base_word,
             20 => $base_word,
             21 => $base_word.'а',
             22 => $base_word.'ы',
             23 => $base_word.'ы',
             24 => $base_word.'ы',
             25 => $base_word,
             26 => $base_word,
             27 => $base_word,
             28 => $base_word,
             29 => $base_word,
            100 => $base_word,
            101 => $base_word.'а',
            102 => $base_word.'ы',
            103 => $base_word.'ы',
            104 => $base_word.'ы',
            105 => $base_word,
            106 => $base_word,
            107 => $base_word,
            108 => $base_word,
            109 => $base_word,
            110 => $base_word,
            111 => $base_word,
            112 => $base_word,
            113 => $base_word,
            114 => $base_word,
            115 => $base_word,
            116 => $base_word,
            117 => $base_word,
            118 => $base_word,
            119 => $base_word
        ];

        $formula = '%^(?<variant_1>1|.*[^1]1)$|'.
                    '^(?<variant_2>[234]|.*[^1][234])$%S';

        $matches = [
            'variant_1' => 'а',
            'variant_2' => 'ы'
        ];

        foreach ($expected as $c_num => $c_expected) {
            $c_gotten = $base_word;
            $c_preg_result = [];
            preg_match($formula, (string)$c_num, $c_preg_result);
            if (isset($c_preg_result['variant_1']) && strlen($c_preg_result['variant_1'])) $c_gotten.= $matches['variant_1'];
            if (isset($c_preg_result['variant_2']) && strlen($c_preg_result['variant_2'])) $c_gotten.= $matches['variant_2'];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_num.' '.$c_gotten, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_num.' '.$c_gotten, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        foreach ($expected as $c_num => $c_expected) {
            $c_gotten = Translation::apply('%%_number second%%_plural(number|s)', ['number' => $c_num], 'ru');
            $c_expected = $c_num.' '.$c_expected;
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__apply__files(&$test, $dpath, &$c_results) {

        # ─────────────────────────────────────────────────────────────────────
        # nominative, masculine:
        # ┌───────────┬────────────┬────────────┬─────────────┬─────────────┐
        # │ 0 файл ов │ 10 файл ов │ 20 файл ов │ 100 файл ов │ 110 файл ов │
        # │ 1 файл    │ 11 файл ов │ 21 файл    │ 101 файл    │ 111 файл ов │
        # │ 2 файл а  │ 12 файл ов │ 22 файл а  │ 102 файл а  │ 112 файл ов │
        # │ 3 файл а  │ 13 файл ов │ 23 файл а  │ 103 файл а  │ 113 файл ов │
        # │ 4 файл а  │ 14 файл ов │ 24 файл а  │ 104 файл а  │ 114 файл ов │
        # │ 5 файл ов │ 15 файл ов │ 25 файл ов │ 105 файл ов │ 115 файл ов │
        # │ 6 файл ов │ 16 файл ов │ 26 файл ов │ 106 файл ов │ 116 файл ов │
        # │ 7 файл ов │ 17 файл ов │ 27 файл ов │ 107 файл ов │ 117 файл ов │
        # │ 8 файл ов │ 18 файл ов │ 28 файл ов │ 108 файл ов │ 118 файл ов │
        # │ 9 файл ов │ 19 файл ов │ 29 файл ов │ 109 файл ов │ 119 файл ов │
        # └───────────┴────────────┴────────────┴─────────────┴─────────────┘

        $base_word = 'файл';

        $expected = [
              0 => $base_word.'ов',
              1 => $base_word,
              2 => $base_word.'а',
              3 => $base_word.'а',
              4 => $base_word.'а',
              5 => $base_word.'ов',
              6 => $base_word.'ов',
              7 => $base_word.'ов',
              8 => $base_word.'ов',
              9 => $base_word.'ов',
             10 => $base_word.'ов',
             11 => $base_word.'ов',
             12 => $base_word.'ов',
             13 => $base_word.'ов',
             14 => $base_word.'ов',
             15 => $base_word.'ов',
             16 => $base_word.'ов',
             17 => $base_word.'ов',
             18 => $base_word.'ов',
             19 => $base_word.'ов',
             20 => $base_word.'ов',
             21 => $base_word,
             22 => $base_word.'а',
             23 => $base_word.'а',
             24 => $base_word.'а',
             25 => $base_word.'ов',
             26 => $base_word.'ов',
             27 => $base_word.'ов',
             28 => $base_word.'ов',
             29 => $base_word.'ов',
            100 => $base_word.'ов',
            101 => $base_word,
            102 => $base_word.'а',
            103 => $base_word.'а',
            104 => $base_word.'а',
            105 => $base_word.'ов',
            106 => $base_word.'ов',
            107 => $base_word.'ов',
            108 => $base_word.'ов',
            109 => $base_word.'ов',
            110 => $base_word.'ов',
            111 => $base_word.'ов',
            112 => $base_word.'ов',
            113 => $base_word.'ов',
            114 => $base_word.'ов',
            115 => $base_word.'ов',
            116 => $base_word.'ов',
            117 => $base_word.'ов',
            118 => $base_word.'ов',
            119 => $base_word.'ов'
        ];

        $formula = '%^(?<variant_1>[05-9]|.*[1][0-9]|.*[^1][05-9])$|'.
                    '^(?<variant_2>[234]|.*[^1][234])$%S';

        $matches = [
            'variant_1' => 'ов',
            'variant_2' => 'а'
        ];

        foreach ($expected as $c_num => $c_expected) {
            $c_gotten = $base_word;
            $c_preg_result = [];
            preg_match($formula, (string)$c_num, $c_preg_result);
            if (isset($c_preg_result['variant_1']) && strlen($c_preg_result['variant_1'])) $c_gotten.= $matches['variant_1'];
            if (isset($c_preg_result['variant_2']) && strlen($c_preg_result['variant_2'])) $c_gotten.= $matches['variant_2'];
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_num.' '.$c_gotten, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_num.' '.$c_gotten, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        foreach ($expected as $c_num => $c_expected) {
            $c_gotten = Translation::apply('%%_number file%%_plural(number|s)', ['number' => $c_num], 'ru');
            $c_expected = $c_num.' '.$c_expected;
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => $c_expected, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

    static function test_step_code__plural(&$test, $dpath, &$c_results) {
        $data = [
            'en-0' => ['gotten' => Translation::plural(['number', 's'   ], ['number' => 0], 'en'), 'expected' => 's' ],
            'en-1' => ['gotten' => Translation::plural(['number', 's'   ], ['number' => 1], 'en'), 'expected' => ''  ],
            'en-2' => ['gotten' => Translation::plural(['number', 's'   ], ['number' => 2], 'en'), 'expected' => 's' ],
            'ru-0' => ['gotten' => Translation::plural(['number', 'ov-a'], ['number' => 0], 'ru'), 'expected' => 'ов'],
            'ru-1' => ['gotten' => Translation::plural(['number', 'ov-a'], ['number' => 1], 'ru'), 'expected' => ''  ],
            'ru-2' => ['gotten' => Translation::plural(['number', 'ov-a'], ['number' => 2], 'ru'), 'expected' => 'а' ],
            'ru-3' => ['gotten' => Translation::plural(['number', 'ov-a'], ['number' => 3], 'ru'), 'expected' => 'а' ],
            'ru-4' => ['gotten' => Translation::plural(['number', 'ov-a'], ['number' => 4], 'ru'), 'expected' => 'а' ],
            'ru-5' => ['gotten' => Translation::plural(['number', 'ov-a'], ['number' => 5], 'ru'), 'expected' => 'ов'],
            'be-0' => ['gotten' => Translation::plural(['number', 'au-a'], ['number' => 0], 'be'), 'expected' => 'аў'],
            'be-1' => ['gotten' => Translation::plural(['number', 'au-a'], ['number' => 1], 'be'), 'expected' => ''  ],
            'be-2' => ['gotten' => Translation::plural(['number', 'au-a'], ['number' => 2], 'be'), 'expected' => 'а' ],
            'be-3' => ['gotten' => Translation::plural(['number', 'au-a'], ['number' => 3], 'be'), 'expected' => 'а' ],
            'be-4' => ['gotten' => Translation::plural(['number', 'au-a'], ['number' => 4], 'be'), 'expected' => 'а' ],
            'be-5' => ['gotten' => Translation::plural(['number', 'au-a'], ['number' => 5], 'be'), 'expected' => 'аў'],
            'uk-0' => ['gotten' => Translation::plural(['number', 'iv-i'], ['number' => 0], 'uk'), 'expected' => 'ів'],
            'uk-1' => ['gotten' => Translation::plural(['number', 'iv-i'], ['number' => 1], 'uk'), 'expected' => ''  ],
            'uk-2' => ['gotten' => Translation::plural(['number', 'iv-i'], ['number' => 2], 'uk'), 'expected' => 'и' ],
            'uk-3' => ['gotten' => Translation::plural(['number', 'iv-i'], ['number' => 3], 'uk'), 'expected' => 'и' ],
            'uk-4' => ['gotten' => Translation::plural(['number', 'iv-i'], ['number' => 4], 'uk'), 'expected' => 'и' ],
            'uk-5' => ['gotten' => Translation::plural(['number', 'iv-i'], ['number' => 5], 'uk'), 'expected' => 'ів'],
        ];

        foreach ($data as $c_row_id => $c_info) {
            $c_expected = $c_info['expected'];
            $c_gotten = $c_info['gotten'];
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

    static function test_step_code__filter(&$test, $dpath, &$c_results) {
        $data = [
            'empty' => '',
            '**' =>
                'some text at line 1. | некоторый текст в строке 1.'.NL.
                'some text at line 2. | некоторый текст в строке 2.'.NL.
                'some text at line 3. | некоторый текст в строке 3.',
            'en' =>
                '%%_lang(en)'.
                'some text at line 1.'.NL.
                'some text at line 2.'.NL.
                'some text at line 3.',
            'ru' =>
                '%%_lang(ru)'.
                'некоторый текст в строке 1.'.NL.
                'некоторый текст в строке 2.'.NL.
                'некоторый текст в строке 3.',
            '**_en_ru-canonical' =>
                '%%_lang(**)'.
                'some text at line 1. | некоторый текст в строке 1.'.NL.
                'some text at line 2. | некоторый текст в строке 2.'.NL.
                'some text at line 3. | некоторый текст в строке 3.'.NL.
                '%%_lang(en)'.
                'some text at line 4.'.NL.
                'some text at line 5.'.NL.
                'some text at line 6.'.NL.
                '%%_lang(ru)'.
                'некоторый текст в строке 7.'.NL.
                'некоторый текст в строке 8.'.NL.
                'некоторый текст в строке 9.',
            '**_en_**' =>
                'some text at line 1. | некоторый текст в строке 1.'.NL.
                'some text at line 2. | некоторый текст в строке 2.'.NL.
                'some text at line 3. | некоторый текст в строке 3.'.NL.
                '%%_lang(en)'.
                'some text at line 4.'.NL.
                'some text at line 5.'.NL.
                'some text at line 6.'.NL.
                '%%_lang'.
                'some text at line 7. | некоторый текст в строке 7.'.NL.
                'some text at line 8. | некоторый текст в строке 8.'.NL.
                'some text at line 9. | некоторый текст в строке 9.',
            '**_ru_**' =>
                'some text at line 1. | некоторый текст в строке 1.'.NL.
                'some text at line 2. | некоторый текст в строке 2.'.NL.
                'some text at line 3. | некоторый текст в строке 3.'.NL.
                '%%_lang(ru)'.
                'некоторый текст в строке 4.'.NL.
                'некоторый текст в строке 5.'.NL.
                'некоторый текст в строке 6.'.NL.
                '%%_lang'.
                'some text at line 7. | некоторый текст в строке 7.'.NL.
                'some text at line 8. | некоторый текст в строке 8.'.NL.
                'some text at line 9. | некоторый текст в строке 9.'
        ];

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_en = [
            'empty' => [],
            '**' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.' ],
            'en' => [
                0 => 'some text at line 1.'.NL.
                     'some text at line 2.'.NL.
                     'some text at line 3.' ],
            'ru' => [],
            '**_en_ru-canonical' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.'.NL,
                1 => 'some text at line 4.'.NL.
                     'some text at line 5.'.NL.
                     'some text at line 6.'.NL ],
            '**_en_**' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.'.NL,
                1 => 'some text at line 4.'.NL.
                     'some text at line 5.'.NL.
                     'some text at line 6.'.NL,
                2 => 'some text at line 7. | некоторый текст в строке 7.'.NL.
                     'some text at line 8. | некоторый текст в строке 8.'.NL.
                     'some text at line 9. | некоторый текст в строке 9.' ],
            '**_ru_**' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.'.NL,
                1 => 'some text at line 7. | некоторый текст в строке 7.'.NL.
                     'some text at line 8. | некоторый текст в строке 8.'.NL.
                     'some text at line 9. | некоторый текст в строке 9.'
            ]
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected_en[$c_row_id];
            $c_gotten = Translation::filter($c_value, 'en');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'en: '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'en: '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_en_strict = [
            'empty' => [],
            '**' => [],
            'en' => [
                0 => 'some text at line 1.'.NL.
                     'some text at line 2.'.NL.
                     'some text at line 3.' ],
            'ru' => [],
            '**_en_ru-canonical' => [
                0 => 'some text at line 4.'.NL.
                     'some text at line 5.'.NL.
                     'some text at line 6.'.NL ],
            '**_en_**' => [
                0 => 'some text at line 4.'.NL.
                     'some text at line 5.'.NL.
                     'some text at line 6.'.NL ],
            '**_ru_**' => []
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected_en_strict[$c_row_id];
            $c_gotten = Translation::filter($c_value, 'en', true);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'en + trict: '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'en + trict: '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_ru = [
            'empty' => [],
            '**' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.' ],
            'en' => [],
            'ru' => [
                0 => 'некоторый текст в строке 1.'.NL.
                     'некоторый текст в строке 2.'.NL.
                     'некоторый текст в строке 3.' ],
            '**_en_ru-canonical' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.'.NL,
                1 => 'некоторый текст в строке 7.'.NL.
                     'некоторый текст в строке 8.'.NL.
                     'некоторый текст в строке 9.' ],
            '**_en_**' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.'.NL,
                1 => 'some text at line 7. | некоторый текст в строке 7.'.NL.
                     'some text at line 8. | некоторый текст в строке 8.'.NL.
                     'some text at line 9. | некоторый текст в строке 9.' ],
            '**_ru_**' => [
                0 => 'some text at line 1. | некоторый текст в строке 1.'.NL.
                     'some text at line 2. | некоторый текст в строке 2.'.NL.
                     'some text at line 3. | некоторый текст в строке 3.'.NL,
                1 => 'некоторый текст в строке 4.'.NL.
                     'некоторый текст в строке 5.'.NL.
                     'некоторый текст в строке 6.'.NL,
                2 => 'some text at line 7. | некоторый текст в строке 7.'.NL.
                     'some text at line 8. | некоторый текст в строке 8.'.NL.
                     'some text at line 9. | некоторый текст в строке 9.'
            ]
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected_ru[$c_row_id];
            $c_gotten = Translation::filter($c_value, 'ru');
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ru: '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ru: '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }

        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

        $expected_ru_strict = [
            'empty' => [],
            '**' => [],
            'en' => [],
            'ru' => [
                0 => 'некоторый текст в строке 1.'.NL.
                     'некоторый текст в строке 2.'.NL.
                     'некоторый текст в строке 3.' ],
            '**_en_ru-canonical' => [
                0 => 'некоторый текст в строке 7.'.NL.
                     'некоторый текст в строке 8.'.NL.
                     'некоторый текст в строке 9.' ],
            '**_en_**' => [],
            '**_ru_**' => [
                0 => 'некоторый текст в строке 4.'.NL.
                     'некоторый текст в строке 5.'.NL.
                     'некоторый текст в строке 6.'.NL
            ]
        ];

        foreach ($data as $c_row_id => $c_value) {
            $c_expected = $expected_ru_strict[$c_row_id];
            $c_gotten = Translation::filter($c_value, 'ru', true);
            $c_result = $c_gotten === $c_expected;
            if ($c_result === true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ru + strict: '.$c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'ru + strict: '.$c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                $c_results['return'] = 0;
                return;
            }
        }
    }

}
