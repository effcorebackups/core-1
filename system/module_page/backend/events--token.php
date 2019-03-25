<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\module;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    $settings = module::settings_get('page');
    $colors   = color::all_get();
    switch ($name) {
      case 'color_page'            : return $colors[$settings->color_page_id            ]->value;
      case 'color_text'            : return $colors[$settings->color_text_id            ]->value;
      case 'color_main'            : return $colors[$settings->color_main_id            ]->value;
      case 'color_link'            : return $colors[$settings->color_link_id            ]->value;
      case 'color_link_active'     : return $colors[$settings->color_link_active_id     ]->value;
      case 'color_table_row_odd'   : return $colors[$settings->color_table_row_odd_id   ]->value;
      case 'color_table_row_even'  : return $colors[$settings->color_table_row_even_id  ]->value;
      case 'color_relation'        : return $colors[$settings->color_relation_id        ]->value;
      case 'color_menu'            : return $colors[$settings->color_menu_id            ]->value;
      case 'color_menu_active'     : return $colors[$settings->color_menu_active_id     ]->value;
      case 'color_menu_text'       : return $colors[$settings->color_menu_text_id       ]->value;
      case 'color_menu_link'       : return $colors[$settings->color_menu_link_id       ]->value;
      case 'color_menu_link_active': return $colors[$settings->color_menu_link_active_id]->value;
      case 'color_ok'              : return $colors[$settings->color_ok_id              ]->value;
      case 'color_warning'         : return $colors[$settings->color_warning_id         ]->value;
      case 'color_error'           : return $colors[$settings->color_error_id           ]->value;
      case 'color_fieldset'        : return $colors[$settings->color_fieldset_id        ]->value;
      case 'color_fieldset_nested' : return $colors[$settings->color_fieldset_nested_id ]->value;
      case 'color_field'           : return $colors[$settings->color_field_id           ]->value;
      case 'color_field_text'      : return $colors[$settings->color_field_text_id      ]->value;
      case 'color_button'          : return $colors[$settings->color_button_id          ]->value;
      case 'color_button_active'   : return $colors[$settings->color_button_active_id   ]->value;
      case 'color_button_text'     : return $colors[$settings->color_button_text_id     ]->value;
    }
  }

}}