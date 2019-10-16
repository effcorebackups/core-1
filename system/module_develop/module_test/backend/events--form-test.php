<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\test {
          use const \effcore\br;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\page;
          use \effcore\test;
          use \effcore\text;
          abstract class events_form_test {

  static function on_init($event, $form, $items) {
    $id = page::get_current()->args_get('id');
    if ($id) {
      $test = test::get($id);
      $items['params']->description = $test->description;
      $items['report']->child_select('document')->child_insert(
        new text('The report will be created after running the test.'));
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
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'launch':
        $id = page::get_current()->args_get('id');
        if ($id) {
          $test = test::get($id);
          $test_result = $test->run();
        # show message
          if (!empty($test_result['return']))
               message::insert('The test was successful.'     );
          else message::insert('The test was failed!', 'error');
        # make report
          if (!empty($test_result['reports'])) {
            $items['report']->child_select('document')->children_delete();
            foreach ($test_result['reports'] as $c_part) {
              if (is_array($c_part))
                   $items['report']->child_select('document')->child_insert(new markup('p', [], implode(br, $c_part)));
              else $items['report']->child_select('document')->child_insert(new markup('p', [],             $c_part) );
            }
          }
        }
        break;
    }
  }

}}