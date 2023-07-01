<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use effcore\Entity;
use effcore\Text_multiline;
use effcore\Text;

abstract class Events_Form_Instance_insert {

    static function on_validate($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
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
                        $items['#id_data']->error_set(new Text_multiline([
                            'Field "%%_title" contains the previously used combination of values!',
                            'Only unique value is allowed.'], ['title' => (new Text($items['#id_data']->title))->render() ]
                        ));
                    }
                }
                break;
        }
    }

}
