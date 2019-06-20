<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\cache;
          use \effcore\core;
          use \effcore\event;
          use \effcore\field_switcher;
          use \effcore\fieldset;
          use \effcore\locale;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\node;
          use \effcore\storage_nosql_files;
          use \effcore\text_simple;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_modules_install {

  static function on_init($form, $items) {
    $info = $form->child_select('info');
    $enabled_by_boot = core::boot_select('enabled');
    $embed   = module::get_embed ();
    $modules = module::get_all   ();
    $groups  = module::groups_get();
    $modules_by_groups = [];
    core::array_sort_text($groups);
    foreach ($groups as $c_group_id => $c_group_title) {
      $c_fieldset = new fieldset($c_group_title);
      $c_fieldset->state = 'closed';
      $info->child_insert($c_fieldset, $c_group_id);
      foreach ($modules as $c_module)
        if ($c_group_id == $c_module->group_get_id())
          $modules_by_groups[$c_group_id][$c_module->id] = $c_module;
      core::array_sort_by_title(
        $modules_by_groups[$c_group_id]
      );
    }
    foreach ($modules_by_groups as $c_modules) {
      foreach ($c_modules as $c_module) {
        $c_depended               = $c_module->depended_status_get    ();
        $c_dependencies           = $c_module->dependencies_status_get();
        $c_is_ok_php_dependencies = !isset(array_count_values($c_dependencies->php)[0]);
        $c_is_ok_sys_dependencies = !isset(array_count_values($c_dependencies->sys)[0]);
        $c_is_ok_sys_depended     = !isset(array_count_values($c_depended         )[1]);
        $c_dependencies_php_items = new node;
        $c_dependencies_sys_items = new node;
        $c_depended_sys_items     = new node;
        foreach ($c_dependencies->php as $c_id => $c_state) $c_dependencies_php_items->child_insert(new markup('x-sticker', ['data-state' => $c_state ? ''   : 'warning'], new text_simple(strtolower($c_id))), strtolower($c_id));
        foreach ($c_dependencies->sys as $c_id => $c_state) $c_dependencies_sys_items->child_insert(new markup('x-sticker', ['data-state' => $c_state ? ''   : 'warning'], new text_simple(strtolower($c_id))), strtolower($c_id));
        foreach ($c_depended          as $c_id => $c_state) $c_depended_sys_items    ->child_insert(new markup('x-sticker', ['data-state' => $c_state ? 'ok' : ''       ], new text_simple(strtolower($c_id))), strtolower($c_id));
        $c_info = new markup('x-module-info');
        $c_switcher = new field_switcher();
        $c_switcher->attribute_insert('title', new text('Press to select module %%_title to be enabled or disabled', ['title' => $c_module->title]), 'element_attributes');
        $c_switcher->build();
        $c_switcher->name_set('is_enabled[]');
        $c_switcher->value_set($c_module->id);
        $c_switcher->checked_set (isset($enabled_by_boot[$c_module->id]));
        $c_switcher->disabled_set(isset($embed          [$c_module->id]) || !$c_is_ok_php_dependencies || !$c_is_ok_sys_dependencies || !$c_is_ok_sys_depended);
        $c_info->child_insert($c_switcher, 'switcher');
                                                                $c_info->child_insert(new markup('x-module-title',        [],              [new markup('x-label', ['aria-hidden' => 'true'], 'title'), new markup('x-value', [],                        $c_module->title              )]), 'title'           );
                                                                $c_info->child_insert(new markup('x-module-id',           [],              [new markup('x-label', [], 'id'                          ), new markup('x-value', [],        new text_simple($c_module->id)                )]), 'id'              );
                                                                $c_info->child_insert(new markup('x-module-version',      [],              [new markup('x-label', [], 'version'                     ), new markup('x-value', [], locale::format_version($c_module->version)           )]), 'version'         );
                                                                $c_info->child_insert(new markup('x-module-is-embed',     [],              [new markup('x-label', [], 'is embed'                    ), new markup('x-value', [],           isset($embed[$c_module->id]) ? 'yes' : 'no')]), 'is_embed'        );
        if ($c_module->id_bundle                              ) $c_info->child_insert(new markup('x-module-bundle_id',    [],              [new markup('x-label', [], 'bundle id'                   ), new markup('x-value', [],        new text_simple($c_module->id_bundle)         )]), 'bundle_id'       );
        if ($c_module->id_bundle                              ) $c_info->child_insert(new markup('x-module-bundle_build', [],              [new markup('x-label', [], 'bundle build number'         ), new markup('x-value', [],     module::bundle_get($c_module->id_bundle)->build  )]), 'bundle_build'    );
        if ($c_module->path                                   ) $c_info->child_insert(new markup('x-module-path',         [],              [new markup('x-label', [], 'path'                        ), new markup('x-value', [],                        $c_module->path               )]), 'path'            );
        if ($c_module->description                            ) $c_info->child_insert(new markup('x-module-description',  [],              [new markup('x-label', [], 'description'                 ), new markup('x-value', [],                        $c_module->description        )]), 'description'     );
        if ($c_module->copyright                              ) $c_info->child_insert(new markup('x-module-copyright',    [],              [new markup('x-label', [], 'copyright'                   ), new markup('x-value', [],                        $c_module->copyright          )]), 'copyright'       );
        if ($c_dependencies_php_items->children_select_count()) $c_info->child_insert(new markup('x-dependencies', ['data-type' => 'sys'], [new markup('x-label', [], 'depend from php extensions'  ), new markup('x-value', [],                        $c_dependencies_php_items     )]), 'dependencies_php');
        if ($c_dependencies_sys_items->children_select_count()) $c_info->child_insert(new markup('x-dependencies', ['data-type' => 'php'], [new markup('x-label', [], 'depend from modules'         ), new markup('x-value', [],                        $c_dependencies_sys_items     )]), 'dependencies_sys');
        if ($c_depended_sys_items    ->children_select_count()) $c_info->child_insert(new markup('x-dependencies', ['data-type' => 'use'], [new markup('x-label', [], 'used by modules'             ), new markup('x-value', [],                        $c_depended_sys_items         )]), 'depended_sys'    );
        $info->child_select($c_module->group_get_id())->child_insert($c_info, 'module_'.$c_module->id);
      }
    }
  }

  static function on_validate($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $enabled_by_boot = core::boot_select('enabled');
        $embed   = module::get_embed();
        $modules = module::get_all  ();
        $modules_to_enable  = [];
        $modules_to_disable = [];
        foreach ($modules as $c_module) {
          if (!isset($embed[$c_module->id])) {
            if ($items['#is_enabled:'.$c_module->id]->checked_get()          && isset($enabled_by_boot[$c_module->id]) == false) $modules_to_enable [$c_module->id] = $c_module;
            if ($items['#is_enabled:'.$c_module->id]->checked_get() == false && isset($enabled_by_boot[$c_module->id]))          $modules_to_disable[$c_module->id] = $c_module;
          }
        }
      # check dependencies
        if ($modules_to_enable) {
          foreach ($modules_to_enable as $c_module) {
            $c_dependencies = $c_module->dependencies->system ?? [];
            foreach ($c_dependencies as $c_dependency) {
              if (isset($modules_to_disable[$c_dependency])) {
                $items['#is_enabled:'.$c_dependency]->error_set();
                $items['#is_enabled:'.$c_module->id]->error_set(
                  'Can not enable module "%%_module_id_1" when you try to disable dependent module "%%_module_id_2"!', [
                  'module_id_1' => $c_module->id,
                  'module_id_2' => $c_dependency
                ]);
              }
            }
          }
        }
        break;
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $enabled_by_boot = core::boot_select('enabled');
        $embed   = module::get_embed();
        $modules = module::get_all  ();
        $modules_to_enable  = [];
        $modules_to_disable = [];
        $modules_to_install = [];
        $include_paths      = [];
      # collect information
        foreach ($modules as $c_module) {
          if (!isset($embed[$c_module->id])) {
            if ($items['#is_enabled:'.$c_module->id]->checked_get()          && isset($enabled_by_boot[$c_module->id]) == false) {$modules_to_enable [$c_module->id] = $c_module; $include_paths[$c_module->id] = $c_module->path;}
            if ($items['#is_enabled:'.$c_module->id]->checked_get() == false && isset($enabled_by_boot[$c_module->id]))          {$modules_to_disable[$c_module->id] = $c_module;                                                 }
          }
        }
      # enable modules
        if ($modules_to_enable) {
          cache::update_global($include_paths);
          foreach ($modules_to_enable as $c_module) {
            if (!module::is_installed($c_module->id)) {
              $modules_to_install[$c_module->id] = $c_module->id;
              event::start('on_module_install', $c_module->id);
            } event::start('on_module_enable',  $c_module->id);
          }
        }
      # disable modules
        if ($modules_to_disable) {
          foreach ($modules_to_disable as $c_module) {
            event::start('on_module_disable', $c_module->id);
          }
        }
      # update caches and this form
        cache::update_global();
        $form->child_select('info')->children_delete();
        static::on_init($form, $items);
      # show report
        $enabled_by_boot = core::boot_select('enabled');
        if ($modules_to_enable) {
          foreach ($modules_to_enable as $c_module) {
            if (isset($enabled_by_boot[$c_module->id])) {
              if (isset($modules_to_install[$c_module->id]))
                   message::insert(new text('Module "%%_title" (%%_id) has been installed.', ['title' => translation::get($c_module->title), 'id' => $c_module->id]));
              else message::insert(new text('Module "%%_title" (%%_id) has been enabled.',   ['title' => translation::get($c_module->title), 'id' => $c_module->id]));
            }
          }
        }
        if ($modules_to_disable) {
          foreach ($modules_to_disable as $c_module) {
            if (!isset($enabled_by_boot[$c_module->id])) {
              message::insert(
                new text('Module "%%_title" (%%_id) has been disabled.', ['title' => translation::get($c_module->title), 'id' => $c_module->id])
              );
            }
          }
        }
        if (!$modules_to_enable && !$modules_to_disable) {
          message::insert(
            'Nothing selected!', 'warning'
          );
        }
        break;
      case 'refresh':
      # update caches and this form
        cache::update_global();
        $form->child_select('info')->children_delete();
        static::on_init($form, $items);
      # show report
        message::insert(
          'All caches have been reset.'
        );
        break;
    }
  }

}}