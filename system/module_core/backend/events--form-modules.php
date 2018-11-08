<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\field_switcher;
          use \effcore\locale;
          use \effcore\markup;
          use \effcore\module_embed;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\translation;
          abstract class events_form_modules {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $modules = module::all_get();
    $boot_installed = module::installed_by_boot_get();
    $boot_enabled   = module::enabled_by_boot_get();
    core::array_sort_by_property($modules, 'title');
    foreach ($modules as $c_module) {
      $c_info = new markup('x-module-info');
      $c_switcher = new field_switcher();
      $c_switcher->build();
      $c_switcher->name_set('is_enabled[]');
      $c_switcher->value_set($c_module->id);
      $c_switcher->checked_set(isset($boot_enabled[$c_module->id]));
      $c_switcher->disabled_set($c_module instanceof module ? false : true);
      $c_info->child_insert($c_switcher, 'switcher');
      $c_info->child_insert(new markup('x-module-title',       [], [new markup('x-value', [], $c_module->title)]),                                                                           'title');
      $c_info->child_insert(new markup('x-module-id',          [], [new markup('x-label', [], 'id'),          ': ', new markup('x-value', [], $c_module->id.' ')]),                          'id');
      $c_info->child_insert(new markup('x-module-version',     [], [new markup('x-label', [], 'version'),     ': ', new markup('x-value', [], locale::format_version($c_module->version))]), 'version');
      $c_info->child_insert(new markup('x-module-description', [], [new markup('x-label', [], 'description'), ': ', new markup('x-value', [], $c_module->description)]),                     'description');
      $c_info->child_insert(new markup('x-module-path',        [], [new markup('x-label', [], 'path'),        ': ', new markup('x-value', [], $c_module->path)]),                            'path');
      $info->child_insert($c_info, 'module_'.$c_module->id);
    }
  }

  static function on_validate($form, $items) {
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $boot_installed = [];
        $boot_enabled   = [];
        $modules = module::all_get();
        $embed = module::embed_get();
        foreach ($modules as $c_module) {
          if (isset($embed[$c_module->id]) || $items['#is_enabled:'.$c_module->id]->checked_get()) {
            $boot_installed[$c_module->id] = $c_module->id;
            $boot_enabled  [$c_module->id] = $c_module->id;
          }
        }
        module::installed_by_boot_set($boot_installed);
        module::enabled_by_boot_set($boot_enabled);
        break;
      case 'refresh':
        break;
    }
  }

}}