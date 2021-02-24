<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color_preset;
          use \effcore\message;
          use \effcore\page;
          abstract class events_form_colors_preset {

  static function on_init($event, $form, $items) {
    $id = page::get_current()->args_get('id');
    $preset = color_preset::get($id);
    if ($preset) {
      $items['#color_page_id'                  ]->color_set($preset->colors->color_page_id                  );
      $items['#color_text_id'                  ]->color_set($preset->colors->color_text_id                  );
      $items['#color_main_id'                  ]->color_set($preset->colors->color_main_id                  );
      $items['#color_link_id'                  ]->color_set($preset->colors->color_link_id                  );
      $items['#color_link_active_id'           ]->color_set($preset->colors->color_link_active_id           );
      $items['#color_table_row_odd_id'         ]->color_set($preset->colors->color_table_row_odd_id         );
      $items['#color_table_row_even_id'        ]->color_set($preset->colors->color_table_row_even_id        );
      $items['#color_relation_id'              ]->color_set($preset->colors->color_relation_id              );
      $items['#color_menu_id'                  ]->color_set($preset->colors->color_menu_id                  );
      $items['#color_menu_active_id'           ]->color_set($preset->colors->color_menu_active_id           );
      $items['#color_menu_text_id'             ]->color_set($preset->colors->color_menu_text_id             );
      $items['#color_menu_link_id'             ]->color_set($preset->colors->color_menu_link_id             );
      $items['#color_menu_link_active_id'      ]->color_set($preset->colors->color_menu_link_active_id      );
      $items['#color_tabs_id'                  ]->color_set($preset->colors->color_tabs_id                  );
      $items['#color_tabs_link_id'             ]->color_set($preset->colors->color_tabs_link_id             );
      $items['#color_tabs_link_active_id'      ]->color_set($preset->colors->color_tabs_link_active_id      );
      $items['#color_tabs_link_active_no_bg_id']->color_set($preset->colors->color_tabs_link_active_no_bg_id);
      $items['#color_ok_id'                    ]->color_set($preset->colors->color_ok_id                    );
      $items['#color_warning_id'               ]->color_set($preset->colors->color_warning_id               );
      $items['#color_error_id'                 ]->color_set($preset->colors->color_error_id                 );
      $items['#color_fieldset_id'              ]->color_set($preset->colors->color_fieldset_id              );
      $items['#color_fieldset_nested_id'       ]->color_set($preset->colors->color_fieldset_nested_id       );
      $items['#color_field_id'                 ]->color_set($preset->colors->color_field_id                 );
      $items['#color_field_text_id'            ]->color_set($preset->colors->color_field_text_id            );
      $items['#color_button_id'                ]->color_set($preset->colors->color_button_id                );
      $items['#color_button_active_id'         ]->color_set($preset->colors->color_button_active_id         );
      $items['#color_button_text_id'           ]->color_set($preset->colors->color_button_text_id           );
    } else $items['~apply']->disabled_set(true);
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $id = page::get_current()->args_get('id');
        $preset = color_preset::get($id);
        if ($preset) {
          $changes = [];
          if ($items['#color_page_id'                  ]->checked_get()) $changes['color_page_id'                  ] = true;
          if ($items['#color_text_id'                  ]->checked_get()) $changes['color_text_id'                  ] = true;
          if ($items['#color_main_id'                  ]->checked_get()) $changes['color_main_id'                  ] = true;
          if ($items['#color_link_id'                  ]->checked_get()) $changes['color_link_id'                  ] = true;
          if ($items['#color_link_active_id'           ]->checked_get()) $changes['color_link_active_id'           ] = true;
          if ($items['#color_table_row_odd_id'         ]->checked_get()) $changes['color_table_row_odd_id'         ] = true;
          if ($items['#color_table_row_even_id'        ]->checked_get()) $changes['color_table_row_even_id'        ] = true;
          if ($items['#color_relation_id'              ]->checked_get()) $changes['color_relation_id'              ] = true;
          if ($items['#color_menu_id'                  ]->checked_get()) $changes['color_menu_id'                  ] = true;
          if ($items['#color_menu_active_id'           ]->checked_get()) $changes['color_menu_active_id'           ] = true;
          if ($items['#color_menu_text_id'             ]->checked_get()) $changes['color_menu_text_id'             ] = true;
          if ($items['#color_menu_link_id'             ]->checked_get()) $changes['color_menu_link_id'             ] = true;
          if ($items['#color_menu_link_active_id'      ]->checked_get()) $changes['color_menu_link_active_id'      ] = true;
          if ($items['#color_tabs_id'                  ]->checked_get()) $changes['color_tabs_id'                  ] = true;
          if ($items['#color_tabs_link_id'             ]->checked_get()) $changes['color_tabs_link_id'             ] = true;
          if ($items['#color_tabs_link_active_id'      ]->checked_get()) $changes['color_tabs_link_active_id'      ] = true;
          if ($items['#color_tabs_link_active_no_bg_id']->checked_get()) $changes['color_tabs_link_active_no_bg_id'] = true;
          if ($items['#color_ok_id'                    ]->checked_get()) $changes['color_ok_id'                    ] = true;
          if ($items['#color_warning_id'               ]->checked_get()) $changes['color_warning_id'               ] = true;
          if ($items['#color_error_id'                 ]->checked_get()) $changes['color_error_id'                 ] = true;
          if ($items['#color_fieldset_id'              ]->checked_get()) $changes['color_fieldset_id'              ] = true;
          if ($items['#color_fieldset_nested_id'       ]->checked_get()) $changes['color_fieldset_nested_id'       ] = true;
          if ($items['#color_field_id'                 ]->checked_get()) $changes['color_field_id'                 ] = true;
          if ($items['#color_field_text_id'            ]->checked_get()) $changes['color_field_text_id'            ] = true;
          if ($items['#color_button_id'                ]->checked_get()) $changes['color_button_id'                ] = true;
          if ($items['#color_button_active_id'         ]->checked_get()) $changes['color_button_active_id'         ] = true;
          if ($items['#color_button_text_id'           ]->checked_get()) $changes['color_button_text_id'           ] = true;
          if (!count($changes)) {
            message::insert('No one item was selected!', 'warning');
          } else {
            $result = color_preset::apply($id, $changes, true);
            if ($result) message::insert('Colors was applied.'             );
            else         message::insert('Colors was not applied!', 'error');
            static::on_init(null, $form, $items);
          }
        }
        break;
    }
  }

}}