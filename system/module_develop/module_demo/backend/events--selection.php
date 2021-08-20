<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\text;
          abstract class events_selection {

  static function on_selection_build_before_demo_selection_field_types($event, $selection) {
    $selection->field_insert_entity('type_field_code', 'demo_data', 'id', new text('Type "%%_type" from code', ['type' => 'field']), [], 390);
    $selection->field_insert_text('type_text_code', '   item #%%_number', new text('Type "%%_type" from code', ['type' => 'text']), ['filters' => [500 => 'ltrim', 400 => 'translate', 300 => 'tokenized']], 210);
  }

}}
