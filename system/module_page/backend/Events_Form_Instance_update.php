<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Access;
use effcore\Area;
use effcore\Core;
use effcore\Entity;
use effcore\Form_part;
use effcore\Layout;
use effcore\Markup;
use effcore\Text;
use effcore\Widget_Blocks;

abstract class Events_Form_Instance_update {

    static function on_build($event, $form) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'page') {
                $form->child_select('fields')->child_insert(
                    Form_part::get('form_instance_update__page_width'), 'page_width'
                );
                # layout and its blocks
                $layout = Core::deep_clone(Layout::select($form->_instance->id_layout));
                if ($layout) {
                    foreach ($layout->children_select_recursive() as $c_area) {
                        if ($c_area instanceof Area) {
                            $c_area->managing_enable();
                            $c_area->build();
                            if ($c_area->id) {
                                $c_widget_blocks = new Widget_Blocks($c_area->id);
                                $c_widget_blocks->cform = $form;
                                $c_widget_blocks->name_complex = 'widget_blocks__'.$c_area->id;
                                $c_widget_blocks->build();
                                $c_widget_blocks->value_set($form->_instance->blocks[$c_area->id] ?? null, ['once' => true]);
                                $c_area->child_insert($c_widget_blocks, 'widget_blocks');
                                $form->_widgets_area[$c_area->id] = $c_widget_blocks;
                            }
                        }
                    }
                    $form->child_select('fields')->child_insert(
                        new Markup('x-layout-manager', ['data-id' => $layout->id], ['manager' => $layout], -500), 'layout_manager'
                    );
                } else {
                    $form->child_select('fields')->child_insert(
                        new Markup('x-form-message', [], ['message' => new Text(
                            'LOST LAYOUT: %%_id', ['id' => $form->_instance->id_layout ?: 'n/a'])
                        ], -500), 'message_lost_layout'
                    );
                }
            }
        }
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'page') {
                # disable field 'url' for embedded instance
                if (!empty($form->_instance->is_embedded)) {
                    $items['#url']->disabled_set(true);
                }
                # field 'min width' + field 'max width'
                $items['#width_min']->value_set($form->_instance->data['width_min'] ?? 0);
                $items['#width_max']->value_set($form->_instance->data['width_max'] ?? 0);
                # meta
                if (!Access::check((object)['roles'       => ['admins'      => 'admins'     ],
                                            'permissions' => ['manage__seo' => 'manage__seo']])) {
                    $items['#meta']->disabled_set(true);
                    $items['#is_use_global_meta']->disabled_set(true);
                }
            }
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'update':
                if ($entity->name === 'page') {
                    if ($items['#width_min']->value_get() >
                        $items['#width_max']->value_get()) {
                        $items['#width_min']->error_set();
                        $items['#width_max']->error_set();
                        $form->error_set('The minimum value cannot be greater than the maximum!');
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'update':
                if ($entity->name === 'page') {
                    # field 'min width' + field 'max width'
                    $data = $form->_instance->data;
                    $data['width_min'] = $items['#width_min']->value_get();
                    $data['width_max'] = $items['#width_max']->value_get();
                    $form->_instance->data = $data;
                    # save layout blocks
                    if (Layout::select($form->_instance->id_layout)) {
                        $all_blocks = [];
                        foreach ($form->_widgets_area as $c_id_area => $c_widget) {
                            $c_blocks_by_area = $c_widget->value_get();
                            if ($c_blocks_by_area) {
                                $all_blocks[$c_id_area] = $c_blocks_by_area;
                            }
                        }
                        if (count($all_blocks))
                             $form->_instance->blocks = $all_blocks;
                        else $form->_instance->blocks = null;
                    }
                }
                break;
        }
    }

}
