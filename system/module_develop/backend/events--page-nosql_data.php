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
          abstract class events_page_nosql_data {

  static function on_show_block_nosql_data() {
  }

  static function on_show_block_events($page) {
    $targets = new markup('x-targets');
    $report = new node();
    $events = event::get_all();
    ksort($events);
    foreach ($events as $c_event_type => $c_events) {
      $targets->child_insert(new markup('a', ['href' => '#type_'.$c_event_type], $c_event_type));
      $c_decorator = new decorator('table');
      $c_decorator->id = 'events_registered_handlers_'.$c_event_type;
      $c_decorator->result_attributes = ['data-is-compact' => 'true'];
      $report->child_insert(new markup('h2', ['id' => 'type_'.$c_event_type], $c_event_type), $c_event_type.'_header'   );
      $report->child_insert($c_decorator,                                                     $c_event_type.'_decorator');
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
      $targets,
      $report
    ]);
  }

}}
