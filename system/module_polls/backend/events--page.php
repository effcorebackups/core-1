<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\page_part_preset;
          use \effcore\translation;
          abstract class events_page {

  static function on_page_parts_dynamic_build($event, $id = null) {
    if ($id === null) {
      foreach (entity::get('poll')->instances_select() as $c_poll) {
        page_part_preset::insert('poll_form_'.$c_poll->id, translation::get('Poll').' (SQL)', $c_poll->question, ['content' => 'content'], null, 'copy', 'forms/polls/poll', ['_id_poll' => $c_poll->id], [], 0, 'polls');
      }
    }
    if ($id !== null && strpos($id, 'poll_form_') === 0) {
      $id_poll = substr($id, strlen('poll_form_'));
      $c_poll = (new instance('poll', ['id' => $id_poll]))->select();
      if ($c_poll) {
        page_part_preset::insert('poll_form_'.$c_poll->id, translation::get('Poll').' (SQL)', $c_poll->question, ['content' => 'content'], null, 'copy', 'forms/polls/poll', ['_id_poll' => $c_poll->id], [], 0, 'polls');
      }
    }
  }

}}