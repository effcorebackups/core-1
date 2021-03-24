<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\color_preset;
          use \effcore\module;
          use \effcore\page;
          use \effcore\url;
          abstract class events_token {

  static function on_apply($name, $args = []) {
    $settings = module::settings_get('page');
    switch ($name) {
      case 'thumbnail_small_width':  return $settings->thumbnail_small_width;
      case 'thumbnail_middle_width': return $settings->thumbnail_middle_width;
      case 'thumbnail_big_width':    return $settings->thumbnail_big_width;
      case 'page_min_width':         return $settings->page_min_width;
      case 'page_max_width':         return $settings->page_max_width;
      case 'css_page_min_width_context':
      case 'css_page_max_width_context':
        $page_id = url::get_current()->query_arg_select('page_id');
        $page = is_string($page_id) ? page::get_by_id($page_id) : null;
        if ($name === 'css_page_min_width_context' && !empty($page->data['width_min'])) return 'min-width: '.$page->data['width_min'].'px /* induvidual page size */';
        if ($name === 'css_page_max_width_context' && !empty($page->data['width_max'])) return 'max-width: '.$page->data['width_max'].'px /* induvidual page size */';
        break;
    }
  # colors
    if (substr($name, 0, 7) === 'color__') {
      $colors = color::get_all();
      $is_all_colors_available = color_preset::is_all_colors_available();
      if ($name === 'color__page'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__page_id                   : 'default_1'    ];
      if ($name === 'color__text'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__text_id                   : 'white'        ];
      if ($name === 'color__main'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__main_id                   : 'default_4'    ];
      if ($name === 'color__link'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__link_id                   : 'white'        ];
      if ($name === 'color__link_active'           ) $color = $colors[ $is_all_colors_available ? $settings->color__link_active_id            : 'gold_r1'      ];
      if ($name === 'color__table_row_odd'         ) $color = $colors[ $is_all_colors_available ? $settings->color__table_row_odd_id          : 'default_2'    ];
      if ($name === 'color__table_row_even'        ) $color = $colors[ $is_all_colors_available ? $settings->color__table_row_even_id         : 'default_3'    ];
      if ($name === 'color__relation'              ) $color = $colors[ $is_all_colors_available ? $settings->color__relation_id               : 'white'        ];
      if ($name === 'color__menu'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_id                   : 'default_2'    ];
      if ($name === 'color__menu_active'           ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_active_id            : 'default_3'    ];
      if ($name === 'color__menu_text'             ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_text_id              : 'white'        ];
      if ($name === 'color__menu_link'             ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_link_id              : 'white'        ];
      if ($name === 'color__menu_link_active'      ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_link_active_id       : 'gold_r1'      ];
      if ($name === 'color__tabs'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__tabs_id                   : 'default_4'    ];
      if ($name === 'color__tabs_link'             ) $color = $colors[ $is_all_colors_available ? $settings->color__tabs_link_id              : 'white'        ];
      if ($name === 'color__tabs_link_active'      ) $color = $colors[ $is_all_colors_available ? $settings->color__tabs_link_active_id       : 'white'        ];
      if ($name === 'color__tabs_link_active_no_bg') $color = $colors[ $is_all_colors_available ? $settings->color__tabs_link_active_no_bg_id : 'gold_r1'      ];
      if ($name === 'color__ok'                    ) $color = $colors[ $is_all_colors_available ? $settings->color__ok_id                     : 'state_ok'     ];
      if ($name === 'color__warning'               ) $color = $colors[ $is_all_colors_available ? $settings->color__warning_id                : 'state_warning'];
      if ($name === 'color__error'                 ) $color = $colors[ $is_all_colors_available ? $settings->color__error_id                  : 'state_error'  ];
      if ($name === 'color__fieldset'              ) $color = $colors[ $is_all_colors_available ? $settings->color__fieldset_id               : 'default_2'    ];
      if ($name === 'color__fieldset_nested'       ) $color = $colors[ $is_all_colors_available ? $settings->color__fieldset_nested_id        : 'default_1'    ];
      if ($name === 'color__field'                 ) $color = $colors[ $is_all_colors_available ? $settings->color__field_id                  : 'default_1'    ];
      if ($name === 'color__field_text'            ) $color = $colors[ $is_all_colors_available ? $settings->color__field_text_id             : 'white'        ];
      if ($name === 'color__button'                ) $color = $colors[ $is_all_colors_available ? $settings->color__button_id                 : 'default_5'    ];
      if ($name === 'color__button_active'         ) $color = $colors[ $is_all_colors_available ? $settings->color__button_active_id          : 'default_6'    ];
      if ($name === 'color__button_text'           ) $color = $colors[ $is_all_colors_available ? $settings->color__button_text_id            : 'white'        ];
      if (!empty($color->value) && count($args) === 0) return $color->value;
      if (!empty($color->value) && count($args) === 3) return $color->filter_shift($args[0], $args[1], $args[2], 1, color::return_hex);
      if (!empty($color->value) && count($args) === 4) return $color->filter_shift($args[0], $args[1], $args[2], $args[3], color::return_rgba);
    }
  }

}}