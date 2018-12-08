<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\event;
          use \effcore\field_switcher;
          use \effcore\locale;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\node;
          use \effcore\page;
          use \effcore\storage_nosql_files;
          use \effcore\text_simple;
          use \effcore\url;
          abstract class events_form_modules {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $enabled_by_boot = core::boot_select('enabled');
    $modules = module::all_get();
    $embed   = module::embed_get();
    core::array_sort_by_property($modules, 'title');
    foreach ($modules as $c_module) {
      $c_dependencies = $c_module->dependencies_status_get();
      $c_dependencies_php_items = new node;
      $c_dependencies_sys_items = new node;
      $c_is_ok_php_dependencies = !isset(array_count_values($c_dependencies->php)[0]);
      $c_is_ok_sys_dependencies = !isset(array_count_values($c_dependencies->sys)[0]);
      foreach ($c_dependencies->php as $c_id => $c_state) $c_dependencies_php_items->child_insert(new markup('x-dependency', ['data-state' => $c_state], new text_simple(strtolower($c_id))), strtolower($c_id));
      foreach ($c_dependencies->sys as $c_id => $c_state) $c_dependencies_sys_items->child_insert(new markup('x-dependency', ['data-state' => $c_state], new text_simple(strtolower($c_id))), strtolower($c_id));
      $c_info = new markup('x-module-info');
      $c_switcher = new field_switcher();
      $c_switcher->build();
      $c_switcher->name_set('is_enabled[]');
      $c_switcher->value_set($c_module->id);
      $c_switcher->checked_set (isset($enabled_by_boot[$c_module->id]));
      $c_switcher->disabled_set(isset($embed          [$c_module->id]) || !$c_is_ok_php_dependencies || !$c_is_ok_sys_dependencies);
      $c_info->child_insert($c_switcher, 'switcher');
      $c_info->child_insert(new markup('x-module-title',       [], [new markup('x-value', [], $c_module->title)]),                                                                           'title'      );
      $c_info->child_insert(new markup('x-module-id',          [], [new markup('x-label', [], 'id'),          ': ', new markup('x-value', [], $c_module->id.' ')]),                          'id'         );
      $c_info->child_insert(new markup('x-module-version',     [], [new markup('x-label', [], 'version'),     ': ', new markup('x-value', [], locale::version_format($c_module->version))]), 'version'    );
      $c_info->child_insert(new markup('x-module-description', [], [new markup('x-label', [], 'description'), ': ', new markup('x-value', [], $c_module->description)]),                     'description');
      $c_info->child_insert(new markup('x-module-path',        [], [new markup('x-label', [], 'path'),        ': ', new markup('x-value', [], $c_module->path)]),                            'path'       );
      if ($c_dependencies_php_items->children_count()) $c_info->child_insert(new markup('x-dependencies', ['data-type' => 'sys'], [new markup('x-label', [], 'php dependencies'),    ': ', $c_dependencies_php_items]), 'dependencies_php');
      if ($c_dependencies_sys_items->children_count()) $c_info->child_insert(new markup('x-dependencies', ['data-type' => 'php'], [new markup('x-label', [], 'system dependencies'), ': ', $c_dependencies_sys_items]), 'dependencies_sys');
      $info->child_insert($c_info, 'module_'.$c_module->id);
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $enabled_by_boot = core::boot_select('enabled');
        $embed = module::embed_get();
        $modules_to_enable  = [];
        $modules_to_disable = [];
        foreach (module::all_get() as $c_module) {
          if (!isset($embed[$c_module->id])) {
            if ($items['#is_enabled:'.$c_module->id]->checked_get()          && isset($enabled_by_boot[$c_module->id]) == false) $modules_to_enable [$c_module->id] = $c_module->path;
            if ($items['#is_enabled:'.$c_module->id]->checked_get() == false && isset($enabled_by_boot[$c_module->id]))          $modules_to_disable[$c_module->id] = $c_module->path;
          }
        }
      # enable modules
        if ($modules_to_enable) {
          static::cache_full_reset($modules_to_enable);
          foreach ($modules_to_enable as $c_id => $c_path) {
            if (!$c_module->is_installed())
            event::start('on_module_install', $c_id);
            event::start('on_module_enable',  $c_id);
          }
        }
      # disable modules
        if ($modules_to_disable) {
          foreach ($modules_to_disable as $c_id => $c_path) {
            event::start('on_module_disable', $c_id);
          }
        }
      # update cache and this form
        static::cache_full_reset();
        $form->child_select('info')->children_delete_all();
        static::on_init($form, $items);
        break;
      case 'refresh':
        static::cache_full_reset();
        url::go(page::current_get()->args_get('base'));
        break;
    }
  }

  static function cache_full_reset($modules_to_enable = []) {
    storage_nosql_files::cache_files_cleaning();
    core::structures_map_get         (true, $modules_to_enable);
    storage_nosql_files::cache_update(true, $modules_to_enable);
    core::structures_cache_cleaning();
  }

}}