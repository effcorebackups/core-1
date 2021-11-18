<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\demo {
          use const \effcore\nl;
          use \effcore\core;
          use \effcore\data;
          use \effcore\field;
          use \effcore\message;
          use \effcore\request;
          use \effcore\text;
          abstract class events_form_demo {

  static function on_init($event, $form, $items) {
    if ($form->clicked_button &&
        $form->clicked_button->value_get() === 'reset') {
      request::values_reset();
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
    $paths = data::select('demo_files');
    if (!empty($paths['texts'            ])) $items['#file_text'     ]->values_set($paths['texts'   ]);
    if (!empty($paths['pictures'         ])) $items['#file_picture'  ]->values_set($paths['pictures']);
    if (!empty($paths['audios'           ])) $items['#file_audio'    ]->values_set($paths['audios'  ]);
    if (!empty($paths['videos'           ])) $items['#file_video'    ]->values_set($paths['videos'  ]);
    if (!empty($paths[   'texts_multiple'])) $items['*files_texts'   ]->value_set_complex($paths[   'texts_multiple'], true);
    if (!empty($paths['pictures_multiple'])) $items['*files_pictures']->value_set_complex($paths['pictures_multiple'], true);
    if (!empty($paths[  'audios_multiple'])) $items['*files_audios'  ]->value_set_complex($paths[  'audios_multiple'], true);
    if (!empty($paths[  'videos_multiple'])) $items['*files_videos'  ]->value_set_complex($paths[  'videos_multiple'], true);
  }

  static function on_validate($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'send':
        message::insert(
          new text('Call "%%_call" on click "%%_click"', ['call' => '\\'.__METHOD__, 'click' => (new text('send'))->render()])
        );
        break;
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'send':
        message::insert(
          new text('Call "%%_call" on click "%%_click"', ['call' => '\\'.__METHOD__, 'click' => (new text('send'))->render()])
        );
        $def_value_checkboxes = core::array_keys_map(['checkbox_2', 'checkbox_4']);
        $def_value_switchers  = core::array_keys_map(['switcher_2', 'switcher_4']);
        $def_value_email = 'test1@example.com,test2@example.com';
        $def_value_select          = ['option_1' => 'Option 1 (selected)'];
        $def_value_select_multiple = ['option_1' => 'Option 1 (selected)'];
        $def_value_textarea = 'text in text area line 1'.nl.'text in text area line 2'.nl.'text in text area line 3';
        $def_value_textarea_data = new \stdClass;
        $def_value_textarea_data->root['key_1'] = 'value 1';
        $def_value_textarea_data->root['key_2'] = 'value 2';
        $def_value_textarea_data->root['key_3'] = 'value 3';
        if ($items['#text'           ]->value_get  ()         !== 'text in input'                ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#text'           ]->title))->render() ]) ); # …\field_text
        if ($items['#password'       ]->value_get  (false)    !== 'text in password'             ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#password'       ]->title))->render() ]) ); # …\field_password
        if ($items['#search'         ]->value_get  ()         !== 'text in search'               ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#search'         ]->title))->render() ]) ); # …\field_search
        if ($items['#url'            ]->value_get  ()         !== 'http://example.com'           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#url'            ]->title))->render() ]) ); # …\field_url
        if ($items['#tel'            ]->value_get  ()         !== '+000112334455'                ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#tel'            ]->title))->render() ]) ); # …\field_tel
        if ($items['#email'          ]->value_get  ()         !== $def_value_email               ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#email'          ]->title))->render() ]) ); # …\field_email
        if ($items['#nickname'       ]->value_get  ()         !== 'user'                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#nickname'       ]->title))->render() ]) ); # …\field_nickname
        if ($items['#number'         ]->value_get  ()         !== '0'                            ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#number'         ]->title))->render() ]) ); # …\field_number
        if ($items['#range'          ]->value_get  ()         !== '0'                            ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#range'          ]->title))->render() ]) ); # …\field_range
        if ($items['#color'          ]->value_get  ()         !== '#ffffff'                      ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#color'          ]->title))->render() ]) ); # …\field_color
        if ($items['#time'           ]->value_get  ()         !== '01:23:45'                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#time'           ]->title))->render() ]) ); # …\field_time
        if ($items['#date'           ]->value_get  ()         !== '2030-02-01'                   ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#date'           ]->title))->render() ]) ); # …\field_date
        if ($items['#datetime'       ]->value_get  ()         !== '2030-02-01 01:23:45'          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#datetime'       ]->title))->render() ]) ); # …\field_datetime
        if ($items['#datetime_local' ]->value_get  ()         !== '2030-01-02 12:00:00'          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#datetime_local' ]->title))->render() ]) ); # …\field_datetime_local
        if ($items['#timezone'       ]->value_get  ()         !== 'UTC'                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#timezone'       ]->title))->render() ]) ); # …\field_timezone
        if ($items['#select'         ]->values_get ()         !== $def_value_select              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#select'         ]->title))->render() ]) ); # …\field_select
        if ($items['#select_multiple']->values_get ()         !== $def_value_select_multiple     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#select_multiple']->title))->render() ]) ); # …\field_select
        if ($items['#logic'          ]->value_get  ()         !== 1                              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#logic'          ]->title))->render() ]) ); # …\field_logic
        if ($items['#relation'       ]->value_get  ()         !== 'demo_sql_item_1'              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#relation'       ]->title))->render() ]) ); # …\field_relation
        if ($items['#relation_tree'  ]->value_get  ()         !== 'demo_sql_item_1'              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#relation_tree'  ]->title))->render() ]) ); # …\field_relation
        if ($items['#lang_code'      ]->value_get  ()         !== 'en'                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#lang_code'      ]->title))->render() ]) ); # …\field_language
        if ($items['#text_direction' ]->value_get  ()         !== 'ltr'                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#text_direction' ]->title))->render() ]) ); # …\field_text_direction
        if ($items['#textarea'       ]->value_get  ()         !==        $def_value_textarea     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#textarea'       ]->title))->render() ]) ); # …\field_textarea
        if ((array)$items['#textarea_data']->value_data_get() !== (array)$def_value_textarea_data) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#textarea_data'  ]->title))->render() ]) ); # …\field_textarea_data
        if ($items['#checkbox'       ]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkbox'       ]->title))->render() ]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][0]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][0]->title))->render() ]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][1]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][1]->title))->render() ]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][2]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][2]->title))->render() ]) ); # …\field_checkbox
        if ($items['#checkboxes'  ][3]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][3]->title))->render() ]) ); # …\field_checkbox
        if ($items['#switcher'       ]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switcher'       ]->title))->render() ]) ); # …\field_switcher
        if ($items['#switchers'   ][0]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][0]->title))->render() ]) ); # …\field_switchers
        if ($items['#switchers'   ][1]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][1]->title))->render() ]) ); # …\field_switchers
        if ($items['#switchers'   ][2]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][2]->title))->render() ]) ); # …\field_switchers
        if ($items['#switchers'   ][3]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][3]->title))->render() ]) ); # …\field_switchers
        if ($items['#radiobutton'    ]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobutton'    ]->title))->render() ]) ); # …\field_radiobutton
        if ($items['#radiobuttons'][0]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobuttons'][0]->title))->render() ]) ); # …\field_radiobutton
        if ($items['#radiobuttons'][1]->checked_get()         !== true                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobuttons'][1]->title))->render() ]) ); # …\field_radiobutton
        if ($items['#radiobuttons'][2]->checked_get()         !== false                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobuttons'][2]->title))->render() ]) ); # …\field_radiobutton
        if ($items['*access'         ]->value_get_complex()   !== null                           ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*access'         ]->title))->render() ]) ); # …\widget_access
        if ($items['*checkboxes'     ]->values_get ()         !== $def_value_checkboxes          ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*checkboxes'     ]->title))->render() ]) ); # …\group_checkboxes
        if ($items['*switchers'      ]->values_get ()         !== $def_value_switchers           ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*switchers'      ]->title))->render() ]) ); # …\group_switchers
        if ($items['*radiobuttons'   ]->value_get  ()         !== 'radiobutton_2'                ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*radiobuttons'   ]->title))->render() ]) ); # …\group_radiobuttons
        if ($items['*palette_color'  ]->value_get  ()         !== 'transparent'                  ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*palette_color'  ]->title))->render() ]) ); # …\group_palette
      # save the files
        $paths = [];
        $paths['texts'            ] = $items['#file_text'     ]->values_get();
        $paths['pictures'         ] = $items['#file_picture'  ]->values_get();
        $paths['audios'           ] = $items['#file_audio'    ]->values_get();
        $paths['videos'           ] = $items['#file_video'    ]->values_get();
        $paths[   'texts_multiple'] = $items['*files_texts'   ]->value_get_complex();
        $paths['pictures_multiple'] = $items['*files_pictures']->value_get_complex();
        $paths[  'audios_multiple'] = $items['*files_audios'  ]->value_get_complex();
        $paths[  'videos_multiple'] = $items['*files_videos'  ]->value_get_complex();
        if (empty($paths['texts'            ])) unset($paths['texts'            ]);
        if (empty($paths['pictures'         ])) unset($paths['pictures'         ]);
        if (empty($paths['audios'           ])) unset($paths['audios'           ]);
        if (empty($paths['videos'           ])) unset($paths['videos'           ]);
        if (empty($paths[   'texts_multiple'])) unset($paths[   'texts_multiple']);
        if (empty($paths['pictures_multiple'])) unset($paths['pictures_multiple']);
        if (empty($paths[  'audios_multiple'])) unset($paths[  'audios_multiple']);
        if (empty($paths[  'videos_multiple'])) unset($paths[  'videos_multiple']);
        if (count($paths)) data::update('demo_files', $paths);
        else               data::delete('demo_files');
        break;
    }
  }

}}