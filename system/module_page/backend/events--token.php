<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\module;
          use \effcore\page;
          use \effcore\url;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    $settings = module::settings_get('page');
    switch ($name) {
      case     'page_min_width': return $settings->page_min_width;
      case     'page_max_width': return $settings->page_max_width;
      case 'css_page_min_width_context':
      case 'css_page_max_width_context':
        $page_id = url::get_current()->query_arg_select('page_id');
        $page = $page_id !== null ? page::get($page_id) : null;
        if ($name == 'css_page_min_width_context' && !empty($page->data['width_min'])) return 'min-width: '.$page->data['width_min'].'px /* induvidual page size */';
        if ($name == 'css_page_max_width_context' && !empty($page->data['width_max'])) return 'max-width: '.$page->data['width_max'].'px /* induvidual page size */';
        break;
    }
  # colors
    $colors = color::get_all();
    switch ($name) {
      case 'color_page'                  : $color = $colors[$settings->color_page_id                  ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_text'                  : $color = $colors[$settings->color_text_id                  ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_main'                  : $color = $colors[$settings->color_main_id                  ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'gray';
      case 'color_link'                  : $color = $colors[$settings->color_link_id                  ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_link_active'           : $color = $colors[$settings->color_link_active_id           ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'orange';
      case 'color_table_row_odd'         : $color = $colors[$settings->color_table_row_odd_id         ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_table_row_even'        : $color = $colors[$settings->color_table_row_even_id        ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_relation'              : $color = $colors[$settings->color_relation_id              ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_menu'                  : $color = $colors[$settings->color_menu_id                  ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_menu_active'           : $color = $colors[$settings->color_menu_active_id           ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'gray';
      case 'color_menu_text'             : $color = $colors[$settings->color_menu_text_id             ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_menu_link'             : $color = $colors[$settings->color_menu_link_id             ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_menu_link_active'      : $color = $colors[$settings->color_menu_link_active_id      ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'orange';
      case 'color_tabs'                  : $color = $colors[$settings->color_tabs_id                  ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_tabs_link'             : $color = $colors[$settings->color_tabs_link_id             ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_tabs_link_active'      : $color = $colors[$settings->color_tabs_link_active_id      ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_tabs_link_active_no_bg': $color = $colors[$settings->color_tabs_link_active_no_bg_id] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'orange';
      case 'color_ok'                    : $color = $colors[$settings->color_ok_id                    ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'green';
      case 'color_warning'               : $color = $colors[$settings->color_warning_id               ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'orange';
      case 'color_error'                 : $color = $colors[$settings->color_error_id                 ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'red';
      case 'color_fieldset'              : $color = $colors[$settings->color_fieldset_id              ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'lightgray';
      case 'color_fieldset_nested'       : $color = $colors[$settings->color_fieldset_nested_id       ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_field'                 : $color = $colors[$settings->color_field_id                 ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';
      case 'color_field_text'            : $color = $colors[$settings->color_field_text_id            ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_button'                : $color = $colors[$settings->color_button_id                ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'black';
      case 'color_button_active'         : $color = $colors[$settings->color_button_active_id         ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'gray';
      case 'color_button_text'           : $color = $colors[$settings->color_button_text_id           ] ?? null; if (!empty($color->value) && count($args) == 0) return $color->value; if (!empty($color->value) && count($args) == 3) return static::color_shifted_value_get($color, $args[0], $args[1], $args[2]); return 'white';}
  }

  static function color_shifted_value_get($color, $r_offset, $g_offset, $b_offset) {
    $rgb = $color->rgb_get();
    if ($rgb) {
      $new_r = max(min($rgb['r'] + (int)$r_offset, 255), 0);
      $new_g = max(min($rgb['g'] + (int)$g_offset, 255), 0);
      $new_b = max(min($rgb['b'] + (int)$b_offset, 255), 0);
      return '#'.str_pad(dechex($new_r), 2, '0', STR_PAD_LEFT).
                 str_pad(dechex($new_g), 2, '0', STR_PAD_LEFT).
                 str_pad(dechex($new_b), 2, '0', STR_PAD_LEFT);
    }
  }

}}