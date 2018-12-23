<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\event;
          use \effcore\group_checkboxes;
          use \effcore\module;
          abstract class events_form_modules_uninstall {

  static function on_init($form, $items) {
    $installed_by_boot = core::boot_select('installed');
    $enabled_by_boot = core::boot_select('enabled');
    $embed = module::embed_get();
    $modules = module::all_get();
    $checkboxes = new group_checkboxes();
    $checkboxes->description = 'The removing module should be disabled at first. Embed modules cannot be removed.';
    $checkboxes->build();
    foreach ($modules as $c_module) {
      if  (!isset($embed            [$c_module->id]) &&
            isset($installed_by_boot[$c_module->id])) {
        if (isset($enabled_by_boot  [$c_module->id]))
        $checkboxes->disabled[$c_module->id] = $c_module->id;
        $checkboxes->field_insert(
          $c_module->title, ['name' => 'uninstall[]', 'value' => $c_module->id]
        );
      }
    }
    $info = $form->child_select('info');
    $info->child_insert($checkboxes, 'checkboxes');
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        foreach ($items['*uninstall']->values_get() as $c_module_id) {
          event::start('on_module_uninstall', $c_module_id);
        }
        break;
    }
  }

}}