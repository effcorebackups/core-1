<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\Entity;
use effcore\Field_Hidden;
use effcore\Markup;
use effcore\Message;
use effcore\Node;
use effcore\Page;
use effcore\Request;
use effcore\Text;
use effcore\Tree;
use effcore\Url;
use stdClass;

abstract class Events_Form_Instance_select_multiple {

    static function on_build($event, $form) {
        if ($form->has_error_on_build === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'tree_item') {
                if (!$form->category_id)
                     $form->category_id = Page::get_current()->args_get('category_id');
                $trees = Tree::select_all('sql');
                if (isset($trees[$form->category_id])) {
                    # drag-and-drop functionality
                    $form->_selection->is_builded = false;
                    $form->_selection->query_settings['conditions'] = [
                        'id_tree_!f'       => 'id_tree',
                        'id_tree_operator' => '=',
                        'id_tree_!v'       => $form->category_id];
                    # field 'extra'
                    $form->_selection->fields['code']['extra'] = new stdClass;
                    $form->_selection->fields['code']['extra']->closure = function ($c_row_id, $c_row, $c_instance, $settings = []) {
                        return new Node([], [
                            'actions'       => $c_row['actions']['value'],
                            'hidden_parent' => new Field_Hidden('parent-'.$c_instance->id, $c_instance->id_parent, ['data-type' => 'parent']),
                            'hidden_weight' => new Field_Hidden('weight-'.$c_instance->id, $c_instance->weight,    ['data-type' => 'weight'])
                        ]);
                    };
                    $form->_selection->build();
                } else {
                    $form->child_select('data')->child_delete('selection');
                    $form->child_select('data')->child_insert(new Markup('x-no-items', ['data-style' => 'table'], 'wrong category'), 'message_error');
                    $form->has_error_on_build = true;
                }
            }
        }
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'tree_item') {
                $items['#actions']->disabled_set();
                $items[ '~apply' ]->disabled_set(count($form->_selection->_instances) === 0);
            }
        }
    }

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                if ($entity->name === 'tree_item' && $form->category_id) {
                    $event->is_last = true;
                    $has_changes = false;
                    $tree_items = Entity::get('tree_item')->instances_select(['conditions' => [
                        'id_tree_!f'       => 'id_tree',
                        'id_tree_operator' => '=',
                        'id_tree_!v'       => $form->category_id]], 'id');
                    foreach ($tree_items as $c_item) {
                        $c_new_parent = Request::value_get('parent-'.$c_item->id) ?: null;
                        $c_new_weight = Request::value_get('weight-'.$c_item->id) ?: '0';
                        if ( ($c_new_parent === null || isset($tree_items[$c_new_parent])) &&
                             ($c_new_weight ===              (string)(int)$c_new_weight) ) {
                            if ($c_item->id_parent !== $c_new_parent ||
                                $c_item->weight    !== $c_new_weight) {
                                $c_item->id_parent  =  $c_new_parent;
                                $c_item->weight     =  $c_new_weight;
                                $has_changes = true;
                                $c_result = $c_item->update();
                                if ($form->is_show_result_message && $c_result !== null) Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was updated.',     ['type' => (new Text($entity->title))->render(), 'id' => $c_item->id])           );
                                if ($form->is_show_result_message && $c_result === null) Message::insert(new Text('Item of type "%%_type" with ID = "%%_id" was not updated!', ['type' => (new Text($entity->title))->render(), 'id' => $c_item->id]), 'warning');
                            }
                        }
                    }
                    if ($form->is_show_result_message && !$has_changes) {
                        Message::insert(
                            'You have not made any changes before!', 'warning'
                        );
                    }
                    static::on_build(null, $form);
                    static::on_init (null, $form, $items);
                }
                break;
            case 'insert':
                if ($entity->name === 'tree_item' && $form->category_id) {
                    Url::go($entity->make_url_for_insert().'/'.$form->category_id.'?'.Url::back_part_make());
                }
                break;
        }
    }

}
