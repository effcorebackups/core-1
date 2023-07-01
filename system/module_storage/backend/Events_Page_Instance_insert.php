<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use const effcore\BR;
use effcore\Access;
use effcore\Entity;
use effcore\Response;
use effcore\Text_multiline;

abstract class Events_Page_Instance_insert {

    static function on_check_existence($event, $page) {
        $managing_group_id = $page->args_get('managing_group_id');
        $entity_name       = $page->args_get('entity_name');
        $entity = Entity::get($entity_name);
        $groups = Entity::get_managing_group_ids();
        if ($managing_group_id === null || isset($groups[$managing_group_id])) {
            if ($entity) {
                if ($entity->managing_is_enabled) {
                    return true;
                } else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['management for this entity is not available', 'go to <a href="/">front page</a>'], [], BR.BR));
            }     else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong entity name',                           'go to <a href="/">front page</a>'], [], BR.BR));
        }         else Response::send_header_and_exit('page_not_found', null, new Text_multiline(['wrong management group',                      'go to <a href="/">front page</a>'], [], BR.BR));
    }

    static function on_check_access($event, $page) {
        $entity_name = $page->args_get('entity_name');
        $entity = Entity::get($entity_name);
        if (!Access::check($entity->access_insert)) {
            Response::send_header_and_exit('access_forbidden');
        }
    }

}
