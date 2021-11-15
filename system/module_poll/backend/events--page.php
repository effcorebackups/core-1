<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\poll {
          use \effcore\block_preset;
          use \effcore\entity;
          use \effcore\instance;
          abstract class events_page {

  static function on_block_presets_dynamic_build($event, $id = null) {
    if ($id === null                                            ) {foreach (entity::get('poll')->instances_select() as $c_item) block_preset::insert('block__form__vote__'.$c_item->id, 'Polls', $c_item->question, ['content' => 'content'], ['title' => 'Voting form', 'title_is_visible' => false, 'type' => 'copy', 'source' => 'forms/poll/vote', 'properties' => ['_id_poll' => $c_item->id], 'args' => ['instance_id' => $c_item->id, 'entity_name' => 'poll'], 'has_admin_menu' => true, 'attributes' => ['data-block' => true, 'data-id' => 'block__poll_sql__'.$c_item->id]], 0, 'polls');}
    if ($id !== null && strpos($id, 'block__form__vote__') === 0) {$c_item__id = substr($id, strlen('block__form__vote__'));    block_preset::insert('block__form__vote__'.$c_item__id, 'Polls', 'NO TITLE',        ['content' => 'content'], ['title' => 'Voting form', 'title_is_visible' => false, 'type' => 'copy', 'source' => 'forms/poll/vote', 'properties' => ['_id_poll' => $c_item__id], 'args' => ['instance_id' => $c_item__id, 'entity_name' => 'poll'], 'has_admin_menu' => true, 'attributes' => ['data-block' => true, 'data-id' => 'block__poll_sql__'.$c_item__id]], 0, 'polls');}
  }

}}