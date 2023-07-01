<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\project;

use effcore\Entity;
use effcore\File;
use effcore\Project_release;
use effcore\Text_multiline;
use effcore\Text;

abstract class Events_Form_Instance_insert {

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                if ($entity->name === 'project_release' && !$form->has_error()) {
                    # field 'id_project' + field 'build'
                    $id_project = $items['#id_project']->value_get();
                    $build      = $items['#build'     ]->value_get();
                    if (Project_release::select($id_project, $build)) {
                        $items['#id_project']->error_set();
                        $items['#build']->error_set(new Text_multiline([
                            'Field "%%_title" contains an error!',
                            'This combination of values is already in use!'], ['title' => (new Text($items['#build']->title))->render() ]
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
                if ($entity->name === 'project_release' && !$form->has_error()) {
                    # field 'hash sum'
                    $file = new File($items['#path']->value_get());
                    if ($file->is_exists())
                         $items['#hash_sum']->value_set($file->hash_get());
                    else $items['#hash_sum']->value_set('');
                }
                break;
        }
    }

}
