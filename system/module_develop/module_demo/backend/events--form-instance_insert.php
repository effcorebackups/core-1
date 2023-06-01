<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\entity;
use effcore\text_multiline;
use effcore\text;

abstract class events_form_instance_insert {

    static function on_validate($event, $form, $items) {
        $entity = entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'insert':
            case 'insert_and_update':
                if ($entity->name === 'demo_join' && !$form->has_error()) {
                    # field 'id_data'
                    $id_data = $items['#id_data']->value_get();
                    $result = $entity->instances_select(['conditions' => [
                        'id_data_!f'       => 'id_data',
                        'id_data_operator' => '=',
                        'id_data_!v'       => $id_data], 'limit' => 1]);
                    if ($result) {
                        $items['#id_data']->error_set(new text_multiline([
                            'Field "%%_title" contains the previously used combination of values!',
                            'Only unique value is allowed.'], ['title' => (new text($items['#id_data']->title))->render() ]
                        ));
                    }
                }
                break;
        }
    }

}
