<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\core;
          use \effcore\test;
          use \effcore\url;
          use \effcore\tabs_item;
          abstract class events_page_test {

  static function on_page_init($page) {
    $tests = test::get_all(false);
    $id = $page->args_get('id');
    core::array_sort_by_title($tests);
    if (!isset($tests[$id])) url::go($page->args_get('base').'/'.reset($tests)->id);
    foreach ($tests as $c_test) {
      tabs_item::insert($c_test->title,
        'test_execute_'.$c_test->id,
        'test_execute', $c_test->id, null, ['class' => [
             'execute-'.$c_test->id =>
             'execute-'.$c_test->id]]);
    }
  }

}}
