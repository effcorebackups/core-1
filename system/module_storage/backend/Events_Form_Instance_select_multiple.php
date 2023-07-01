<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use effcore\Access;
use effcore\Actions_list;
use effcore\Core;
use effcore\Entity;
use effcore\Markup;
use effcore\Message;
use effcore\Page;
use effcore\Selection;
use effcore\Text;
use effcore\Url;
use stdClass;

abstract class Events_Form_Instance_select_multiple {

    static function on_build($event, $form) {
        Page::get_current()->args_set('action_name', 'select_multiple');
        if (!$form->managing_group_id) $form->managing_group_id = Page::get_current()->args_get('managing_group_id');
        if (!$form->entity_name      ) $form->entity_name       = Page::get_current()->args_get('entity_name');
        $entity = Entity::get($form->entity_name);
        $groups = Entity::get_managing_group_ids();
        if (isset($groups[$form->managing_group_id])) {
            if ($entity) {
                $form->attribute_insert('data-entity_name', $form->entity_name);
                $form->_has_access_select = (bool)Access::check($entity->access_select);
                $form->_has_access_insert = (bool)Access::check($entity->access_insert);
                $form->_has_access_update = (bool)Access::check($entity->access_update);
                $form->_has_access_delete = (bool)Access::check($entity->access_delete);
                # list of items
                $selection = Selection::get('instance_select_multiple-'.$entity->name);
                if ($selection) {
                    $selection = Core::deep_clone($selection);
                    $selection->fields['checkboxes']['checkbox-select'] = new stdClass;
                    $selection->fields['checkboxes']['checkbox-select']->weight = 500;
                    $selection->fields['checkboxes']['checkbox-select']->settings = ['name' => 'is_checked[]'];
                    $selection->fields['code']['actions'] = new stdClass;
                    $selection->fields['code']['actions']->weight = -500;
                    $selection->fields['code']['actions']->closure = function ($c_row_id, $c_row, $c_instance, $settings = []) use ($form) {
                        $c_actions_list = new Actions_list;
                        if ($form->_has_access_delete && empty($c_instance->is_embedded)) $c_actions_list->action_insert($c_instance->make_url_for_delete().'?'.Url::back_part_make(), 'delete');
                        if ($form->_has_access_select                                   ) $c_actions_list->action_insert($c_instance->make_url_for_select().'?'.Url::back_part_make(), 'select');
                        if ($form->_has_access_update                                   ) $c_actions_list->action_insert($c_instance->make_url_for_update().'?'.Url::back_part_make(), 'update');
                        return $c_actions_list;
                    };
                    $selection->build();
                    $form->_selection = $selection;
                    $form->child_select('data')->child_insert($selection, 'selection');
                    if (count($selection->_instances) === 0) {
                        $form->has_no_items = true;
                    }
                    if ($form->_has_access_delete) {
                        unset($form->child_select('actions')->disabled['delete']);
                    }
                } else {$form->child_select('data')->child_insert(new Markup('x-no-items', ['data-style' => 'table'], new Text('No Selection with ID = "%%_id".', ['id' => 'instance_select_multiple-'.$entity->name])), 'message_no_items'); $form->has_error_on_build = true;}
            }     else {$form->child_select('data')->child_insert(new Markup('x-no-items', ['data-style' => 'table'], 'wrong entity name'     ),                                                                         'message_error'   ); $form->has_error_on_build = true;}
        }         else {$form->child_select('data')->child_insert(new Markup('x-no-items', ['data-style' => 'table'], 'wrong management group'),                                                                         'message_error'   ); $form->has_error_on_build = true;}
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false) {
            $items['~insert' ]->disabled_set($form->_has_access_insert === false);
            $items['#actions']->disabled_set($form->has_no_items);
            $items['~apply'  ]->disabled_set($form->has_no_items);
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                if (!$items['#actions']->disabled_get()) {
                    $form->_selected_instances = [];
                    foreach ($form->_selection->_instances as $c_instance) {
                        $c_instance_id = implode('+', $c_instance->values_id_get());
                        if (isset($items['#is_checked:'.$c_instance_id]) &&
                                  $items['#is_checked:'.$c_instance_id]->checked_get()) {
                            $form->_selected_instances[$c_instance_id] = $c_instance;
                        }
                    }
                    if ($form->_selected_instances === []) {
                        Message::insert('No one item was selected!', 'warning');
                        foreach ($form->_selection->_instances as $c_instance) {
                            $c_instance_id = implode('+', $c_instance->values_id_get());
                            if (isset($items['#is_checked:'.$c_instance_id]))
                                      $items['#is_checked:'.$c_instance_id]->error_set();
                        }
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                if (!empty($form->_selected_instances)) {
                    foreach ($form->_selected_instances as $c_instance_id => $c_instance) {
                        if ($items['#actions']->value_get() === 'delete') {
                            if (empty($c_instance->is_embedded)) {
                                $c_result = $c_instance->delete();
                                if ($form->is_show_result_message && $c_result !== null) Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was deleted.',                            ['type' => (new Text($entity->title))->render(), 'id' => $c_instance_id])           );
                                if ($form->is_show_result_message && $c_result === null) Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was not deleted!',                        ['type' => (new Text($entity->title))->render(), 'id' => $c_instance_id]), 'error'  );
                            } else                                                       Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was not deleted because it is embedded!', ['type' => (new Text($entity->title))->render(), 'id' => $c_instance_id]), 'warning');
                        }
                    }
                }
                static::on_build(null, $form);
                static::on_init (null, $form, $items);
                break;
            case 'insert':
                Url::go($entity->make_url_for_insert().'?'.Url::back_part_make());
                break;
        }
    }

}
