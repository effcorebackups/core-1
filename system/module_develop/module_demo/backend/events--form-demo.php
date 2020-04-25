<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use \effcore\core;
          use \effcore\data;
          use \effcore\field;
          use \effcore\message;
          use \effcore\text;
          use \effcore\translation;
          abstract class events_form_demo {

  static function on_init($event, $form, $items) {
    if ($form->clicked_button &&
        $form->clicked_button->value_get() == 'reset') {
      field::request_values_reset();
    }
    $items['#select'         ]->option_insert('Option 5 (inserted + disabled from code)', 'option_5', ['disabled' => true], 'group_1');
    $items['#select_multiple']->option_insert('Option 5 (inserted + disabled from code)', 'option_5', ['disabled' => true], 'group_1');
    $items['#select'         ]->option_insert('Option 6 (inserted from code)', 'option_6', [], 'group_1');
    $items['#select_multiple']->option_insert('Option 6 (inserted from code)', 'option_6', [], 'group_1');
    $items['#select'         ]->optgroup_insert('group_2', 'Group 2 (inserted from code)');
    $items['#select_multiple']->optgroup_insert('group_2', 'Group 2 (inserted from code)');
    $items['#select'         ]->option_insert('Option 7 (inserted from code)', 'option_7', [], 'group_2');
    $items['#select_multiple']->option_insert('Option 7 (inserted from code)', 'option_7', [], 'group_2');
    $items['#select'         ]->option_insert('Option 8 (inserted from code)', 'option_8', [], 'group_2');
    $items['#select_multiple']->option_insert('Option 8 (inserted from code)', 'option_8', [], 'group_2');
    $items['#select'         ]->option_insert('Option 9 (inserted from code)', 'option_9', [], 'group_2');
    $items['#select_multiple']->option_insert('Option 9 (inserted from code)', 'option_9', [], 'group_2');
    $items['*palette_color']->value_set('transparent');
    $items['#picture']->values_set(data::select('files_demo'));
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'send':
        message::insert(
          new text('Call "%%_call"', ['call' => '\\'.__METHOD__])
        );
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    message::insert(
      new text('Call "%%_call"', ['call' => '\\'.__METHOD__])
    );
    switch ($form->clicked_button->value_get()) {
      case 'send':
        $def_value_checkboxes = core::array_kmap(['checkbox_2', 'checkbox_4']);
        $def_value_switchers  = core::array_kmap(['switcher_2', 'switcher_4']);
        $def_value_email = 'test1@example.com,test2@example.com';
        $def_value_select          = ['option_1' => 'Option 1 (selected)'];
        $def_value_select_multiple = ['option_1' => 'Option 1 (selected)'];
        if ($items['#text'           ]->value_get  ()       != 'text in input'           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#text'           ]->title)]) ); # …\field_text
        if ($items['#password'       ]->value_get  (false)  != 'text in password'        ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#password'       ]->title)]) ); # …\field_password
        if ($items['#search'         ]->value_get  ()       != 'text in search'          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#search'         ]->title)]) ); # …\field_search
        if ($items['#url'            ]->value_get  ()       != 'http://example.com'      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#url'            ]->title)]) ); # …\field_url
        if ($items['#tel'            ]->value_get  ()       != '+000112334455'           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#tel'            ]->title)]) ); # …\field_tel
        if ($items['#email'          ]->value_get  ()       != $def_value_email          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#email'          ]->title)]) ); # …\field_email
        if ($items['#nickname'       ]->value_get  ()       != 'user'                    ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#nickname'       ]->title)]) ); # …\field_nickname
        if ($items['#number'         ]->value_get  ()       != '0'                       ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#number'         ]->title)]) ); # …\field_number
        if ($items['#range'          ]->value_get  ()       != '0'                       ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#range'          ]->title)]) ); # …\field_range
        if ($items['#color'          ]->value_get  ()       != '#ffffff'                 ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#color'          ]->title)]) ); # …\field_color
        if ($items['#date'           ]->value_get  ()       != '2030-02-01'              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#date'           ]->title)]) ); # …\field_date
        if ($items['#time'           ]->value_get  ()       != '01:23:45'                ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#time'           ]->title)]) ); # …\field_time
        if ($items['#datetime'       ]->value_get  ()       != '2030-02-01 01:23:45'     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#datetime'       ]->title)]) ); # …\field_datetime
        if ($items['#datetime_local' ]->value_get  ()       != '2030-02-01 01:23:45'     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#datetime_local' ]->title)]) ); # …\field_datetime_local
        if ($items['#timezone'       ]->value_get  ()       != 'UTC'                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#timezone'       ]->title)]) ); # …\field_timezone
        if ($items['#select'         ]->values_get ()       != $def_value_select         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#select'         ]->title)]) ); # …\field_select
        if ($items['#select_multiple']->values_get ()       != $def_value_select_multiple) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#select_multiple']->title)]) ); # …\field_select
        if ($items['#logic'          ]->value_get  ()       != '1'                       ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#logic'          ]->title)]) ); # …\field_logic
        if ($items['#relation'       ]->value_get  ()       != 'demo_sql_item_1'         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#relation'       ]->title)]) ); # …\field_relation
        if ($items['#relation_tree'  ]->value_get  ()       != 'demo_sql_item_1'         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#relation_tree'  ]->title)]) ); # …\field_relation
        if ($items['#lang_code'      ]->value_get  ()       != 'en'                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#lang_code'      ]->title)]) ); # …\field_language
        if ($items['#text_direction' ]->value_get  ()       != 'ltr'                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#text_direction' ]->title)]) ); # …\field_text_direction
        if ($items['#textarea'       ]->value_get  ()       != 'text in text area'       ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#textarea'       ]->title)]) ); # …\field_textarea
        if ($items['#checkbox'       ]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#checkbox'       ]->title)]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][0]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#checkboxes'  ][0]->title)]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][1]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#checkboxes'  ][1]->title)]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][2]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#checkboxes'  ][2]->title)]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][3]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#checkboxes'  ][3]->title)]) ); # …\field_checkbox
        if ($items['#switcher'       ]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#switcher'       ]->title)]) ); # …\field_switcher
        if ($items['#switchers'   ][0]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#switchers'   ][0]->title)]) ); # …\field_switchers
        if ($items['#switchers'   ][1]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#switchers'   ][1]->title)]) ); # …\field_switchers
        if ($items['#switchers'   ][2]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#switchers'   ][2]->title)]) ); # …\field_switchers
        if ($items['#switchers'   ][3]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#switchers'   ][3]->title)]) ); # …\field_switchers
        if ($items['#radiobutton'    ]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#radiobutton'    ]->title)]) ); # …\field_radiobutton
        if ($items['#radiobuttons'][0]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#radiobuttons'][0]->title)]) ); # …\field_radiobutton
        if ($items['#radiobuttons'][1]->checked_get()       != true                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#radiobuttons'][1]->title)]) ); # …\field_radiobutton
        if ($items['#radiobuttons'][2]->checked_get()       != false                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => translation::apply($items['#radiobuttons'][2]->title)]) ); # …\field_radiobutton
        if ($items['*access'         ]->value_get_complex() != []                        ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => translation::apply($items['*access'         ]->title)]) ); # …\widget_access
        if ($items['*checkboxes'     ]->values_get ()       != $def_value_checkboxes     ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => translation::apply($items['*checkboxes'     ]->title)]) ); # …\group_checkboxes
        if ($items['*switchers'      ]->values_get ()       != $def_value_switchers      ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => translation::apply($items['*switchers'      ]->title)]) ); # …\group_switchers
        if ($items['*radiobuttons'   ]->value_get  ()       != 'radiobutton_2'           ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => translation::apply($items['*radiobuttons'   ]->title)]) ); # …\group_radiobuttons
        if ($items['*palette_color'  ]->value_get  ()       != 'transparent'             ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => translation::apply($items['*palette_color'  ]->title)]) ); # …\group_palette
      # save the files
        $paths = $items['#picture']->values_get();
        if (count($paths)) data::update('files_demo', $paths);
        else               data::delete('files_demo');
        break;
    }
  }

}}