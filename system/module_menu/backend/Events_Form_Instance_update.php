<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\Entity;
use effcore\Tree_item;
use effcore\Url;

abstract class Events_Form_Instance_update {

    static function on_build($event, $form) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'tree_item') {
                # field 'parent'
                $tree_item = Tree_item::select(
                    $form->_instance->id,
                    $form->_instance->id_tree);
                $tree_item->build();
                foreach ($tree_item->children_select_recursive() as $c_child) $form->child_select('fields')->child_select('id_parent')->disabled[$c_child->id] = $c_child->id;
                $form->child_select('fields')->child_select('id_parent')->disabled[$form->_instance->id] = $form->_instance->id;
                $form->child_select('fields')->child_select('id_parent')->query_settings['conditions'] = [
                    'id_tree_!f'       => 'id_tree',
                    'id_tree_operator' => '=',
                    'id_tree_!v'       => $form->_instance->id_tree
                ];
            }
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'update':
            case 'cancel':
                if ($entity->name === 'tree_item') {
                    if (!Url::back_url_get())
                         Url::back_url_set('back', $entity->make_url_for_select_multiple().'///'.$form->_instance->id_tree);
                }
                break;
        }
    }

}
