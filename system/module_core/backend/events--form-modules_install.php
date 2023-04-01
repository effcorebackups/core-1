<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\cache;
          use \effcore\core;
          use \effcore\event;
          use \effcore\field_switcher;
          use \effcore\fieldset;
          use \effcore\locale;
          use \effcore\markup_simple;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\node;
          use \effcore\text_simple;
          use \effcore\text;
          use \effcore\url;
          abstract class events_form_modules_install {

  static function on_init($event, $form, $items) {
    $info = $form->child_select('info');
    $enabled  = module::get_enabled_by_boot();
    $embedded = module::get_embedded();
    $modules  = module::get_all();
    $groups   = module::groups_get();
    $modules_by_groups = [];
    core::array_sort($groups);
    foreach ($groups as $c_group_id => $c_group_title) {
      $c_fieldset = new fieldset($c_group_title);
      $c_fieldset->state = 'closed';
      $info->child_insert($c_fieldset, $c_group_id);
      foreach ($modules as $c_module)
        if ($c_group_id === $c_module->group_get_id())
          $modules_by_groups[$c_group_id][$c_module->id] = $c_module;
      core::array_sort_by_string(
        $modules_by_groups[$c_group_id]
      );
    }
    foreach ($modules_by_groups as $c_modules) {
      foreach ($c_modules as $c_module) {
        $c_required_for_info      = $c_module->required_for_info_get('boot');
        $c_dependencies_info      = $c_module->dependencies_info_get('boot');
        $c_required_for_sys_items = new node;
        $c_dependencies_sys_items = new node;
        $c_dependencies_php_items = new node;
        foreach ($c_required_for_info->req as $c_id => $c_info) $c_required_for_sys_items->child_insert(new markup('x-sticker', ['data-style' => $c_info->state === 1                       ? 'ok'      : null], [new markup('x-title', [], new text_simple(strtoupper($c_id)))]                                                                           ), strtolower($c_id));
        foreach ($c_dependencies_info->sys as $c_id => $c_info) $c_dependencies_sys_items->child_insert(new markup('x-sticker', ['data-style' => $c_dependencies_info->has_dependencies_sys ? 'warning' : null], [new markup('x-title', [], new text_simple(strtoupper($c_id))), new markup('x-version', [], locale::format_version($c_info->version_min))]), strtolower($c_id));
        foreach ($c_dependencies_info->php as $c_id => $c_info) $c_dependencies_php_items->child_insert(new markup('x-sticker', ['data-style' => $c_dependencies_info->has_dependencies_php ? 'warning' : null], [new markup('x-title', [], new text_simple(strtoupper($c_id))), new markup('x-version', [],                        $c_info->version_min )]), strtolower($c_id));
        $c_info = new markup('x-module-info', ['data-id' => $c_module->id]);
        $c_switcher = new field_switcher;
        $c_switcher->attribute_insert('title', new text('press to enable or disable the module "%%_title"', ['title' => $c_module->title]), 'element_attributes');
        $c_switcher->build();
        $c_switcher->name_set('is_enabled[]');
        $c_switcher->value_set($c_module->id);
        $c_switcher->checked_set (isset($enabled [$c_module->id]));
        $c_switcher->disabled_set(isset($embedded[$c_module->id])            ||
                                  $c_required_for_info->has_required         ||
                                  $c_dependencies_info->has_dependencies_php ||
                                  $c_dependencies_info->has_dependencies_sys);
        $c_info->child_insert($c_switcher, 'switcher');
        if ($c_module->icon_path                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'icon'              ], [new markup('x-title', ['aria-hidden' => 'true'], 'icon'                      ), new markup('x-value', [], new markup_simple('img', ['src' => $c_module->icon_path[0] === '/' ? $c_module->icon_path : '/'.$c_module->path.$c_module->icon_path, 'alt' => 'icon']) )]), 'icon'              );
        if (true                                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'title'             ], [new markup('x-title', ['aria-hidden' => 'true'], 'title'                     ), new markup('x-value', [],                                    $c_module->title                                                                                                     )]), 'title'             );
        if (true                                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'id'                ], [new markup('x-title', [                       ], 'id'                        ), new markup('x-value', [],                    new text_simple($c_module->id)                                                                                                       )]), 'id'                );
        if (true                                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'version'           ], [new markup('x-title', [                       ], 'version'                   ), new markup('x-value', [],             locale::format_version($c_module->version)                                                                                                  )]), 'version'           );
        if (true                                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'is-embedded'       ], [new markup('x-title', [                       ], 'is embedded'               ), new markup('x-value', [],                    isset($embedded[$c_module->id]) ? 'yes' : 'no'                                                                                       )]), 'is_embedded'       );
        if ($c_module->description                            ) $c_info->child_insert(new markup('x-param', ['data-type' => 'description'       ], [new markup('x-title', [                       ], 'description'               ), new markup('x-value', [],                                    $c_module->description                                                                                               )]), 'description'       );
        if ($c_module->copyright                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'copyright'         ], [new markup('x-title', [                       ], 'copyright'                 ), new markup('x-value', [],                                    $c_module->copyright                                                                                                 )]), 'copyright'         );
        if ($c_module->path                                   ) $c_info->child_insert(new markup('x-param', ['data-type' => 'path'              ], [new markup('x-title', [                       ], 'path'                      ), new markup('x-value', [],                                    $c_module->path                                                                                                      )]), 'path'              );
        if ($c_module->id_bundle                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'bundle-id'         ], [new markup('x-title', [                       ], 'bundle id'                 ), new markup('x-value', [],                    new text_simple($c_module->id_bundle)                                                                                                )]), 'bundle_id'         );
        if ($c_module->id_bundle                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'bundle-build'      ], [new markup('x-title', [                       ], 'bundle build number'       ), new markup('x-value', [],                 module::bundle_get($c_module->id_bundle)->build                                                                                         )]), 'bundle_build'      );
        if ($c_module->id_bundle                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'bundle-title'      ], [new markup('x-title', [                       ], 'bundle title'              ), new markup('x-value', [],                 module::bundle_get($c_module->id_bundle)->title                                                                                         )]), 'bundle_title'      );
        if ($c_module->id_bundle                              ) $c_info->child_insert(new markup('x-param', ['data-type' => 'bundle-description'], [new markup('x-title', [                       ], 'bundle description'        ), new markup('x-value', [],                 module::bundle_get($c_module->id_bundle)->description                                                                                   )]), 'bundle_description');
        if ($c_dependencies_php_items->children_select_count()) $c_info->child_insert(new markup('x-param', ['data-type' => 'dependencies-sys'  ], [new markup('x-title', [                       ], 'depend from PHP extensions'), new markup('x-value', [],                                    $c_dependencies_php_items                                                                                            )]), 'dependencies_php'  );
        if ($c_dependencies_sys_items->children_select_count()) $c_info->child_insert(new markup('x-param', ['data-type' => 'dependencies-php'  ], [new markup('x-title', [                       ], 'depend from modules'       ), new markup('x-value', [],                                    $c_dependencies_sys_items                                                                                            )]), 'dependencies_sys'  );
        if ($c_required_for_sys_items->children_select_count()) $c_info->child_insert(new markup('x-param', ['data-type' => 'required-for-sys'  ], [new markup('x-title', [                       ], 'required for modules'      ), new markup('x-value', [],                                    $c_required_for_sys_items                                                                                            )]), 'required_for_sys'  );
        if (isset($c_module->urls) && is_array($c_module->urls))
          foreach ($c_module->urls as $c_title => $c_url)
            if (isset($enabled[$c_module->id]))
              $c_info->child_insert(new markup('x-param', ['data-type' => 'url'], [
                new markup('x-title', [], $c_title),
                new markup('x-value', [], new markup('a', ['href' => $c_url], url::url_to_markup($c_url)))
              ]), 'url_'.core::sanitize_id($c_title, '_'));
        $info->child_select($c_module->group_get_id())->child_insert($c_info, 'module_'.$c_module->id);
      }
    }
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $enabled  = module::get_enabled_by_boot();
        $embedded = module::get_embedded();
        $modules  = module::get_all();
        $modules_to_enable  = [];
        $modules_to_disable = [];
        foreach ($modules as $c_module) {
          if (!isset($embedded[$c_module->id])) {
            if ($items['#is_enabled:'.$c_module->id]->checked_get() !== false && isset($enabled[$c_module->id]) === false) $modules_to_enable [$c_module->id] = $c_module;
            if ($items['#is_enabled:'.$c_module->id]->checked_get() === false && isset($enabled[$c_module->id]) !== false) $modules_to_disable[$c_module->id] = $c_module;
          }
        }
      # check dependencies
        if ($modules_to_enable) {
          foreach ($modules_to_enable as $c_module) {
            $c_dependencies = $c_module->dependencies->system ?? [];
            foreach ($c_dependencies as $c_id => $c_version_min) {
              if (isset($modules_to_disable[$c_id])) {
                $items['#is_enabled:'.$c_id        ]->error_set();
                $items['#is_enabled:'.$c_module->id]->error_set(
                  'Module "%%_title" (%%_id) cannot be enabled when you try to disable dependent module "%%_dependency_title" (%%_dependency_id)!', [
                  'title'            => $c_module->title,
                  'id'               => $c_module->id,
                  'dependency_title' => module::get($c_id)->title,
                  'dependency_id'    => module::get($c_id)->id
                ]);
              }
            }
          }
        }
      # if no one item is selected
        if (!$modules_to_enable && !$modules_to_disable) {
          message::insert(
            'No one item was selected!', 'warning'
          );
        }
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $enabled  = module::get_enabled_by_boot();
        $embedded = module::get_embedded();
        $modules  = module::get_all();
        $modules_to_enable  = [];
        $modules_to_disable = [];
        $modules_to_install = [];
        $modules_to_include = [];
      # collect information
        core::array_sort_by_number($modules, 'deploy_weight');
        foreach ($modules as $c_module) {
          if (!isset($embedded[$c_module->id])) {
            if ($items['#is_enabled:'.$c_module->id]->checked_get() !== false && isset($enabled[$c_module->id]) === false) {$modules_to_enable [$c_module->id] = $c_module; $modules_to_include[$c_module->id] = $c_module->path;}
            if ($items['#is_enabled:'.$c_module->id]->checked_get() === false && isset($enabled[$c_module->id]) !== false) {$modules_to_disable[$c_module->id] = $c_module;                                                      }
          }
        }
      # enable modules
        if ($modules_to_enable) {
          cache::update_global($modules_to_include);
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
        static::on_init(null, $form, $items);
        break;
    }
  }

}}