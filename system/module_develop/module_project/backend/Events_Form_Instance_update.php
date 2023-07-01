<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\project;

use effcore\Entity;
use effcore\File;

abstract class Events_Form_Instance_update {

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'update':
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
