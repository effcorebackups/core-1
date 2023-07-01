<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use const effcore\BR;
use effcore\Access;
use effcore\Actions_list;
use effcore\Core;
use effcore\Entity;
use effcore\Instance;
use effcore\Markup;
use effcore\Response;
use effcore\Selection;
use effcore\Text_multiline;
use effcore\Text;
use effcore\Token;
use effcore\Url;
use stdClass;

abstract class Events_Page_Instance_select {

    static function on_redirect_and_check_existence($event, $page) {
        $managing_group_id = $page->args_get('managing_group_id');
        $entity_name       = $page->args_get('entity_name');
        $instance_id       = $page->args_get('instance_id');
        $entity = Entity::get($entity_name);
        $groups = Entity::get_managing_group_ids();
        if (isset($groups[$managing_group_id])) {
            if ($entity) {
                if ($entity->managing_is_enabled) {
                    $id_keys   = $entity->id_get();
                    $id_values = explode('+', $instance_id);
                    if (count($id_keys) ===
                        count($id_values)) {
                        $conditions = array_combine($id_keys, $id_values);
                        $instance = new Instance($entity_name, $conditions);
                        if ($instance->select() === null && Url::back_url_get() !== '') Url::go(Url::back_url_get()); # after deletion
                        if ($instance->select() === null && Url::back_url_get() === '')
                           Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong instance key',                          'go to <a href="/">front page</a>'], [], BR.BR));
                    } else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong number of instance keys',               'go to <a href="/">front page</a>'], [], BR.BR));
                }     else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['management for this entity is not available', 'go to <a href="/">front page</a>'], [], BR.BR));
            }         else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong entity name',                           'go to <a href="/">front page</a>'], [], BR.BR));
        }             else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong management group',                      'go to <a href="/">front page</a>'], [], BR.BR));
    }

    static function on_check_access($event, $page) {
        $entity_name = $page->args_get('entity_name');
        $entity = Entity::get($entity_name);
        if (!Access::check($entity->access_select)) {
            Response::send_header_and_exit('access_forbidden');
        }
    }

    static function block_markup__instance_select($page, $args = []) {
        $page->args_set('action_name', 'select');
        $entity_name = $page->args_get('entity_name');
        $instance_id = $page->args_get('instance_id');
        $entity = Entity::get($entity_name);
        if ($entity) {
            $id_keys   = $entity->id_get();
            $id_values = explode('+', $instance_id);
            if (count($id_keys) ===
                count($id_values)) {
                $conditions = array_combine($id_keys, $id_values);
                $instance = new Instance($entity_name, $conditions);
                if ($instance->select()) {
                    $selection = Selection::get('instance_select-'.$entity->name);
                    if ($selection) {
                        foreach ($conditions as $c_id_key => $c_id_value)
                            Token::insert('selection_'.$entity_name.'_'.$c_id_key.'_context', 'text', $c_id_value, null, 'storage');
                        $selection = Core::deep_clone($selection);
                        $has_access_update = Access::check($entity->access_update);
                        $has_access_delete = Access::check($entity->access_delete);
                        if ($has_access_update ||
                            $has_access_delete) {
                            $selection->fields['code']['actions'] = new stdClass;
                            $selection->fields['code']['actions']->title = 'Actions';
                            $selection->fields['code']['actions']->weight = -500;
                            $selection->fields['code']['actions']->closure = function ($c_row_id, $c_row, $c_instance, $settings = []) use ($has_access_update, $has_access_delete) {
                                $c_actions_list = new Actions_list;
                                if ($has_access_delete && empty($c_instance->is_embedded)) $c_actions_list->action_insert($c_instance->make_url_for_delete().'?'.Url::back_part_make(), 'delete');
                                if ($has_access_update                                   ) $c_actions_list->action_insert($c_instance->make_url_for_update().'?'.Url::back_part_make(), 'update');
                                return $c_actions_list;
                            };
                        }
                        $selection->build();
                        return $selection;
                    } else {
                        return new Markup('x-no-items', ['data-style' => 'table'], new Text(
                            'No Selection with ID = "%%_id".', ['id' => 'instance_select-'.$entity->name]
                        ));
                    }
                }
            }
        }
    }

}
