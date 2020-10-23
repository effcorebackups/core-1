<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\block_preset;
          use \effcore\entity;
          use \effcore\instance;
          abstract class events_page {

  static function on_block_presets_dynamic_build($event, $id = null) {
    if ($id === null                                    ) {foreach (entity::get('poll')->instances_select() as $c_item) block_preset::insert('poll_form__'.$c_item->id, 'Polls', $c_item->question, ['content' => 'content'], ['type' => 'copy', 'source' => 'forms/polls/poll', 'properties' => ['_id_poll' => $c_item->id]], 0, 'polls');}
    if ($id !== null && strpos($id, 'poll_form__') === 0) {$c_item__id = substr($id, strlen('poll_form__'));            block_preset::insert('poll_form__'.$c_item__id, 'Polls', 'NO TITLE',        ['content' => 'content'], ['type' => 'copy', 'source' => 'forms/polls/poll', 'properties' => ['_id_poll' => $c_item__id]], 0, 'polls');}
  }

}}