<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\module;
          use \effcore\page;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    $settings = module::settings_get('page');
  # colors
    $colors = color::get_all();
    $color_result = null;
    switch ($name) {
      case 'color_page'            : $color_result = $colors[$settings->color_page_id            ]; break;
      case 'color_text'            : $color_result = $colors[$settings->color_text_id            ]; break;
      case 'color_main'            : $color_result = $colors[$settings->color_main_id            ]; break;
      case 'color_link'            : $color_result = $colors[$settings->color_link_id            ]; break;
      case 'color_link_active'     : $color_result = $colors[$settings->color_link_active_id     ]; break;
      case 'color_table_row_odd'   : $color_result = $colors[$settings->color_table_row_odd_id   ]; break;
      case 'color_table_row_even'  : $color_result = $colors[$settings->color_table_row_even_id  ]; break;
      case 'color_relation'        : $color_result = $colors[$settings->color_relation_id        ]; break;
      case 'color_menu'            : $color_result = $colors[$settings->color_menu_id            ]; break;
      case 'color_menu_active'     : $color_result = $colors[$settings->color_menu_active_id     ]; break;
      case 'color_menu_text'       : $color_result = $colors[$settings->color_menu_text_id       ]; break;
      case 'color_menu_link'       : $color_result = $colors[$settings->color_menu_link_id       ]; break;
      case 'color_menu_link_active': $color_result = $colors[$settings->color_menu_link_active_id]; break;
      case 'color_ok'              : $color_result = $colors[$settings->color_ok_id              ]; break;
      case 'color_warning'         : $color_result = $colors[$settings->color_warning_id         ]; break;
      case 'color_error'           : $color_result = $colors[$settings->color_error_id           ]; break;
      case 'color_fieldset'        : $color_result = $colors[$settings->color_fieldset_id        ]; break;
      case 'color_fieldset_nested' : $color_result = $colors[$settings->color_fieldset_nested_id ]; break;
      case 'color_field'           : $color_result = $colors[$settings->color_field_id           ]; break;
      case 'color_field_text'      : $color_result = $colors[$settings->color_field_text_id      ]; break;
      case 'color_button'          : $color_result = $colors[$settings->color_button_id          ]; break;
      case 'color_button_active'   : $color_result = $colors[$settings->color_button_active_id   ]; break;
      case 'color_button_text'     : $color_result = $colors[$settings->color_button_text_id     ]; break;}
    if ($color_result) {
      if (!empty($color_result->value) && count($args) == 0)   return $color_result->value;
      if (!empty($color_result->value) && count($args) == 3 && !empty($color_result->value_hex)) {
        $color = ltrim($color_result->value_hex, '#');
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
  # instance_id
    if ($name == 'instance_id_context') {
      return page::get_current()->args_get('instance_id');
    }

  }

}}