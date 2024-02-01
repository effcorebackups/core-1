<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Color;
use effcore\Core;
use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Color {

    static function test_step_code__RGB_to_HSV(&$test, $dpath, &$c_results) {
        $data = [
            '[    0|    0|    0]' => ['value' => ['r' =>     0, 'g' =>     0, 'b' =>     0], 'expected' => ['h' =>   0, 's' =>   0, 'v' =>   0]],
            '[    0|    0|  255]' => ['value' => ['r' =>     0, 'g' =>     0, 'b' =>   255], 'expected' => ['h' => 240, 's' => 100, 'v' => 100]],
            '[    0|  127|  255]' => ['value' => ['r' =>     0, 'g' =>   127, 'b' =>   255], 'expected' => ['h' => 210, 's' => 100, 'v' => 100]],
            '[    0|  255|    0]' => ['value' => ['r' =>     0, 'g' =>   255, 'b' =>     0], 'expected' => ['h' => 120, 's' => 100, 'v' => 100]],
            '[    0|  255|  127]' => ['value' => ['r' =>     0, 'g' =>   255, 'b' =>   127], 'expected' => ['h' => 150, 's' => 100, 'v' => 100]],
            '[    0|  255|  255]' => ['value' => ['r' =>     0, 'g' =>   255, 'b' =>   255], 'expected' => ['h' => 180, 's' => 100, 'v' => 100]],
            '[  127|    0|  255]' => ['value' => ['r' =>   127, 'g' =>     0, 'b' =>   255], 'expected' => ['h' => 270, 's' => 100, 'v' => 100]],
            '[  127|  127|  255]' => ['value' => ['r' =>   127, 'g' =>   127, 'b' =>   255], 'expected' => ['h' => 240, 's' =>  50, 'v' => 100]],
            '[  127|  255|    0]' => ['value' => ['r' =>   127, 'g' =>   255, 'b' =>     0], 'expected' => ['h' =>  90, 's' => 100, 'v' => 100]],
            '[  127|  255|  127]' => ['value' => ['r' =>   127, 'g' =>   255, 'b' =>   127], 'expected' => ['h' => 120, 's' =>  50, 'v' => 100]],
            '[  255|    0|    0]' => ['value' => ['r' =>   255, 'g' =>     0, 'b' =>     0], 'expected' => ['h' =>   0, 's' => 100, 'v' => 100]],
            '[  255|    0|  127]' => ['value' => ['r' =>   255, 'g' =>     0, 'b' =>   127], 'expected' => ['h' => 330, 's' => 100, 'v' => 100]],
            '[  255|    0|  255]' => ['value' => ['r' =>   255, 'g' =>     0, 'b' =>   255], 'expected' => ['h' => 300, 's' => 100, 'v' => 100]],
            '[  255|  127|    0]' => ['value' => ['r' =>   255, 'g' =>   127, 'b' =>     0], 'expected' => ['h' =>  30, 's' => 100, 'v' => 100]],
            '[  255|  127|  127]' => ['value' => ['r' =>   255, 'g' =>   127, 'b' =>   127], 'expected' => ['h' =>   0, 's' =>  50, 'v' => 100]],
            '[  255|  255|  255]' => ['value' => ['r' =>   255, 'g' =>   255, 'b' =>   255], 'expected' => ['h' =>   0, 's' =>   0, 'v' => 100]],
            '[10000|    0|    0]' => ['value' => ['r' => 10000, 'g' =>     0, 'b' =>     0], 'expected' => ['h' =>   0, 's' => 100, 'v' => 100]],
            '[    0|10000|    0]' => ['value' => ['r' =>     0, 'g' => 10000, 'b' =>     0], 'expected' => ['h' => 120, 's' => 100, 'v' => 100]],
            '[    0|    0|10000]' => ['value' => ['r' =>     0, 'g' =>     0, 'b' => 10000], 'expected' => ['h' => 240, 's' => 100, 'v' => 100]],
            '[10000|10000|10000]' => ['value' => ['r' => 10000, 'g' => 10000, 'b' => 10000], 'expected' => ['h' =>   0, 's' =>   0, 'v' => 100]],
        ];

        foreach ($data as $c_row_id => $c_info) {
            $c_value = $c_info['value'];
            $c_expected = $c_info['expected'];
            $c_gotten = Color::RGB_to_HSV($c_value['r'], $c_value['g'], $c_value['b']);
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

        if (Core::is_CLI()) return;

        # iterate over all possible values
        for ($r = -10; $r <= 260; $r++) {
            for ($g = -10; $g <= 260; $g++) {
                for ($b = -10; $b <= 260; $b++) {
                    $c_hsv = Color::RGB_to_HSV($r, $g, $b);
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                    $c_gotten = $c_hsv['h'];
                    $c_expected = 0;
                    if ($c_gotten < 0) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsv['h'].';s='.$c_hsv['s'].';v='.$c_hsv['v'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    $c_expected = 360;
                    if ($c_gotten > $c_expected) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsv['h'].';s='.$c_hsv['s'].';v='.$c_hsv['v'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                    $c_gotten = $c_hsv['s'];
                    $c_expected = 0;
                    if ($c_gotten < 0) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsv['h'].';s='.$c_hsv['s'].';v='.$c_hsv['v'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    $c_expected = 100;
                    if ($c_gotten > $c_expected) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsv['h'].';s='.$c_hsv['s'].';v='.$c_hsv['v'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                    $c_gotten = $c_hsv['v'];
                    $c_expected = 0;
                    if ($c_gotten < 0) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsv['h'].';s='.$c_hsv['s'].';v='.$c_hsv['v'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    $c_expected = 100;
                    if ($c_gotten > $c_expected) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsv['h'].';s='.$c_hsv['s'].';v='.$c_hsv['v'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                }
            }
        }
    }

    static function test_step_code__RGB_to_HSL(&$test, $dpath, &$c_results) {
        $data = [
            '[    0|    0|    0]' => ['value' => ['r' =>     0, 'g' =>     0, 'b' =>     0], 'expected' => ['h' =>   0, 's' =>   0, 'l' =>   0]],
            '[    0|    0|  255]' => ['value' => ['r' =>     0, 'g' =>     0, 'b' =>   255], 'expected' => ['h' => 240, 's' => 100, 'l' =>  50]],
            '[    0|  127|  255]' => ['value' => ['r' =>     0, 'g' =>   127, 'b' =>   255], 'expected' => ['h' => 210, 's' => 100, 'l' =>  50]],
            '[    0|  255|    0]' => ['value' => ['r' =>     0, 'g' =>   255, 'b' =>     0], 'expected' => ['h' => 120, 's' => 100, 'l' =>  50]],
            '[    0|  255|  127]' => ['value' => ['r' =>     0, 'g' =>   255, 'b' =>   127], 'expected' => ['h' => 150, 's' => 100, 'l' =>  50]],
            '[    0|  255|  255]' => ['value' => ['r' =>     0, 'g' =>   255, 'b' =>   255], 'expected' => ['h' => 180, 's' => 100, 'l' =>  50]],
            '[  127|    0|  255]' => ['value' => ['r' =>   127, 'g' =>     0, 'b' =>   255], 'expected' => ['h' => 270, 's' => 100, 'l' =>  50]],
            '[  127|  127|  255]' => ['value' => ['r' =>   127, 'g' =>   127, 'b' =>   255], 'expected' => ['h' => 240, 's' => 100, 'l' =>  75]],
            '[  127|  255|    0]' => ['value' => ['r' =>   127, 'g' =>   255, 'b' =>     0], 'expected' => ['h' =>  90, 's' => 100, 'l' =>  50]],
            '[  127|  255|  127]' => ['value' => ['r' =>   127, 'g' =>   255, 'b' =>   127], 'expected' => ['h' => 120, 's' => 100, 'l' =>  75]],
            '[  255|    0|    0]' => ['value' => ['r' =>   255, 'g' =>     0, 'b' =>     0], 'expected' => ['h' =>   0, 's' => 100, 'l' =>  50]],
            '[  255|    0|  127]' => ['value' => ['r' =>   255, 'g' =>     0, 'b' =>   127], 'expected' => ['h' => 330, 's' => 100, 'l' =>  50]],
            '[  255|    0|  255]' => ['value' => ['r' =>   255, 'g' =>     0, 'b' =>   255], 'expected' => ['h' => 300, 's' => 100, 'l' =>  50]],
            '[  255|  127|    0]' => ['value' => ['r' =>   255, 'g' =>   127, 'b' =>     0], 'expected' => ['h' =>  30, 's' => 100, 'l' =>  50]],
            '[  255|  127|  127]' => ['value' => ['r' =>   255, 'g' =>   127, 'b' =>   127], 'expected' => ['h' =>   0, 's' => 100, 'l' =>  75]],
            '[  255|  255|  255]' => ['value' => ['r' =>   255, 'g' =>   255, 'b' =>   255], 'expected' => ['h' =>   0, 's' =>   0, 'l' => 100]],
            '[10000|    0|    0]' => ['value' => ['r' => 10000, 'g' =>     0, 'b' =>     0], 'expected' => ['h' =>   0, 's' => 100, 'l' =>  50]],
            '[    0|10000|    0]' => ['value' => ['r' =>     0, 'g' => 10000, 'b' =>     0], 'expected' => ['h' => 120, 's' => 100, 'l' =>  50]],
            '[    0|    0|10000]' => ['value' => ['r' =>     0, 'g' =>     0, 'b' => 10000], 'expected' => ['h' => 240, 's' => 100, 'l' =>  50]],
            '[10000|10000|10000]' => ['value' => ['r' => 10000, 'g' => 10000, 'b' => 10000], 'expected' => ['h' =>   0, 's' =>   0, 'l' => 100]],
        ];

        foreach ($data as $c_row_id => $c_info) {
            $c_value = $c_info['value'];
            $c_expected = $c_info['expected'];
            $c_gotten = Color::RGB_to_HSL($c_value['r'], $c_value['g'], $c_value['b']);
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

        if (Core::is_CLI()) return;

        # iterate over all possible values
        for ($r = -10; $r <= 260; $r++) {
            for ($g = -10; $g <= 260; $g++) {
                for ($b = -10; $b <= 260; $b++) {
                    $c_hsl = Color::RGB_to_HSL($r, $g, $b);
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                    $c_gotten = $c_hsl['h'];
                    $c_expected = 0;
                    if ($c_gotten < 0) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsl['h'].';s='.$c_hsl['s'].';l='.$c_hsl['l'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    $c_expected = 360;
                    if ($c_gotten > $c_expected) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsl['h'].';s='.$c_hsl['s'].';l='.$c_hsl['l'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                    $c_gotten = $c_hsl['s'];
                    $c_expected = 0;
                    if ($c_gotten < 0) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsl['h'].';s='.$c_hsl['s'].';l='.$c_hsl['l'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    $c_expected = 100;
                    if ($c_gotten > $c_expected) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsl['h'].';s='.$c_hsl['s'].';l='.$c_hsl['l'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
                    $c_gotten = $c_hsl['l'];
                    $c_expected = 0;
                    if ($c_gotten < 0) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsl['h'].';s='.$c_hsl['s'].';l='.$c_hsl['l'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                    $c_expected = 100;
                    if ($c_gotten > $c_expected) {
                        $c_results['reports'][$dpath][] = new Text('checking of item "%%_id": "%%_result"', ['id' => 'r='.$r.';g='.$g.';b='.$b.'|h='.$c_hsl['h'].';s='.$c_hsl['s'].';l='.$c_hsl['l'], 'result' => (new Text('failure'))->render()]);
                        $c_results['reports'][$dpath][] = new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                        $c_results['reports'][$dpath][] = new Text('gotten value: %%_value', ['value' => Test::result_prepare($c_gotten)]);
                        $c_results['return'] = 0;
                        return;
                    }
                }
            }
        }
    }

}
