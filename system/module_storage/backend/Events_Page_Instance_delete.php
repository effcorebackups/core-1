<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use const effcore\BR;
use effcore\Access;
use effcore\Entity;
use effcore\Instance;
use effcore\Response;
use effcore\Text_multiline;

abstract class Events_Page_Instance_delete {

    static function on_check_existence($event, $page) {
        $managing_group_id = $page->args_get('managing_group_id');
        $entity_name       = $page->args_get('entity_name');
        $instance_id       = $page->args_get('instance_id');
        $entity = Entity::get($entity_name);
        $groups = Entity::get_managing_group_ids();
        if ($managing_group_id === null || isset($groups[$managing_group_id])) {
            if ($entity) {
                if ($entity->managing_is_enabled) {
                    $id_keys   = $entity->id_get();
                    $id_values = explode('+', $instance_id);
                    if (count($id_keys) ===
                        count($id_values)) {
                        $conditions = array_combine($id_keys, $id_values);
                        $instance = new Instance($entity_name, $conditions);
                        if ($instance->select()) {
                            return true;
                        } else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong instance key',                          'go to <a href="/">front page</a>'], [], BR.BR));
                    }     else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong number of instance keys',               'go to <a href="/">front page</a>'], [], BR.BR));
                }         else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['management for this entity is not available', 'go to <a href="/">front page</a>'], [], BR.BR));
            }             else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong entity name',                           'go to <a href="/">front page</a>'], [], BR.BR));
        }                 else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong management group',                      'go to <a href="/">front page</a>'], [], BR.BR));
    }

    static function on_check_access($event, $page) {
        $entity_name = $page->args_get('entity_name');
        $instance_id = $page->args_get('instance_id');
        $entity = Entity::get($entity_name);
        if (!Access::check($entity->access_delete)) {
            Response::send_header_and_exit('access_forbidden');
        }
        $id_keys = $entity->id_get();
        $id_values = explode('+', $instance_id);
        $conditions = array_combine($id_keys, $id_values);
        $instance = new Instance($entity_name, $conditions);
        if ($instance->select() && !empty($instance->is_embedded)) {
            Response::send_header_and_exit('access_forbidden', null, new Text_multiline(['entity is embedded', 'go to <a href="/">front page</a>'], [], BR.BR));
        }
    }

}
