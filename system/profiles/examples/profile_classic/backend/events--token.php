<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\color;
          use \effcore\module;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    $settings = module::settings_get('profile_classic');
    $colors = color::get_all();
    if ($name === 'color_custom__head' || $name === 'color_custom__foot') {
      if ($name === 'color_custom__head') $color = $colors[$settings->color_custom__head_id] ?? $colors['white'];
      if ($name === 'color_custom__foot') $color = $colors[$settings->color_custom__foot_id] ?? $colors['white'];
      if (empty($color->value_hex) === true) return 'transparent';
      if (empty($color->value_hex) !== true && count($args) === 0) return $color->value_hex;
      if (empty($color->value_hex) !== true && count($args) === 3) return $color->filter_shift($args[0], $args[1], $args[2], 1, color::return_hex);
      if (empty($color->value_hex) !== true && count($args) === 4) return $color->filter_shift($args[0], $args[1], $args[2], $args[3], color::return_rgba);
    }
  }

}}