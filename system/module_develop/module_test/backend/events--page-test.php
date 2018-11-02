<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\core;
          use \effcore\test;
          use \effcore\url;
          use \effcore\tabs;
          abstract class events_page_test {

  static function on_page_init($page) {
    $tests = test::all_get(false);
    $id = $page->args_get('id');
    core::array_sort_by_property($tests, 'title');
    if (!isset($tests[$id])) url::go($page->args_get('base').'/'.reset($tests)->id);
    foreach ($tests as $c_test) {
      tabs::item_insert($c_test->title, 'test_execute_'.$c_test->id, 'test_execute', $c_test->id);
    }
  }

}}
