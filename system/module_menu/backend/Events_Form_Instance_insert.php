<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\Entity;
use effcore\Page;
use effcore\Text_multiline;
use effcore\Text;
use effcore\Tree_item;
use effcore\Tree;
use effcore\Url;

abstract class Events_Form_Instance_insert {

    static function on_build($event, $form) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'tree_item') {
                if (!$form->category_id)
                     $form->category_id = Page::get_current()->args_get('category_id');
                # field 'parent'
                $form->child_select('fields')->child_select('id_parent')->query_settings['conditions'] = [
                    'id_tree_!f'       => 'id_tree',
                    'id_tree_operator' => '=',
                    'id_tree_!v'       => $form->category_id
                ];
            }
        }
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'tree_item') {
                # field 'id_tree'
                $items['#id_tree']->value_set($form->category_id);
            }
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                if ($entity->name === 'tree') {
                    # field 'id' (check the uniqueness of SQL + NoSQL data)
                    if ($items['#id']->value_get()) {
                        if (Tree::select($items['#id']->value_get())) {
                            $items['#id']->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Previously used value was specified.',
                                'Only unique value is allowed.'], ['title' => (new Text($items['#id']->title))->render() ]
                            ));
                        }
                    }
                }
                if ($entity->name === 'tree_item') {
                    # field 'id' (check the uniqueness of SQL + NoSQL data)
                    if ($items['#id']->value_get()) {
                        if (Tree_item::select($items['#id']->value_get(), null)) {
                            $items['#id']->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Previously used value was specified.',
                                'Only unique value is allowed.'], ['title' => (new Text($items['#id']->title))->render() ]
                            ));
                        }
                    }
                }
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
            case 'cancel':
                if ($entity->name === 'tree_item') {
                    if (!Url::back_url_get())
                         Url::back_url_set('back', $entity->make_url_for_select_multiple().'///'.$items['#id_tree']->value_get());
                }
                break;
        }
    }

}
