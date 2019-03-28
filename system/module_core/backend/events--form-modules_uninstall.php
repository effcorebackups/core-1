<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\cache;
          use \effcore\core;
          use \effcore\event;
          use \effcore\group_checkboxes;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_modules_uninstall {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $installed_by_boot = core::boot_select('installed');
    $enabled_by_boot = core::boot_select('enabled');
    $embed = module::embed_get();
    $modules = module::all_get();
    $checkboxes = new group_checkboxes();
    $checkboxes->description = 'The removing module should be disabled at first. Embed modules cannot be removed.';
    $checkboxes->build();
    core::array_sort_by_title($modules);
    foreach ($modules as $c_module) {
      if  (!isset($embed            [$c_module->id]) &&
            isset($installed_by_boot[$c_module->id])) {
        if (isset($enabled_by_boot  [$c_module->id]))
        $checkboxes->disabled[$c_module->id] = $c_module->id;
        $checkboxes->field_insert(
          $c_module->title, null, ['name' => 'uninstall[]', 'value' => $c_module->id]
        );
      }
    }
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
        $embed = module::embed_get();
        $modules = module::all_get();
        $modules_to_uninstall = [];
        $include_paths        = [];
      # collect information
        if  (isset($items['*uninstall'])) {
          foreach ($items['*uninstall']->values_get() as $c_module_id) {
            $c_module = $modules[$c_module_id];
            if (!isset($embed[$c_module->id])) {
              $modules_to_uninstall[$c_module->id] = $c_module;
              $include_paths       [$c_module->id] = $c_module->path;
            }
          }
        }
      # uninstall modules
        if ($modules_to_uninstall) {
          cache::update_global($include_paths);
          foreach ($modules_to_uninstall as $c_module) {
            event::start('on_module_uninstall', $c_module->id);
          }
        }
      # update caches and this form
        cache::update_global();
        $form->child_select('info')->children_delete_all();
        static::on_init($form, $items);
      # show report
        $installed_by_boot = core::boot_select('installed');
        if ($modules_to_uninstall) {
          foreach ($modules_to_uninstall as $c_module) {
            if (!isset($installed_by_boot[$c_module->id])) {
              message::insert(
                new text('Module data %%_title (%%_id) has been removed.', ['title' => translation::get($c_module->title), 'id' => $c_module->id])
              );
            }
          }
        } else {
          message::insert(
            'Nothing selected!', 'warning'
          );
        }
        break;
    }
  }

}}