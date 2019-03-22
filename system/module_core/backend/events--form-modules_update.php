<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\fieldset;
          use \effcore\group_checkboxes;
          use \effcore\markup;
          use \effcore\module;
          abstract class events_form_modules_update {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $modules = module::all_get();
    core::array_sort_by_title($modules);
    foreach ($modules as $c_module) {
      $c_updates = module::updates_get($c_module->id);
      if (count($c_updates)) {
        $c_fieldset = new fieldset($c_module->title);
        $c_fieldset->state = 'opened';
        $c_fieldset->build();
        $c_checkboxes = new group_checkboxes();
        $c_checkboxes->build();
        $c_fieldset->child_insert($c_checkboxes, 'checkboxes');
        $info->child_insert($c_fieldset, $c_module->id);
        foreach ($c_updates as $c_update) {
          $c_checkboxes->field_insert(
            $c_update->title, ['name' => 'update_'.$c_module->id.'[]', 'value' => $c_update->number]
          );
        }
      }
    }
    if ($info->children_count() == 0) {
      $form->child_update('info', new markup('x-no-result', [], 'no items'));
      $items['~apply']->disabled_set();
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        break;
    }
  }

}}