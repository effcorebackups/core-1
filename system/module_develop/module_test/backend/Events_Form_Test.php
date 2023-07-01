<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Locale;
use effcore\Markup;
use effcore\Message;
use effcore\Page;
use effcore\Test;
use effcore\Text_multiline;
use effcore\Text;
use effcore\Timer;

abstract class Events_Form_Test {

    static function on_init($event, $form, $items) {
        $id = Page::get_current()->args_get('id');
        $test = Test::get($id);
        if ($test) {
            $items['params']->description = $test->description;
            if ($test->params) {
                foreach ($test->params as $c_id => $c_param) {
                    $items['params']->child_insert($c_param, $c_id);
                    $c_param->build();
                }
            } else {
                $items['params']->child_insert(
                    new Text('No additional parameters.')
                );
            }
        } else {
            $items['~launch']->disabled_set();
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'launch':
                $id = Page::get_current()->args_get('id');
                $test = Test::get($id);
                if ($test) {
                    Timer::tap('test_total');
                    $test_result = $test->run();
                    Timer::tap('test_total');
                    # show message
                    if (!empty($test_result['return']))
                         Message::insert(new Text_multiline(['The test was successful.', 'Total run time: %%_time sec.'], ['time' => Locale::format_number(Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN)]));
                    else Message::insert('The test was failed!', 'error');
                    # make report
                    if (!empty($test_result['reports'])) {
                        $items['report']->child_select('document')->children_delete();
                        foreach ($test_result['reports'] as $c_dpath => $c_part) {
                            $c_depth = Core::path_get_depth($c_dpath);
                            if (is_array($c_part)) foreach ($c_part as $c_key => $c_line) $c_part[$c_key] = Core::return_rendered($c_line);
                            if (is_array($c_part)) $items['report']->child_select('document')->child_insert(new Markup('p', ['data-depth' => $c_depth], new Text_multiline($c_part) ));
                            else                   $items['report']->child_select('document')->child_insert(new Markup('p', ['data-depth' => $c_depth],                    $c_part  ));
                        }
                    }
                }
                break;
        }
    }

}
