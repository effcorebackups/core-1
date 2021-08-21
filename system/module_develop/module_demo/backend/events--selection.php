<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\text;
          abstract class events_selection {

  static function on_selection_build_before_demo_selection_field_types($event, $selection) {
    $selection->field_insert_entity('type_field_code', 'demo_data', 'id', new text('Type "%%_type" from code', ['type' => 'field']), [], 390);
    $selection->field_insert_entity_join('type_field_join_code', 'demo_join', 'email', new text('Type "%%_type" from code', ['type' => 'join_field']), [], 370);
    $selection->field_insert_text('type_text_with_translation_code', 'text with translation', new text('Type "%%_type" from code', ['type' => 'text + translation']), ['filters' => [500 => 'trim', 400 => 'translate']], 190);
    $selection->field_insert_text('type_text_with_translation_with_token_code', 'text with translation and token demo_text = "%%_demo_text"', new text('Type "%%_type" from code', ['type' => 'text + translation + token']), ['filters' => [500 => 'trim', 400 => 'translate', 300 => 'tokenized']], 170);
    $selection->field_insert_text('type_text_with_token_code', 'text with token demo_text = "%%_demo_text"', new text('Type "%%_type" from code', ['type' => 'text + token']), ['filters' => [500 => 'trim', 300 => 'tokenized']], 150);
  }

}}
