<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Cache;
use effcore\Core;
use effcore\Event;
use effcore\Group_Checkboxes;
use effcore\Markup;
use effcore\Module;

abstract class Events_Form_Modules_Uninstall {

    static function on_build($event, $form) {
        $installed = Module::get_installed();
        $enabled   = Module::get_enabled_by_boot();
        $embedded  = Module::get_embedded();
        $modules   = Module::get_all();
        $checkboxes = new Group_Checkboxes;
        $checkboxes->title = 'Modules';
        $checkboxes->title_is_visible = false;
        $checkboxes->description = 'The removing module should be disabled at first. Embedded modules cannot be disabled.';
        $checkboxes->element_attributes['name'] = 'uninstall[]';
        $checkboxes->required_any = true;
        $checkboxes_items = [];
        Core::array_sort_by_string($modules);
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
        else $form->child_update('info', new Markup('x-no-items', ['data-style' => 'table'], 'No modules.'));
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
                $embedded = Module::get_embedded();
                $modules  = Module::get_all     ();
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
                    Cache::update_global($modules_to_include);
                    foreach ($modules_to_uninstall as $c_module) {
                        Event::start('on_module_uninstall', $c_module->id);
                    }
                }
                # update caches and this form
                Cache::update_global();
                $form->child_select('info')->children_delete();
                static::on_build(null, $form);
                static::on_init (null, $form, $form->items_update());
                break;
        }
    }

}
