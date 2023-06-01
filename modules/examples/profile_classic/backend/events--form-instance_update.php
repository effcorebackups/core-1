<?php

######################################################################
### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
######################################################################

namespace effcore\modules\profile_classic;

use effcore\entity;
use effcore\page;
use effcore\url;

abstract class events_form_instance_update {

    static function on_submit($event, $form, $items) {
        $entity = entity::get($form->entity_name);
        switch ($form->clicked_button->value_get()) {
            case 'update':
            case 'cancel':
                if ($entity->name === 'user' && page::get_current()->id === 'user_edit_ru') {
                    if (!url::back_url_get())
                         url::back_url_set('back', '/ru/user/'.$items['#nickname']->value_get());
                }
                break;
        }
    }

}
