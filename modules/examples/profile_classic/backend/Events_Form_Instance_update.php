<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\Entity;
use effcore\Page;
use effcore\URL;

abstract class Events_Form_Instance_update {

    static function on_submit($event, $form, $items) {
        $entity = Entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'update':
            case 'cancel':
                if ($entity->name === 'user' && Page::get_current()->id === 'user_edit_ru') {
                    if (!URL::back_url_get())
                         URL::back_url_set('back', '/ru/user/'.$items['#nickname']->value_get());
                }
                break;
        }
    }

}
