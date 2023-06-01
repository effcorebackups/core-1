<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use const effcore\NL;
use effcore\core;
use effcore\data;
use effcore\message;
use effcore\request;
use effcore\text;
use stdClass;

abstract class events_form_demo {

    static function on_build($event, $form) {
        $field_select          = $form->child_select('form_elements')->child_select('select');
        $field_select_multiple = $form->child_select('form_elements')->child_select('select_multiple');
        $field_select         ->disabled['option_5'] = 'option_5';
        $field_select_multiple->disabled['option_5'] = 'option_5';
        $field_select_items = $field_select->items_get();
        $field_select_items['group_1']->items['option_5'] = 'Option 5 (inserted + disabled from code)';
        $field_select_items['group_1']->items['option_6'] = 'Option 6 (inserted from code)';
        $field_select_items['group_2'] = new stdClass;
        $field_select_items['group_2']->title = 'Group 2 (inserted from code)';
        $field_select_items['group_2']->items['option_7'] = 'Option 7 (inserted from code)';
        $field_select_items['group_2']->items['option_8'] = 'Option 8 (inserted from code)';
        $field_select_items['group_2']->items['option_9'] = 'Option 9 (inserted from code)';
        $field_select->items_set($field_select_items);
        $field_select_multiple_items = $field_select_multiple->items_get();
        $field_select_multiple_items['group_1']->items['option_5'] = 'Option 5 (inserted + disabled from code)';
        $field_select_multiple_items['group_1']->items['option_6'] = 'Option 6 (inserted from code)';
        $field_select_multiple_items['group_2'] = new stdClass;
        $field_select_multiple_items['group_2']->title = 'Group 2 (inserted from code)';
        $field_select_multiple_items['group_2']->items['option_7'] = 'Option 7 (inserted from code)';
        $field_select_multiple_items['group_2']->items['option_8'] = 'Option 8 (inserted from code)';
        $field_select_multiple_items['group_2']->items['option_9'] = 'Option 9 (inserted from code)';
        $field_select_multiple->items_set($field_select_multiple_items);
    }

