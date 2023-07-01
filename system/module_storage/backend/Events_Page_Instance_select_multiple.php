<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use effcore\Access;
use effcore\Entity;
use effcore\Response;
use effcore\Tabs;
use effcore\Url;

abstract class Events_Page_Instance_select_multiple {

    # ──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────
    # URLs variants:
    # ══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════
    # multiple select: / manage / data
    # multiple select: / manage / data / %%_managing_group_id / %%_entity_name
    # multiple select: / manage / data / %%_managing_group_id / %%_entity_name / …………………………………… / ……………………………………………………… / %%_category_id
    #          insert: / manage / data / %%_managing_group_id / %%_entity_name / …………………………………… / %%_action_name=insert / %%_category_id
    #          insert: / manage / data / %%_managing_group_id / %%_entity_name / …………………………………… / %%_action_name=insert
    #          select: / manage / data / %%_managing_group_id / %%_entity_name / %%_instance_id
    #          update: / manage / data / %%_managing_group_id / %%_entity_name / %%_instance_id / %%_action_name=update
    #          delete: / manage / data / %%_managing_group_id / %%_entity_name / %%_instance_id / %%_action_name=delete
    # ──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────

    static function on_redirect($event, $page) {
        $managing_group_id  = $page->args_get('managing_group_id');
        $entity_name        = $page->args_get('entity_name');
        $entities           = Entity::get_all();
        $entities_by_groups = [];
        # collect manageable entities
        foreach ($entities as $c_entity) {
            if ($c_entity->managing_is_enabled) {
                if (Access::check($c_entity->access_select)) {
                    $entities_by_groups[$c_entity->managing_group_id]
                                       [$c_entity->name] = $c_entity->title_plural;
                }
            }
        }
        # redirect if required or send 'access_forbidden'
        if (count($entities_by_groups)) {
            if (empty($entities_by_groups[$managing_group_id][$entity_name])) {
                $first_branch = Tabs::select('data')->get_first_branch();
                if (count($first_branch)) Url::go($page->args_get('base').'/'.end($first_branch)->action_name);
                else Response::send_header_and_exit('access_forbidden');
        }}      else Response::send_header_and_exit('access_forbidden');
    }

    static function on_check_access($event, $page) {
        $entity_name = $page->args_get('entity_name');
        $entity = Entity::get($entity_name);
        if (!Access::check($entity->access_select)) {
            Response::send_header_and_exit('access_forbidden');
        }
    }

}
