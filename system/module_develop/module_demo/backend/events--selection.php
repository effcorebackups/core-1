<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          abstract class events_selection {

  static function on_selection_build_before_demo_selection_field_types($event, $selection) {
    $selection->field_insert_entity('type_field_code', 'demo_data', 'id', 'Type "field" from code', [], 390);
  }

}}