    static function on_init($event, $form, $items) {
        if ($form->clicked_button &&
            $form->clicked_button->value_get() === 'reset') {
            request::values_reset();
        }
        $items['*palette_color']->value_set('transparent');
        $paths = data::select('demo_files');
        if (!empty($paths['texts'            ])) $items['#file_text'     ]->value_set($paths['texts'   ]);
        if (!empty($paths['pictures'         ])) $items['#file_picture'  ]->value_set($paths['pictures']);
        if (!empty($paths['audios'           ])) $items['#file_audio'    ]->value_set($paths['audios'  ]);
        if (!empty($paths['videos'           ])) $items['#file_video'    ]->value_set($paths['videos'  ]);
        if (!empty($paths[   'texts_multiple'])) $items['*files_texts'   ]->value_set($paths[   'texts_multiple'], ['once' => true]);
        if (!empty($paths['pictures_multiple'])) $items['*files_pictures']->value_set($paths['pictures_multiple'], ['once' => true]);
        if (!empty($paths[  'audios_multiple'])) $items['*files_audios'  ]->value_set($paths[  'audios_multiple'], ['once' => true]);
        if (!empty($paths[  'videos_multiple'])) $items['*files_videos'  ]->value_set($paths[  'videos_multiple'], ['once' => true]);
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
                $value_def_access = (object)['roles' => ['demo' => 'demo']];
                $value_def_checkboxes = core::array_keys_map(['checkbox_2', 'checkbox_4']);
                $value_def_switchers  = core::array_keys_map(['switcher_2', 'switcher_4']);
                $value_def_email = 'test1@example.com,test2@example.com';
                $value_def_select          =  'option_1';
                $value_def_select_multiple = ['option_1' => 'Option 1 (selected)'];
                $value_def_textarea = 'text in text area line 1'.NL.'text in text area line 2'.NL.'text in text area line 3';
                $value_def_textarea_data_hash = 'd771b4aace366743c7defb694dbfa827';
                if ($items['#text'           ]->value_get  ()    !== 'text in input'               ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#text'           ]->title))->render() ]) ); # …\field_text
                if ($items['#password'       ]->value_get(false) !== 'text in password'            ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#password'       ]->title))->render() ]) ); # …\field_password
                if ($items['#search'         ]->value_get  ()    !== 'text in search'              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#search'         ]->title))->render() ]) ); # …\field_search
                if ($items['#url'            ]->value_get  ()    !== 'http://example.com'          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#url'            ]->title))->render() ]) ); # …\field_url
                if ($items['#tel'            ]->value_get  ()    !== '+000112334455'               ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#tel'            ]->title))->render() ]) ); # …\field_tel
                if ($items['#email'          ]->value_get  ()    !== $value_def_email              ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#email'          ]->title))->render() ]) ); # …\field_email
                if ($items['#nickname'       ]->value_get  ()    !== 'user'                        ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#nickname'       ]->title))->render() ]) ); # …\field_nickname
                if ($items['#number'         ]->value_get  ()    !== '0'                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#number'         ]->title))->render() ]) ); # …\field_number
                if ($items['#range'          ]->value_get  ()    !== '0'                           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#range'          ]->title))->render() ]) ); # …\field_range
                if ($items['#color'          ]->value_get  ()    !== '#ffffff'                     ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#color'          ]->title))->render() ]) ); # …\field_color
                if ($items['#time'           ]->value_get  ()    !== '01:23:45'                    ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#time'           ]->title))->render() ]) ); # …\field_time
                if ($items['#date'           ]->value_get  ()    !== '2030-02-01'                  ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#date'           ]->title))->render() ]) ); # …\field_date
                if ($items['#datetime'       ]->value_get  ()    !== '2030-02-01 01:23:45'         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#datetime'       ]->title))->render() ]) ); # …\field_datetime
                if ($items['#datetime_local' ]->value_get  ()    !== '2030-01-02 12:00:00'         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#datetime_local' ]->title))->render() ]) ); # …\field_datetime_local
                if ($items['#timezone'       ]->value_get  ()    !== 'UTC'                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#timezone'       ]->title))->render() ]) ); # …\field_select_timezone
                if ($items['#select'         ]->value_get  ()    !== $value_def_select             ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#select'         ]->title))->render() ]) ); # …\field_select
                if ($items['#select_multiple']->value_get  ()    !== $value_def_select_multiple    ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#select_multiple']->title))->render() ]) ); # …\field_select
                if ($items['#logic'          ]->value_get  ()    !== 1                             ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#logic'          ]->title))->render() ]) ); # …\field_select_logic
                if ($items['#relation'       ]->value_get  ()    !== 'demo_sql_item_1'             ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#relation'       ]->title))->render() ]) ); # …\field_select_relation
                if ($items['#relation_tree'  ]->value_get  ()    !== 'demo_sql_item_1'             ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#relation_tree'  ]->title))->render() ]) ); # …\field_select_relation
                if ($items['#lang_code'      ]->value_get  ()    !== 'en'                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#lang_code'      ]->title))->render() ]) ); # …\field_select_language
                if ($items['#text_direction' ]->value_get  ()    !== 'ltr'                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#text_direction' ]->title))->render() ]) ); # …\field_select_text_direction
                if ($items['#textarea'       ]->value_get  ()    !== $value_def_textarea           ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#textarea'       ]->title))->render() ]) ); # …\field_textarea
                if (md5($items['#textarea_data']->value_get())   !== $value_def_textarea_data_hash ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#textarea_data'  ]->title))->render() ]) ); # …\field_textarea_data
                if ($items['#checkbox'       ]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkbox'       ]->title))->render() ]) ); # …\field_checkbox
                if ($items['#checkboxes'  ][0]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][0]->title))->render() ]) ); # …\field_checkbox
                if ($items['#checkboxes'  ][1]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][1]->title))->render() ]) ); # …\field_checkbox
                if ($items['#checkboxes'  ][2]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][2]->title))->render() ]) ); # …\field_checkbox
                if ($items['#checkboxes'  ][3]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#checkboxes'  ][3]->title))->render() ]) ); # …\field_checkbox
                if ($items['#switcher'       ]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switcher'       ]->title))->render() ]) ); # …\field_switcher
                if ($items['#switchers'   ][0]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][0]->title))->render() ]) ); # …\field_switchers
                if ($items['#switchers'   ][1]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][1]->title))->render() ]) ); # …\field_switchers
                if ($items['#switchers'   ][2]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][2]->title))->render() ]) ); # …\field_switchers
                if ($items['#switchers'   ][3]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#switchers'   ][3]->title))->render() ]) ); # …\field_switchers
                if ($items['#radiobutton'    ]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobutton'    ]->title))->render() ]) ); # …\field_radiobutton
                if ($items['#radiobuttons'][0]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobuttons'][0]->title))->render() ]) ); # …\field_radiobutton
                if ($items['#radiobuttons'][1]->checked_get()    !== true                          ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobuttons'][1]->title))->render() ]) ); # …\field_radiobutton
                if ($items['#radiobuttons'][2]->checked_get()    !== false                         ) message::insert( new text('Field "%%_title" has a changed value.', ['title' => (new text($items['#radiobuttons'][2]->title))->render() ]) ); # …\field_radiobutton
                if (serialize($items['*access']->value_get())    !== serialize($value_def_access)  ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*access'         ]->title))->render() ]) ); # …\widget_access
                if ($items['*checkboxes'     ]->value_get  ()    !== $value_def_checkboxes         ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*checkboxes'     ]->title))->render() ]) ); # …\group_checkboxes
                if ($items['*switchers'      ]->value_get  ()    !== $value_def_switchers          ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*switchers'      ]->title))->render() ]) ); # …\group_switchers
                if ($items['*radiobuttons'   ]->value_get  ()    !== 'radiobutton_2'               ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*radiobuttons'   ]->title))->render() ]) ); # …\group_radiobuttons
                if ($items['*palette_color'  ]->value_get  ()    !== 'transparent'                 ) message::insert( new text('Group "%%_title" has a changed value.', ['title' => (new text($items['*palette_color'  ]->title))->render() ]) ); # …\group_palette
                # save the files
                $paths = [];
                $paths['texts'            ] = $items['#file_text'     ]->value_get();
                $paths['pictures'         ] = $items['#file_picture'  ]->value_get();
                $paths['audios'           ] = $items['#file_audio'    ]->value_get();
                $paths['videos'           ] = $items['#file_video'    ]->value_get();
                $paths[   'texts_multiple'] = $items['*files_texts'   ]->value_get();
                $paths['pictures_multiple'] = $items['*files_pictures']->value_get();
                $paths[  'audios_multiple'] = $items['*files_audios'  ]->value_get();
                $paths[  'videos_multiple'] = $items['*files_videos'  ]->value_get();
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

}
