<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use const \effcore\br;
          use \effcore\button;
          use \effcore\core;
          use \effcore\event;
          use \effcore\fieldset;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\text_multiline;
          use \effcore\text;
          abstract class events_form_modules_update_files {

  static function on_init($event, $form, $items) {
    $info = $form->child_select('info');
    $info->children_delete();
    $bundles = module::bundle_get_all();
    core::array_sort_by_text_property($bundles);
    foreach ($bundles as $c_bundle) {
      if (isset($c_bundle->repo_update)) {
        $c_button_update = new button('update', ['title' => new text('update')]);
        $c_button_update->build();
        $c_button_update->value_set($c_bundle->id);
        $c_button_update->_type = 'update';
        $c_button_update->_id = $c_bundle->id;
        $c_report = new markup('x-document', ['data-type' => 'report'], new text('The report will be created after submitting the form.'));
        $c_fieldset = new fieldset($c_bundle->title);
        $c_fieldset->child_insert($c_report, 'report');
        $c_fieldset->child_insert($c_button_update, 'button_update');
        $info->child_insert($c_fieldset, $c_bundle->id);
      }
    }
    if ($info->children_select_count() == 0) {
      $form->child_update('info', new markup('x-no-items', ['data-style' => 'table'], 'no updates'));
    }
  }

  static function on_submit($event, $form, $items) {
    $report = $items['info']->child_select($bundle_id)->child_select('report');
    $report->children_delete();
    switch ($form->clicked_button->_type) {
      case 'update':
        $bundle_id = $form->clicked_button->_id;
        $result = event::start('on_module_update_files', $bundle_id, [$bundle_id]);
        foreach ($result as $c_handler => $c_results) {
          $report->child_insert(new markup('p', [], 'Call '.$c_handler));
          foreach ($c_results as $c_result) {
            if (is_null ($c_result)) $report->child_insert(new markup('p', [], 'null'));
            if (is_array($c_result)) $report->child_insert(new markup('p', [], new text_multiline($c_result, [], br, false, false)));
          }
        }
        break;
    }
  }

}}