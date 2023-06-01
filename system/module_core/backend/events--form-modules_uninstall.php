<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\cache;
use effcore\core;
use effcore\event;
use effcore\group_checkboxes;
use effcore\markup;
use effcore\module;

abstract class events_form_modules_uninstall {

    static function on_build($event, $form) {
        $installed = module::get_installed();
        $enabled   = module::get_enabled_by_boot();
        $embedded  = module::get_embedded();
        $modules   = module::get_all();
        $checkboxes = new group_checkboxes;
        $checkboxes->title = 'Modules';
        $checkboxes->title_is_visible = false;
        $checkboxes->description = 'The removing module should be disabled at first. Embedded modules cannot be disabled.';
        $checkboxes->element_attributes['name'] = 'uninstall[]';
        $checkboxes->required_any = true;
        $checkboxes_items = [];
        core::array_sort_by_string($modules);
        foreach ($modules as $c_module) {
            if  (!isset($embedded [$c_module->id]) &&
                  isset($installed[$c_module->id])) {
                if (isset($enabled  [$c_module->id]))
                    $checkboxes->disabled[$c_module->id] = $c_module->id;
                $checkboxes_items[$c_module->id] = $c_module->title;
            }
        }
        $checkboxes->items_set($checkboxes_items);
        if (count($checkboxes_items))
             $form->child_select('info')->child_insert($checkboxes, 'checkboxes');
        else $form->child_update('info', new markup('x-no-items', ['data-style' => 'table'], 'No modules.'));
    }

    static function on_init($event, $form, $items) {
        if (isset($items['*uninstall'])) {
            $items['~apply']->disabled_set(
                count($items['*uninstall']->items_get()) === count($items['*uninstall']->disabled)
            );
        }
    }

    static function on_validate($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                $embedded = module::get_embedded();
                $modules  = module::get_all     ();
                $modules_to_uninstall = [];
                $modules_to_include   = [];
                # collect information
                if (isset($items['*uninstall'])) {
                    foreach ($items['*uninstall']->value_get() as $c_module_id) {
                        $c_module = $modules[$c_module_id];
                        if (!isset($embedded[$c_module->id])) {
                            $modules_to_uninstall[$c_module->id] = $c_module;
                            $modules_to_include  [$c_module->id] = $c_module->path;
                        }
                    }
                }
                # uninstall modules
                if ($modules_to_uninstall) {
                    cache::update_global($modules_to_include);
                    foreach ($modules_to_uninstall as $c_module) {
                        event::start('on_module_uninstall', $c_module->id);
                    }
                }
                # update caches and this form
                cache::update_global();
                $form->child_select('info')->children_delete();
                static::on_build(null, $form);
                static::on_init (null, $form, $form->items_update());
                break;
        }
    }

}
