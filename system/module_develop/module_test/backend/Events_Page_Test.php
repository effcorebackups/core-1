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
use effcore\Url;

abstract class Events_Page_Test {

    static function on_redirect($event, $page) {
        $id = Page::get_current()->args_get('id');
        $tests = Test::get_all(false);
        foreach ($tests as $c_test)
            $c_test->title_translated = $c_test->title instanceof Text ?
                                        $c_test->title  ->render() :
                              (new Text($c_test->title))->render();
        Core::array_sort_by_string($tests, 'title_translated', Core::SORT_DSC, false);
        if (empty($tests[$id])) {
            Url::go(Page::get_current()->args_get('base').'/'.reset($tests)->id);
        }
    }

    static function on_tab_build_before($event, $tab) {
        $tests = Test::get_all(false);
        foreach ($tests as $c_test)
            $c_test->title_translated = $c_test->title instanceof Text ?
                                        $c_test->title  ->render() :
                              (new Text($c_test->title))->render();
        Core::array_sort_by_string($tests, 'title_translated', Core::SORT_DSC, false);
        foreach ($tests as $c_test) {
            Tab_item::insert(              $c_test->title_translated,
                'test_execution_'         .$c_test->id,
                'test_execution', 'tests', $c_test->id, null, [], [], false, 0, 'develop'
            );
        }
    }

}
