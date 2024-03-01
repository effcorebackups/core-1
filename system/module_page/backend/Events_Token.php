<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Color;
use effcore\Color_profile;
use effcore\Module;
use effcore\Request;
use effcore\Page;

abstract class Events_Token {

    static function on_apply($name, $args) {
        $settings = Module::settings_get('page');
        if ($name === 'page_arg_context') {
            if (Page::get_current() && $args->get(0) !== null)
                 return Page::get_current()->args_get($args->get(0));
            else return                               $args->get(1);
        }

        if ($name === 'page_width_min') {
            if ($args->get(0) === 'context') {
                $page_id = Request::value_get('page_id', 0, '_GET');
                $page = is_string($page_id) && strlen($page_id) ? Page::get_by_id($page_id, true) : null;
                return !empty($page->data['width_min']) ?
                              $page->data['width_min'] : $settings->page_width_min; }
            return $settings->page_width_min;
        }

        if ($name === 'page_width_max') {
            if ($args->get(0) === 'context') {
                $page_id = Request::value_get('page_id', 0, '_GET');
                $page = is_string($page_id) && strlen($page_id) ? Page::get_by_id($page_id, true) : null;
                return !empty($page->data['width_max']) ?
                              $page->data['width_max'] : $settings->page_width_max; }
            return $settings->page_width_max;
        }

        switch ($name) {
            case 'page_id_context'              : return Page::get_current() ? Page::get_current()->id : null;
            case 'thumbnail_width_small'        : return $settings->thumbnail_width_small;
            case 'thumbnail_width_middle'       : return $settings->thumbnail_width_middle;
            case 'thumbnail_width_big'          : return $settings->thumbnail_width_big;
            case 'thumbnail_path_cover_default' : return $settings->thumbnail_path_cover_default;
            case 'thumbnail_path_poster_default': return $settings->thumbnail_path_poster_default;
            case 'page_width_mobile'            : return $settings->page_width_mobile;
        }

        if ($name === 'color') {
            $scope      = $args->get(0);
            $is_encoded = $args->get_named('encoded');
            $profile_id = $args->get_named('profile') ?: Color_profile::get_current()->id;
            $color_id   = Color_profile::get_color_info($profile_id, $scope)->color_id ?? '';
            $color      = Color::get($color_id);
            if ($color === null                                     ) return 'none';
            if ($color !== null && empty($color->value_hex) === true) return 'transparent';
            if ($color !== null && empty($color->value_hex) !== true) {
                $shift_r = ( int )$args->get_named('r');
                $shift_g = ( int )$args->get_named('g');
                $shift_b = ( int )$args->get_named('b');
                $opacity = (float)$args->get_named('o');
                if ($shift_r === 0 && $shift_g === 0 && $shift_b === 0 && $opacity === 0.0) return $is_encoded ? rawurlencode($color->value_hex) : $color->value_hex;
                if ($shift_r !== 0 || $shift_g !== 0 || $shift_b !== 0 || $opacity !== 0.0) {
                    if ($opacity === 0.0) {$result = $color->filter_shift($shift_r, $shift_g, $shift_b, 1       , Color::RETURN_HEX ); return $is_encoded ? rawurlencode($result) : $result;}
                    if ($opacity !== 0.0) {$result = $color->filter_shift($shift_r, $shift_g, $shift_b, $opacity, Color::RETURN_RGBA); return $is_encoded ? rawurlencode($result) : $result;}
                }
            }
        }

        if ($name === 'return_if_scope_is_dark') {
            $scope      = $args->get(0);
            $profile_id = $args->get_named('profile') ?: Color_profile::get_current()->id;
            $color_id   = Color_profile::get_color_info($profile_id, $scope)->color_id ?? '';
            $color      = Color::get($color_id);
            if ($color) {
                if ($color->is_dark())
                     return $args->get(1) !== null ? $args->get(1) : '';
                else return $args->get(2) !== null ? $args->get(2) : '';
            }
        }
    }

}
