<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\field_switcher;
          use \effcore\locale;
          use \effcore\markup;
          use \effcore\module;
          use \effcore\translation;
          abstract class events_form_modules {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $modules = module::all_get();
    core::array_sort_by_property($modules, 'title');
    foreach ($modules as $c_module) {
      $c_info = new markup('x-module-info');
      $c_switcher = new field_switcher();
      $c_switcher->build();
      $c_switcher->name_set('enable_'.$c_module->id);
      $c_switcher->value_set('on');
      $c_switcher->checked_set($c_module->state != 'off');
      $c_switcher->disabled_set($c_module->state == 'always_on');
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
  }

}}