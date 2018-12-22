<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\group_checkboxes;
          use \effcore\module;
          abstract class events_form_modules_uninstall {

  static function on_init($form, $items) {
    $installed_by_boot = core::boot_select('installed');
    $embed = module::embed_get();
    $modules = module::all_get();
    $checkboxes = new group_checkboxes();
    $checkboxes->build();
    foreach ($modules as $c_module) {
      $checkboxes->field_insert(
        $c_module->title, ['name' => 'uninstall[]', 'value' => $c_module->id]
      );
    }
    $info = $form->child_select('info');
    $info->child_insert($checkboxes, 'checkboxes');
  }

  static function on_submit($form, $items) {
  }

}}