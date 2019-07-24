<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\module;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    $result   = null;
    $settings = module::settings_get('page');
    $colors   = color::get_all();
    switch ($name) {
      case 'color_page'            : $result = $colors[$settings->color_page_id            ]; break;
      case 'color_text'            : $result = $colors[$settings->color_text_id            ]; break;
      case 'color_main'            : $result = $colors[$settings->color_main_id            ]; break;
      case 'color_link'            : $result = $colors[$settings->color_link_id            ]; break;
      case 'color_link_active'     : $result = $colors[$settings->color_link_active_id     ]; break;
      case 'color_table_row_odd'   : $result = $colors[$settings->color_table_row_odd_id   ]; break;
      case 'color_table_row_even'  : $result = $colors[$settings->color_table_row_even_id  ]; break;
      case 'color_relation'        : $result = $colors[$settings->color_relation_id        ]; break;
      case 'color_menu'            : $result = $colors[$settings->color_menu_id            ]; break;
      case 'color_menu_active'     : $result = $colors[$settings->color_menu_active_id     ]; break;
      case 'color_menu_text'       : $result = $colors[$settings->color_menu_text_id       ]; break;
      case 'color_menu_link'       : $result = $colors[$settings->color_menu_link_id       ]; break;
      case 'color_menu_link_active': $result = $colors[$settings->color_menu_link_active_id]; break;
      case 'color_ok'              : $result = $colors[$settings->color_ok_id              ]; break;
      case 'color_warning'         : $result = $colors[$settings->color_warning_id         ]; break;
      case 'color_error'           : $result = $colors[$settings->color_error_id           ]; break;
      case 'color_fieldset'        : $result = $colors[$settings->color_fieldset_id        ]; break;
      case 'color_fieldset_nested' : $result = $colors[$settings->color_fieldset_nested_id ]; break;
      case 'color_field'           : $result = $colors[$settings->color_field_id           ]; break;
      case 'color_field_text'      : $result = $colors[$settings->color_field_text_id      ]; break;
      case 'color_button'          : $result = $colors[$settings->color_button_id          ]; break;
      case 'color_button_active'   : $result = $colors[$settings->color_button_active_id   ]; break;
      case 'color_button_text'     : $result = $colors[$settings->color_button_text_id     ]; break;}
    if (!empty($result->value) && count($args) == 0)   return $result->value;
    if (!empty($result->value) && count($args) == 3 && !empty($result->value_hex)) {
      $color = ltrim($result->value_hex, '#');
      $color_parts = [];
      if (strlen($color) == 6) {$color_parts = str_split($color, 2);                                                                                                         }
      if (strlen($color) == 3) {$color_parts = str_split($color, 1); $color_parts[0].= $color_parts[0]; $color_parts[1].= $color_parts[1]; $color_parts[2].= $color_parts[2];}
      if (!empty($color_parts)) {
        $r = max(min((int)hexdec($color_parts[0]) + (int)$args[0], 255), 0);
        $g = max(min((int)hexdec($color_parts[1]) + (int)$args[1], 255), 0);
        $b = max(min((int)hexdec($color_parts[2]) + (int)$args[2], 255), 0);
        return '#'.str_pad(dechex($r), 2, '0', STR_PAD_LEFT).
                   str_pad(dechex($g), 2, '0', STR_PAD_LEFT).
                   str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
      }
    }
  }

}}