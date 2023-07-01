<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\poll;

use effcore\Block_preset;
use effcore\Entity;

abstract class Events_Page {

    static function on_block_presets_dynamic_build($event, $id = null) {
        if ($id === null                                            ) {foreach (Entity::get('poll')->instances_select() as $c_item) Block_preset::insert('block__form__vote__'.$c_item->id, 'Polls', $c_item->question, ['content' => 'content'], ['title' => 'Voting form', 'title_is_visible' => false, 'type' => 'copy', 'source' => 'forms/poll/vote', 'properties' => ['_id_poll' => $c_item->id], 'args' => ['instance_id' => $c_item->id, 'entity_name' => 'poll'], 'has_admin_menu' => true, 'attributes' => ['data-id' => 'block__poll_sql__'.$c_item->id]], 0, 'polls');}
        if ($id !== null && strpos($id, 'block__form__vote__') === 0) {$c_item__id = substr($id, strlen('block__form__vote__'));    Block_preset::insert('block__form__vote__'.$c_item__id, 'Polls', 'NO TITLE',        ['content' => 'content'], ['title' => 'Voting form', 'title_is_visible' => false, 'type' => 'copy', 'source' => 'forms/poll/vote', 'properties' => ['_id_poll' => $c_item__id], 'args' => ['instance_id' => $c_item__id, 'entity_name' => 'poll'], 'has_admin_menu' => true, 'attributes' => ['data-id' => 'block__poll_sql__'.$c_item__id]], 0, 'polls');}
    }

}
