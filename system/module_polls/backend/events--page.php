<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\part_preset;
          abstract class events_page {

  static function on_part_presets_dynamic_build($event, $id = null) {
    if ($id === null                                   ) {foreach (entity::get('poll')->instances_select() as $c_item) part_preset::insert('poll_form_'.$c_item->id, 'Polls', $c_item->question, ['content' => 'content'], /* display = */ null, 'copy', 'forms/polls/poll', ['_id_poll' => $c_item->id], [ /* no args */ ], 0, 'polls');}
    if ($id !== null && strpos($id, 'poll_form_') === 0) {$c_item__id = substr($id, strlen('poll_form_'));             part_preset::insert('poll_form_'.$c_item__id, 'Polls', 'NO TITLE',        ['content' => 'content'], /* display = */ null, 'copy', 'forms/polls/poll', ['_id_poll' => $c_item__id], [ /* no args */ ], 0, 'polls');}
  }

}}