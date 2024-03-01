<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Page;
use effcore\Tab_item;
use effcore\Test;
use effcore\Text;
use effcore\URL;

abstract class Events_Page_Test {

    static function on_redirect($event, $page) {
        $id = Page::get_current()->args_get('id');
        $tests = Test::get_all(false);
        if (empty($tests[$id])) {
            $tests_list = [];
            foreach ($tests as $c_test) {
                $tests_list[$c_test->type][$c_test->id] = $c_test->title instanceof Text ?
                                                          $c_test->title->render() :
                                                (new Text($c_test->title))->render();
            }
            $first_group = reset($tests_list);
            Core::array_sort($first_group, Core::SORT_DSC, false);
            URL::go(Page::get_current()->args_get('base').'/'.key($first_group));
        }
    }

    static function on_tab_build_before($event, $tab) {
        $tests = Test::get_all(false);
        $tests_list = [];
        foreach ($tests as $c_test) {
            $tests_list[$c_test->type][$c_test->id] = $c_test->title instanceof Text ?
                                                      $c_test->title->render() :
                                            (new Text($c_test->title))->render();
        }
        foreach ($tests_list as $c_group_id => &$c_group) {
            Tab_item::insert(     $c_group_id,
                'test_execution_'.$c_group_id,
                'test_execution', 'tests', null, null, [], [], false, 0, 'develop');
            Core::array_sort($c_group, Core::SORT_DSC, false);
            foreach ($c_group as $c_id => $c_title) {
                Tab_item::insert(     $c_title,
                    'test_execution_'.$c_group_id.'_'.$c_id,
                    'test_execution_'.$c_group_id, 'tests', $c_id, null, [], [], false, 0, 'develop'
                );
            }
        }
    }

}
