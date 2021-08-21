<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\markup;
          use \effcore\text;
          abstract class events_selection {

  static function on_selection_build_before_demo_selection_field_types($event, $selection) {
    $selection->field_insert_entity     ('type_field_code',                            new text('Type "%%_type" from code', ['type' => 'field'                     ]), 'demo_data', 'id',                                            [                                                                    ], 900);
    $selection->field_insert_entity_join('type_field_join_code',                       new text('Type "%%_type" from code', ['type' => 'join_field'                ]), 'demo_join', 'email',                                         [                                                                    ], 800);
    $selection->field_insert_text       ('type_text_with_translation_code',            new text('Type "%%_type" from code', ['type' => 'text + translation'        ]), 'text with translation',                                      ['filters' => [500 => 'trim', 400 => 'translate'                    ]], 700);
    $selection->field_insert_text       ('type_text_with_translation_with_token_code', new text('Type "%%_type" from code', ['type' => 'text + translation + token']), 'text with translation and token demo_text = "%%_demo_text"', ['filters' => [500 => 'trim', 400 => 'translate', 300 => 'tokenized']], 600);
    $selection->field_insert_text       ('type_text_with_token_code',                  new text('Type "%%_type" from code', ['type' => 'text + token'              ]), 'text with token demo_text = "%%_demo_text"',                 ['filters' => [500 => 'trim',                     300 => 'tokenized']], 500);
    $selection->field_insert_markup     ('type_markup_code',                           new text('Type "%%_type" from code', ['type' => 'markup'                    ]), new markup('span', [], 'markup'),                             [                                                                    ], 400);
    $selection->field_insert_handler    ('type_handler_code',                          new text('Type "%%_type" from code', ['type' => 'handler'                   ]), '\\effcore\\modules\\demo\\events_selection::demo_handler',   [                                                                    ], 300);
    $selection->field_insert_checkbox   ('checkbox-select',                            new text('Type "%%_type" from code', ['type' => 'checkbox'                  ]),                                                               ['name' => 'is_checked[]'                                            ], 200);
  }

  static function demo_handler($row_id, $row, $instance, $settings = []) {
    return new markup('span', [], 'handler');
  }

}}
