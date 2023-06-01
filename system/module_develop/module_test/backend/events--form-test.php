<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\core;
use effcore\locale;
use effcore\markup;
use effcore\message;
use effcore\page;
use effcore\test;
use effcore\text_multiline;
use effcore\text;
use effcore\timer;

abstract class events_form_test {

    static function on_init($event, $form, $items) {
        $id = page::get_current()->args_get('id');
        $test = test::get($id);
        if ($test) {
            $items['params']->description = $test->description;
            if ($test->params) {
                foreach ($test->params as $c_id => $c_param) {
                    $items['params']->child_insert($c_param, $c_id);
                    $c_param->build();
                }
            } else {
                $items['params']->child_insert(
                    new text('No additional parameters.')
                );
            }
        } else {
            $items['~launch']->disabled_set();
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'launch':
                $id = page::get_current()->args_get('id');
                $test = test::get($id);
                if ($test) {
                    timer::tap('test_total');
                    $test_result = $test->run();
                    timer::tap('test_total');
                    # show message
                    if (!empty($test_result['return']))
                         message::insert(new text_multiline(['The test was successful.', 'Total run time: %%_time sec.'], ['time' => locale::format_number(timer::period_get('test_total', -1, -2), core::FPART_MAX_LEN)]));
                    else message::insert('The test was failed!', 'error');
                    # make report
                    if (!empty($test_result['reports'])) {
                        $items['report']->child_select('document')->children_delete();
                        foreach ($test_result['reports'] as $c_dpath => $c_part) {
                            $c_depth = core::path_get_depth($c_dpath);
                            if (is_array($c_part)) foreach ($c_part as $c_key => $c_line) $c_part[$c_key] = core::return_rendered($c_line);
                            if (is_array($c_part)) $items['report']->child_select('document')->child_insert(new markup('p', ['data-depth' => $c_depth], new text_multiline($c_part) ));
                            else                   $items['report']->child_select('document')->child_insert(new markup('p', ['data-depth' => $c_depth],                    $c_part  ));
                        }
                    }
                }
                break;
        }
    }

}
