<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\group_checkboxes;
          use \effcore\markup;
          use \effcore\module;
          abstract class events_form_modules_update {

  static function on_init($form, $items) {
    $updates = module::updates_get();
    $checkboxes = new group_checkboxes();
    $checkboxes->build();
    foreach ($updates as $c_row_id => $c_update) {
      $checkboxes->field_insert(
        $c_update->title, ['name' => 'update[]', 'value' => $c_row_id]
      );
    }
    $info = $form->child_select('info');
    if ($checkboxes->children_count())
         $info->child_insert($checkboxes, 'checkboxes');
    else $form->child_update('info', new markup('x-no-result', [], 'no items'));
    if (count($checkboxes->disabled) ==
              $checkboxes->children_count()) {
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