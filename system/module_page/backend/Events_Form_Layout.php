<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Area_group;
use effcore\Area;
use effcore\Core;
use effcore\Layout;
use effcore\Markup;
use effcore\Message;
use effcore\Page;
use effcore\Request;

abstract class Events_Form_Layout {

    static function on_build($event, $form) {
        $layout_id = Page::get_current()->args_get('layout_id');
        if (Layout::select($layout_id)) {
            $form->_layout = Core::deep_clone(Layout::select($layout_id));
            foreach ($form->_layout->children_select_recursive() as $c_area) {
                if ($c_area instanceof Area ||
                    $c_area instanceof Area_group) {
                    $c_area->manage_mode_enable('customization');
                    $c_area->states_set(
                        $c_area->id &&
                        isset($form->_layout->states[$c_area->id]) ?
                              $form->_layout->states[$c_area->id] : []);
                    $c_area->build();
                }
            }
            $form->child_select('data')->child_delete('layout_manager');
            $form->child_select('data')->child_insert(
                new Markup('x-layout-manager', [
                    'data-layout-id' => $form->_layout->id], [
                    'manager'        => $form->_layout]), 'layout_manager'
            );
        }
    }

    static function on_init($event, $form, $items) {
        if (isset($form->_layout) && $form->_layout->is_manageable) {
            $items['~save' ]->disabled_set(false);
            $items['~reset']->disabled_set(false);
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $states = [];
                foreach ($form->_layout->children_select_recursive() as $c_area) {
                    if ($c_area instanceof Area ||
                        $c_area instanceof Area_group) {
                        if ($c_area->id) {
                            $c_states = $c_area->states_get();
                            if ($c_states) {
                                $states[$c_area->id] = $c_states;
                            }
                        }
                    }
                }
                $result = Layout::changes_store(
                    $form->_layout->module_id,
                    $form->_layout->id,
                    $states
                );
                if ($result)
                     Message::insert('Changes was saved.'             );
                else Message::insert('Changes was not saved!', 'error');
                break;
            case 'reset':
                $states = [];
                foreach ($form->_layout->children_select_recursive() as $c_area) {
                    if ($c_area instanceof Area ||
                        $c_area instanceof Area_group) {
                        if ($c_area->id) {
                            $c_states = $c_area->states_get();
                            if ($c_states) {
                                $states[$c_area->id] = null;
                            }
                        }
                    }
                }
                $result = Layout::changes_store(
                    $form->_layout->module_id,
                    $form->_layout->id,
                    $states
                );
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                Layout::cache_cleaning();
                Request::values_reset('_POST');
                $form->components_build();
                $form->components_init();
                break;
        }
    }

}
