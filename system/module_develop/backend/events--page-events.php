<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\block;
          use \effcore\decorator;
          use \effcore\event;
          use \effcore\markup;
          use \effcore\node;
          use \effcore\text_simple;
          abstract class events_page_events {

  static function on_show_block_events($page) {
    $events = event::get_all();
    ksort($events);
  # ─────────────────────────────────────────────────────────────────────
  # prepare report for each registered event types
  # ─────────────────────────────────────────────────────────────────────
    $ret_decorator = new decorator('table');
    $ret_decorator->id = 'events_registered_types';
    $ret_decorator->result_attributes = ['class' => ['compact' => 'compact']];
    foreach ($events as $c_event_type => $c_events) {
      $ret_decorator->data[] = [
        'type' => ['value' => new markup('a', ['href' => '#type_'.$c_event_type], $c_event_type), 'title' => 'Registered event types']
      ];
    }
  # ─────────────────────────────────────────────────────────────────────
  # prepare report for each registered event handler
  # ─────────────────────────────────────────────────────────────────────
    $reh_report = new node();
    foreach ($events as $c_event_type => $c_events) {
      $c_decorator = new decorator('table');
      $c_decorator->id = 'events_registered_handlers_'.$c_event_type;
      $c_decorator->result_attributes = ['class' => ['compact' => 'compact']];
      $reh_report->child_insert(new markup('h2', ['id' => 'type_'.$c_event_type], $c_event_type), $c_event_type.'_header'   );
      $reh_report->child_insert($c_decorator,                                                     $c_event_type.'_decorator');
      foreach ($c_events as $c_event) {
        $c_decorator->data[] = [
          'module_id' => ['value' => new text_simple($c_event->module_id), 'title' => 'Module ID'],
          'for_id'    => ['value' => new text_simple($c_event->for      ), 'title' => 'For ID'   ],
          'handler'   => ['value' => new text_simple($c_event->handler  ), 'title' => 'Handler'  ],
          'weight'    => ['value' => new text_simple($c_event->weight   ), 'title' => 'Weight'   ]
        ];
      }
    }
    return new block('', ['data-id' => 'events_registered'], [
      $ret_decorator,
      $reh_report
    ]);
  }

}}
