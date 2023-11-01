<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\demo;

use const effcore\NL;
use effcore\Core;
use effcore\Data;
use effcore\Message;
use effcore\Request;
use effcore\Text;
use stdClass;

abstract class Events_Form_Demo {

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
            Request::values_reset();
        }
        $items['*palette_color']->value_set('transparent');
        $paths = Data::select('demo_files');
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
                Message::insert(
                    new Text('Call "%%_call" on click "%%_click"', ['call' => '\\'.__METHOD__, 'click' => (new Text('send'))->render()])
                );
                break;
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'send':
                Message::insert(
                    new Text('Call "%%_call" on click "%%_click"', ['call' => '\\'.__METHOD__, 'click' => (new Text('send'))->render()])
                );
                $value_def_access = (object)['roles' => ['demo' => 'demo']];
                $value_def_checkboxes = Core::array_keys_map(['checkbox_2', 'checkbox_4']);
                $value_def_switchers  = Core::array_keys_map(['switcher_2', 'switcher_4']);
                $value_def_email = 'test1@example.com,test2@example.com';
                $value_def_select          =  'option_1';
                $value_def_select_multiple = ['option_1' => 'Option 1 (selected)'];
                $value_def_textarea = 'text in text area line 1'.NL.'text in text area line 2'.NL.'text in text area line 3';
                $value_def_textarea_data_hash = '7d74fec8f2ea1eb227a25b0d0bdd0fa2';
                if ($items['#text'           ]->value_get  ()    !== 'text in input'               ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#text'           ]->title))->render() ]) ); # …\Field_Text
                if ($items['#search'         ]->value_get  ()    !== 'text in search'              ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#search'         ]->title))->render() ]) ); # …\Field_Search
                if ($items['#url'            ]->value_get  ()    !== 'http://domain/path?q=w#p1'   ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#url'            ]->title))->render() ]) ); # …\Field_URL
                if ($items['#email'          ]->value_get  ()    !== $value_def_email              ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#email'          ]->title))->render() ]) ); # …\Field_Email
                if ($items['#tel'            ]->value_get  ()    !== '+000112334455'               ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#tel'            ]->title))->render() ]) ); # …\Field_Tel
                if ($items['#nickname'       ]->value_get  ()    !== 'user'                        ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#nickname'       ]->title))->render() ]) ); # …\Field_Nickname
                if ($items['#password'       ]->value_get(false) !== 'text in password'            ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#password'       ]->title))->render() ]) ); # …\Field_Password
                if ($items['#number'         ]->value_get  ()    !== '0'                           ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#number'         ]->title))->render() ]) ); # …\Field_Number
                if ($items['#range'          ]->value_get  ()    !== '0'                           ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#range'          ]->title))->render() ]) ); # …\Field_Range
                if ($items['#color'          ]->value_get  ()    !== '#ffffff'                     ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#color'          ]->title))->render() ]) ); # …\Field_Color
                if ($items['#time'           ]->value_get  ()    !== '01:23:45'                    ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#time'           ]->title))->render() ]) ); # …\Field_Time
                if ($items['#date'           ]->value_get  ()    !== '2030-02-01'                  ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#date'           ]->title))->render() ]) ); # …\Field_Date
                if ($items['#datetime'       ]->value_get  ()    !== '2030-02-01 01:23:45'         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#datetime'       ]->title))->render() ]) ); # …\Field_DateTime
                if ($items['#datetime_local' ]->value_get  ()    !== '2030-01-02 12:00:00'         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#datetime_local' ]->title))->render() ]) ); # …\Field_DateTime_local
                if ($items['#timezone'       ]->value_get  ()    !== 'UTC'                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#timezone'       ]->title))->render() ]) ); # …\Field_Select_timezone
                if ($items['#select'         ]->value_get  ()    !== $value_def_select             ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#select'         ]->title))->render() ]) ); # …\Field_Select
                if ($items['#select_multiple']->value_get  ()    !== $value_def_select_multiple    ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#select_multiple']->title))->render() ]) ); # …\Field_Select
                if ($items['#logic'          ]->value_get  ()    !== 1                             ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#logic'          ]->title))->render() ]) ); # …\Field_Select_logic
                if ($items['#relation'       ]->value_get  ()    !== 'demo_sql_item_1'             ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#relation'       ]->title))->render() ]) ); # …\Field_Select_relation
                if ($items['#relation_tree'  ]->value_get  ()    !== 'demo_sql_item_1'             ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#relation_tree'  ]->title))->render() ]) ); # …\Field_Select_relation
                if ($items['#lang_code'      ]->value_get  ()    !== 'en'                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#lang_code'      ]->title))->render() ]) ); # …\Field_Select_language
                if ($items['#text_direction' ]->value_get  ()    !== 'ltr'                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#text_direction' ]->title))->render() ]) ); # …\Field_Select_text_direction
                if ($items['#textarea'       ]->value_get  ()    !== $value_def_textarea           ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#textarea'       ]->title))->render() ]) ); # …\Field_Textarea
                if (md5($items['#textarea_data']->value_get())   !== $value_def_textarea_data_hash ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#textarea_data'  ]->title))->render() ]) ); # …\Field_Textarea_data
                if ($items['#checkbox'       ]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#checkbox'       ]->title))->render() ]) ); # …\Field_Checkbox
                if ($items['#checkboxes'  ][0]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#checkboxes'  ][0]->title))->render() ]) ); # …\Field_Checkbox
                if ($items['#checkboxes'  ][1]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#checkboxes'  ][1]->title))->render() ]) ); # …\Field_Checkbox
                if ($items['#checkboxes'  ][2]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#checkboxes'  ][2]->title))->render() ]) ); # …\Field_Checkbox
                if ($items['#checkboxes'  ][3]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#checkboxes'  ][3]->title))->render() ]) ); # …\Field_Checkbox
                if ($items['#switcher'       ]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#switcher'       ]->title))->render() ]) ); # …\Field_Switcher
                if ($items['#switchers'   ][0]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#switchers'   ][0]->title))->render() ]) ); # …\Field_Switchers
                if ($items['#switchers'   ][1]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#switchers'   ][1]->title))->render() ]) ); # …\Field_Switchers
                if ($items['#switchers'   ][2]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#switchers'   ][2]->title))->render() ]) ); # …\Field_Switchers
                if ($items['#switchers'   ][3]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#switchers'   ][3]->title))->render() ]) ); # …\Field_Switchers
                if ($items['#radiobutton'    ]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#radiobutton'    ]->title))->render() ]) ); # …\Field_Radiobutton
                if ($items['#radiobuttons'][0]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#radiobuttons'][0]->title))->render() ]) ); # …\Field_Radiobutton
                if ($items['#radiobuttons'][1]->checked_get()    !== true                          ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#radiobuttons'][1]->title))->render() ]) ); # …\Field_Radiobutton
                if ($items['#radiobuttons'][2]->checked_get()    !== false                         ) Message::insert( new Text('Value of "%%_title" field is changed!', ['title' => (new Text($items['#radiobuttons'][2]->title))->render() ]) ); # …\Field_Radiobutton
                if (serialize($items['*access']->value_get())    !== serialize($value_def_access)  ) Message::insert( new Text('Value of "%%_title" group is changed!', ['title' => (new Text($items['*access'         ]->title))->render() ]) ); # …\Widget_Access
                if ($items['*checkboxes'     ]->value_get  ()    !== $value_def_checkboxes         ) Message::insert( new Text('Value of "%%_title" group is changed!', ['title' => (new Text($items['*checkboxes'     ]->title))->render() ]) ); # …\Group_Checkboxes
                if ($items['*switchers'      ]->value_get  ()    !== $value_def_switchers          ) Message::insert( new Text('Value of "%%_title" group is changed!', ['title' => (new Text($items['*switchers'      ]->title))->render() ]) ); # …\Group_Switchers
                if ($items['*radiobuttons'   ]->value_get  ()    !== 'radiobutton_2'               ) Message::insert( new Text('Value of "%%_title" group is changed!', ['title' => (new Text($items['*radiobuttons'   ]->title))->render() ]) ); # …\Group_Radiobuttons
                if ($items['*palette_color'  ]->value_get  ()    !== 'transparent'                 ) Message::insert( new Text('Value of "%%_title" group is changed!', ['title' => (new Text($items['*palette_color'  ]->title))->render() ]) ); # …\Group_Palette
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
                if (count($paths)) Data::update('demo_files', $paths);
                else               Data::delete('demo_files');
                break;
        }
    }

}
