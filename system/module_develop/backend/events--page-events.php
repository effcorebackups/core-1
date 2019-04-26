<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\block;
          use \effcore\decorator;
          use \effcore\event;
          use \effcore\markup;
          use \effcore\text_simple;
          abstract class events_page_events {

  static function on_show_block_events_list($page) {
    $title = new markup('h2', [], 'Registered event handlers');
    $decorator = new decorator('table');
    $decorator->id = 'events';
    $decorator->result_attributes = ['class' => ['compact' => 'compact']];
    foreach (event::get_all() as $c_event_type => $c_events) {
      foreach ($c_events as $c_event) {
        $decorator->data[] = [
          'type'      => ['value' => new text_simple($c_event_type),       'title' => 'Type'     ],
          'module_id' => ['value' => new text_simple($c_event->module_id), 'title' => 'Module ID'],
          'for_id'    => ['value' => new text_simple($c_event->for),       'title' => 'For ID'   ],
          'handler'   => ['value' => new text_simple($c_event->handler),   'title' => 'Handler'  ],
          'weight'    => ['value' => new text_simple($c_event->weight),    'title' => 'Weight'   ]
        ];
      }
    }
    return new block('', ['class' => ['events' => 'events']], [
      $title,
      $decorator
    ]);
  }

}}
