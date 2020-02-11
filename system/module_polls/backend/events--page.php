<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\polls {
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\part_preset;
          abstract class events_page {

  static function on_part_presets_dynamic_build($event, $id = null) {
    if ($id === null                                   ) {foreach (entity::get('poll')->instances_select() as $c_poll)                                                                                              part_preset::insert('poll_form_'.$c_poll->id, 'Polls', $c_poll->question, ['content' => 'content'], null, 'copy', 'forms/polls/poll', ['_id_poll' => $c_poll->id], [], 0, 'polls');}
    if ($id !== null && strpos($id, 'poll_form_') === 0) {                                                    $c_poll = (new instance('poll', ['id' => substr($id, strlen('poll_form_'))]))->select(); if ($c_poll) part_preset::insert('poll_form_'.$c_poll->id, 'Polls', $c_poll->question, ['content' => 'content'], null, 'copy', 'forms/polls/poll', ['_id_poll' => $c_poll->id], [], 0, 'polls');}
  }

}}