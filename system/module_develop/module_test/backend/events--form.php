<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use \effcore\message;
          use \effcore\test;
          use \effcore\text_simple;
          abstract class events_form extends \effcore\events_form {

  ##################
  ### form: test ###
  ##################

  static function on_init_test($form, $items) {
    $tests = test::all_get(false);
    foreach ($tests as $c_test) {
      $items['#select_test']->option_insert($c_test->title, $c_test->id);
    }
  }

  static function on_submit_test($form, $items) {
    $test = test::get($items['#select_test']->value_get());
    $result = $test->run();
    if ($result) {
      message::insert('The test was successful.');
    }
  }

}}