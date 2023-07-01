<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\user;

use effcore\Entity;
use effcore\Message;
use effcore\Page;
use effcore\Form_plugin;
use effcore\Text_multiline;
use effcore\Text;

abstract class Events_Form_Instance_insert {

    static function on_build($event, $form) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'relation_role_ws_user') {
                # field 'role'
                $form->child_select('fields')->child_select('id_role')->disabled['anonymous' ] = 'anonymous';
                $form->child_select('fields')->child_select('id_role')->disabled['registered'] = 'registered';
            }
            if ($entity->name === 'feedback' && Page::get_current()->id !== 'instance_insert') {
                # field 'captcha', button 'cancel', button 'insert'
                $captcha = new Form_plugin('Field_Captcha', [], [], -500);
                $form->child_select('fields')->child_insert($captcha, 'captcha');
                $form->child_delete('button_cancel');
                $form->child_select('button_insert')->title = 'send';
            }
        }
    }

    static function on_init($event, $form, $items) {
        if ($form->has_error_on_build === false &&
            $form->has_no_fields      === false) {
            $entity = Entity::get($form->entity_name);
            if ($entity->name === 'feedback' && Page::get_current()->id !== 'instance_insert') {
                $form->is_show_result_message = false;
                $form->is_redirect_enabled    = false;
                $items['#name'   ]->value_set('');
                $items['#email'  ]->value_set('');
                $items['#message']->value_set('');
                $items['#captcha']->value_set('');
            }
        }
    }

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                if ($entity->name === 'relation_role_ws_user' && !$form->has_error()) {
                    # field 'user' + field 'role'
                    $id_user = $items['#id_user']->value_get();
                    $id_role = $items['#id_role']->value_get();
                    $result = $entity->instances_select(['conditions' => ['conjunction_!and' => [
                        'id_user' => ['id_user_!f' => 'id_user', 'id_user_operator' => '=', 'id_user_!v' => $id_user],
                        'id_role' => ['id_role_!f' => 'id_role', 'id_role_operator' => '=', 'id_role_!v' => $id_role] ]], 'limit' => 1]);
                    if ($result) {
                        $items['#id_user']->error_set();
                        $items['#id_role']->error_set(new Text_multiline([
                            'Field "%%_title" contains an error!',
                            'This combination of values is already in use!'], ['title' => (new Text($items['#id_role']->title))->render() ]
                        ));
                    }
                }
                if ($entity->name === 'relation_role_ws_permission' && !$form->has_error()) {
                    # field 'role' + field 'permission'
                    $id_role       = $items['#id_role'      ]->value_get();
                    $id_permission = $items['#id_permission']->value_get();
                    $result = $entity->instances_select(['conditions' => ['conjunction_!and' => [
                        'id_role'       => [      'id_role_!f' => 'id_role',             'id_role_operator' => '=',       'id_role_!v' => $id_role      ],
                        'id_permission' => ['id_permission_!f' => 'id_permission', 'id_permission_operator' => '=', 'id_permission_!v' => $id_permission] ]], 'limit' => 1]);
                    if ($result) {
                        $items['#id_role'      ]->error_set();
                        $items['#id_permission']->error_set(new Text_multiline([
                            'Field "%%_title" contains an error!',
                            'This combination of values is already in use!'], ['title' => (new Text($items['#id_permission']->title))->render() ]
                        ));
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
                # feedback
                if ($entity->name === 'feedback' && Page::get_current()->id !== 'instance_insert') {
                    Message::insert(new Text('Feedback with ID = "%%_id" has been sent.', ['id' => implode('+', $form->_instance->values_id_get()) ]));
                    static::on_init(null, $form, $items);
                }
                break;
        }
    }

}
