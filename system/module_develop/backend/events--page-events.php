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

  static function on_show_block_events($page) {
    $ret_title = new markup('h2', [], 'Registered event types');
    $reh_title = new markup('h2', [], 'Registered event handlers');
    $ret_decorator = new decorator('table');
    $reh_decorator = new decorator('table');
    $ret_decorator->id = 'events_registered_types';
    $reh_decorator->id = 'events_registered_handlers';
    $ret_decorator->result_attributes = ['class' => ['compact' => 'compact']];
    $reh_decorator->result_attributes = ['class' => ['compact' => 'compact']];
    $events = event::get_all();
    ksort($events);
    foreach ($events as $c_event_type => $c_events) {
      $ret_decorator->data[] = ['type' => ['value' => new text_simple($c_event_type), 'title' => 'Type']];
      foreach ($c_events as $c_event) {
        $reh_decorator->data[] = [
          'type'      => ['value' => new text_simple($c_event_type      ), 'title' => 'Type'     ],
          'module_id' => ['value' => new text_simple($c_event->module_id), 'title' => 'Module ID'],
          'for_id'    => ['value' => new text_simple($c_event->for      ), 'title' => 'For ID'   ],
          'handler'   => ['value' => new text_simple($c_event->handler  ), 'title' => 'Handler'  ],
          'weight'    => ['value' => new text_simple($c_event->weight   ), 'title' => 'Weight'   ]
        ];
      }
    }
    return new block('', ['data-id' => 'events_registered'], [
      $ret_title,
      $ret_decorator,
      $reh_title,
      $reh_decorator
    ]);
  }

}}
