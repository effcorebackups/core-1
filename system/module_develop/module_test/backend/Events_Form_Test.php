<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Locale;
use effcore\Markup;
use effcore\Message;
use effcore\Page;
use effcore\Test_message;
use effcore\Test;
use effcore\Text_multiline;
use effcore\Text_simple;
use effcore\Text;
use effcore\Timer;

abstract class Events_Form_Test {

    static function on_build($event, $form) {
        $id = Page::get_current()->args_get('id');
        $form->_test = Test::get($id);
        if ($form->_test) {
            $form->_test->prepare();
            if ($form->_test->params) {
                foreach ($form->_test->params as $c_id => $c_param) {
                    $form->child_select('params')->child_insert($c_param, $c_id);
                    $c_param->build();
                }
            } else {
                $form->child_select('params')->child_insert(
                    new Text('No additional parameters.')
                );
            }
        }
    }

    static function on_init($event, $form, $items) {
        if ($form->_test) {
            $items['params']->description = $form->_test->description;
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
                    $c_depth = 0;
                    Timer::tap('test_total');
                    $items['report']->child_select('document')->children_delete();
                    foreach ($test->run() as $c_tick) {
                        if ($c_tick === Test::SUCCESSFUL) break;
                        if ($c_tick === Test::FAILED    ) break;
                        if ($c_tick instanceof Test_message && $c_tick->type === 'dpath') {
                            $c_depth = Core::path_get_depth($c_tick->value);
                            $items['report']->child_select('document')->child_insert(
                                (new Markup('p', [], str_repeat('  ', $c_depth).'### '.$c_tick->value))->render()
                            );
                        }
                        if ($c_tick instanceof Text_simple) {
                            if ($c_tick->text === '') $items['report']->child_select('document')->child_insert((new Markup('p', ['data-is-delimiter' => true], ' '                                   ))->render());
                            if ($c_tick->text !== '') $items['report']->child_select('document')->child_insert((new Markup('p', [                           ], [str_repeat('  ', $c_depth), $c_tick] ))->render());
                        }
                    }
                    Timer::tap('test_total');
                    # show message
                    if     ($c_tick === Test::SUCCESSFUL) Message::insert(new Text_multiline(['The test was successful.', 'Total run time: %%_time sec.'], ['time' => Locale::format_number(Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN)]));
                    elseif ($c_tick === Test::FAILED    ) Message::insert(new Text_multiline(['The test was failed!',     'Total run time: %%_time sec.'], ['time' => Locale::format_number(Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN)]), 'error');
                    else                                  Message::insert(new Text_multiline(['The test was completed.',  'Total run time: %%_time sec.'], ['time' => Locale::format_number(Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN)]), 'notice');
                }
                break;
        }
    }

}
