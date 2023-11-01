<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Translation {

    static function test_step_code__research_word__seconds(&$test, $dpath, &$c_results) {

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
        # │  91 секунд а
        # --------------
        # │ 101 секунд а
        # │ 111 секунд
        # │ 121 секунд а
        # │ 131 секунд а
        # │ 191 секунд а

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
        # │  92 секунд ы
        # │  93 секунд ы
        # │  94 секунд ы
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
        # │ 192 секунд ы
        # │ 193 секунд ы
        # │ 194 секунд ы

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
    }

    static function test_step_code__research_word__files(&$test, $dpath, &$c_results) {

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
    }

}
