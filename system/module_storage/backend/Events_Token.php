<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\storage;

use effcore\Entity;
use effcore\Page;
use effcore\Text;

abstract class Events_Token {

    static function on_apply($name, $args = []) {
        switch ($name) {
            case 'instance_id_context'        : if (Page::get_current()) {return         Page::get_current()->args_get('instance_id');} break;
            case 'entity_name_context'        : if (Page::get_current()) {return         Page::get_current()->args_get('entity_name');} break;
            case 'entity_title_context'       : if (Page::get_current()) {$entity_name = Page::get_current()->args_get('entity_name'); if ($entity_name) {$entity = Entity::get($entity_name, false); if ($entity) return (new Text($entity->title       ))->render();}} break;
            case 'entity_title_plural_context': if (Page::get_current()) {$entity_name = Page::get_current()->args_get('entity_name'); if ($entity_name) {$entity = Entity::get($entity_name, false); if ($entity) return (new Text($entity->title_plural))->render();}} break;
        }
    }

}
