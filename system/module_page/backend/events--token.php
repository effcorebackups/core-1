<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\color;
use effcore\color_preset;
use effcore\module;
use effcore\request;
use effcore\page;
use effcore\token;

abstract class events_token {

    static function on_apply($name, $args = []) {
        $settings = module::settings_get('page');
        switch ($name) {
            case 'page_id_context'              : return page::get_current() ? page::get_current()->id : null;
            case 'thumbnail_width_small'        : return $settings->thumbnail_width_small;
            case 'thumbnail_width_middle'       : return $settings->thumbnail_width_middle;
            case 'thumbnail_width_big'          : return $settings->thumbnail_width_big;
            case 'thumbnail_path_cover_default' : return $settings->thumbnail_path_cover_default;
            case 'thumbnail_path_poster_default': return $settings->thumbnail_path_poster_default;
            case 'page_width_min'               : return $settings->page_width_min;
            case 'page_width_max'               : return $settings->page_width_max;
            case 'page_width_min_context':
            case 'page_width_max_context':
                $page_id = request::value_get('page_id', 0, '_GET');
                $page = is_string($page_id) ? page::get_by_id($page_id, true) : null;
                if ($name === 'page_width_min_context') return !empty($page->data['width_min']) ? $page->data['width_min'] : $settings->page_width_min;
                if ($name === 'page_width_max_context') return !empty($page->data['width_max']) ? $page->data['width_max'] : $settings->page_width_max;
                break;
        }
        # colors
        if ($name === 'return_token_color_encode') {
            if (count($args) === 1) {
                if ( (strpos($args[0], 'color__')        === 0 ||
                      strpos($args[0], 'color_custom__') === 0) &&
                      strpos($args[0], '%%') === false) {
                    $value = token::apply('%%_'.$args[0]);
                    if ($value) {
                        return urlencode($value);
                    }
                }
            }
        }
        if ($name === 'return_if_token_color_is_dark') {
            if (count($args) === 3) {
                if ( (strpos($args[0], 'color__')        === 0 ||
                      strpos($args[0], 'color_custom__') === 0) &&
                      strpos($args[0], '%%') === false) {
                    $value = token::apply('%%_'.$args[0]);
                    if ($value) {
                        $is_dark = (new color(null, $value))->is_dark();
                        if ($is_dark !== null) {
                            return $is_dark ? $args[1] :
                                              $args[2];
                        }
                    }
                }
            }
        }
        if (strpos($name, 'color__') === 0) {
            $colors = color::get_all();
            $is_all_colors_available = color_preset::is_all_colors_available();
            if ($name === 'color__page'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__page_id                   : 'default_1'    ];
            if ($name === 'color__text'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__text_id                   : 'white'        ];
            if ($name === 'color__main'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__main_id                   : 'lightblue_l1' ];
            if ($name === 'color__link'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__link_id                   : 'white'        ];
            if ($name === 'color__link_active'           ) $color = $colors[ $is_all_colors_available ? $settings->color__link_active_id            : 'gold_r1'      ];
            if ($name === 'color__table_row_odd'         ) $color = $colors[ $is_all_colors_available ? $settings->color__table_row_odd_id          : 'default_2'    ];
            if ($name === 'color__table_row_even'        ) $color = $colors[ $is_all_colors_available ? $settings->color__table_row_even_id         : 'default_3'    ];
            if ($name === 'color__menu'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_id                   : 'default_2'    ];
            if ($name === 'color__menu_active'           ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_active_id            : 'default_3'    ];
            if ($name === 'color__menu_text'             ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_text_id              : 'white'        ];
            if ($name === 'color__menu_link'             ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_link_id              : 'white'        ];
            if ($name === 'color__menu_link_active'      ) $color = $colors[ $is_all_colors_available ? $settings->color__menu_link_active_id       : 'gold_r1'      ];
            if ($name === 'color__tabs'                  ) $color = $colors[ $is_all_colors_available ? $settings->color__tabs_id                   : 'lightblue_l1' ];
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
            if ($name === 'color__button'                ) $color = $colors[ $is_all_colors_available ? $settings->color__button_id                 : 'lightblue_l1' ];
            if ($name === 'color__button_active'         ) $color = $colors[ $is_all_colors_available ? $settings->color__button_active_id          : 'lightblue_l2' ];
            if ($name === 'color__button_text'           ) $color = $colors[ $is_all_colors_available ? $settings->color__button_text_id            : 'white'        ];
            if (empty($color->value_hex) === true) return 'transparent';
            if (empty($color->value_hex) !== true && count($args) === 0) return $color->value_hex;
            if (empty($color->value_hex) !== true && count($args) === 3) return $color->filter_shift((int)$args[0], (int)$args[1], (int)$args[2], 1, color::RETURN_HEX);
            if (empty($color->value_hex) !== true && count($args) === 4) return $color->filter_shift((int)$args[0], (int)$args[1], (int)$args[2], (float)$args[3], color::RETURN_RGBA);
        }
    }

}
