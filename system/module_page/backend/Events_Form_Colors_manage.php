<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Color_profile;
use effcore\Color;
use effcore\Core;
use effcore\Group_Checkboxes_colors;
use effcore\Markup;
use effcore\Message;
use effcore\Security;
use effcore\Text;

abstract class Events_Form_Colors_manage {

    CONST APPROXIMATE_LIMIT      = 200;
    CONST BASE_COLOR_HEX_DEFAULT = '#7f7f7f';
    CONST MULTIPLIER_L_MIN       =  -1;
    CONST MULTIPLIER_L_DEFAULT   = -11;
    CONST MULTIPLIER_L_MAX       = -26;
    CONST MULTIPLIER_R_MIN       =  +1;
    CONST MULTIPLIER_R_DEFAULT   = +11;
    CONST MULTIPLIER_R_MAX       = +26;

    static function on_build($event, $form) {
        Color::cache_cleaning();
        $form->_dynamic_colors = Color::get_all('nosql-dynamic');
        $form->child_select('delete')->child_select('colors')->children_delete();
        $form->child_select('delete')->child_select('export')->children_delete();
        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
        if (!$form->child_select('insert')
                  ->child_select('colors')
                  ->child_select('group_colors')) {
            $values_new = [];
            foreach (static::_generate_palette() as $c_id => $c_color_hex) {
                $values_new[$c_id] = '';
            }
            $group_colors_insert = new Group_Checkboxes_colors($values_new);
            $group_colors_insert->title = 'Generated colors';
            $group_colors_insert->element_attributes['name'] = 'insert_colors[]';
            $form->child_select('insert')->child_select('colors')->child_insert(
                $group_colors_insert, 'group_colors'
            );
        }
        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
        if (count($form->_dynamic_colors)) {
            $values_old = [];
            foreach ($form->_dynamic_colors as $c_color) {
                $values_old[$c_color->id] = '';
            }
            $group_colors_delete = new Group_Checkboxes_colors($values_old);
            $group_colors_delete->title = 'Colors';
            $group_colors_delete->title_is_visible = false;
            $group_colors_delete->element_attributes['name'] = 'delete_colors[]';
            $form->child_select('delete')->child_select('colors')->child_insert(
                $group_colors_delete, 'group_colors'
            );
        } else {
            $form->child_select('delete')->child_select('colors')->child_insert(
                new Markup('x-no-items', [], 'No items.'), 'message_no_items'
            );
        }
    }

    static function on_init($event, $form, $items) {
        $current_color        = static::_get_current_color       ();
        $current_multiplier_l = static::_get_current_multiplier_l();
        $current_multiplier_r = static::_get_current_multiplier_r();
        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
        foreach (static::_generate_palette($current_color, $current_multiplier_l, $current_multiplier_r) as $c_id => $c_color_hex) {
            $c_is_exists = isset($form->_dynamic_colors['custom_'.ltrim($c_color_hex, '#')]);
            $items['#insert_colors:'.$c_id]->color_set($c_color_hex);
            $items['#insert_colors:'.$c_id]->disabled_set($c_is_exists);
            if ($c_is_exists) $items['#insert_colors:'.$c_id]->checked_set(false);
        }
        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
        $colors_statistics = Color_profile::get_colors_statistics(Color_profile::STATISTICS_MODE_COLOR_BY_PROFILE);
        if (count($form->_dynamic_colors)) {
            foreach ($form->_dynamic_colors as $c_color) {
                $items['#delete_colors:'.$c_color->id]->color_set($c_color->value_hex);
                if (isset($colors_statistics[$c_color->id])) {
                    $items['#delete_colors:'.$c_color->id]->disabled_set();
                }
            }
            $form->child_select('delete')->child_select('export')->child_insert(
                new Markup('a', ['href' => '/manage/view/colors/export'], 'export'), 'link'
            );
        }
        # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
        $button_insert_is_clicked = $form->clicked_button_get() && $form->clicked_button_get()->value_get() === 'insert';
        $button_delete_is_clicked = $form->clicked_button_get() && $form->clicked_button_get()->value_get() === 'delete';
        if ($button_insert_is_clicked && isset($items['*insert_colors'])) $items['*insert_colors']->required_any = true;
        if ($button_delete_is_clicked && isset($items['*delete_colors'])) $items['*delete_colors']->required_any = true;
        if ($current_color        !== null) $items['#color'       ]->value_set($current_color       );
        if ($current_multiplier_l !== null) $items['#multiplier_l']->value_set($current_multiplier_l);
        if ($current_multiplier_r !== null) $items['#multiplier_r']->value_set($current_multiplier_r);
        $items['~insert']->disabled_set(count($form->_dynamic_colors)  >  static::APPROXIMATE_LIMIT);
        $items['~delete']->disabled_set(count($form->_dynamic_colors) === 0                        );
        $form->child_select('insert')->description = count($form->_dynamic_colors) > static::APPROXIMATE_LIMIT ? 'Color limit has been exceeded!' : '';
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'generate':
                static::_set_current_color       (ltrim($items['#color'       ]->value_get(), '#'));
                static::_set_current_multiplier_l(      $items['#multiplier_l']->value_get()      );
                static::_set_current_multiplier_r(      $items['#multiplier_r']->value_get()      );
                $colors = static::_generate_palette(
                    $items['#color'       ]->value_get(),
                    $items['#multiplier_l']->value_get(),
                    $items['#multiplier_r']->value_get()
                );
                foreach ($colors as $c_id => $c_color_hex) {
                    $items['#insert_colors:'.$c_id]->color_set($c_color_hex);
                }
                Message::insert('Generation done.');
                $form->components_build();
                $form->components_init();
                break;
            case 'insert':
                $current_color        = static::_get_current_color       ();
                $current_multiplier_l = static::_get_current_multiplier_l();
                $current_multiplier_r = static::_get_current_multiplier_r();
                $colors = static::_generate_palette(
                    $current_color,
                    $current_multiplier_l,
                    $current_multiplier_r
                );
                $values_new = [];
                foreach (array_intersect_key($colors, $items['*insert_colors']->value_get()) as $c_hex_value) {
                    $values_new['custom_'.ltrim($c_hex_value, '#')] = $c_hex_value;
                }
                $result = Color::changes_store($values_new);
                foreach ($result['colors'] as $c_id => $c_result) {
                    if ($c_result) Message::insert(new Text('Color with ID = "%%_id" and value = "%%_value" was appended.'    , ['id' => $c_id, 'value' => $values_new[$c_id]])         );
                    else           Message::insert(new Text('Color with ID = "%%_id" and value = "%%_value" was not appended!', ['id' => $c_id, 'value' => $values_new[$c_id]]), 'error');
                }
                $form->components_build();
                $form->components_init();
                break;
            case 'delete':
                $values_old = [];
                foreach (array_intersect_key($form->_dynamic_colors, $items['*delete_colors']->value_get()) as $c_color) {
                    $values_old[$c_color->id] = null;
                }
                $result = Color::changes_store($values_old);
                foreach ($result['colors'] as $c_id => $c_result) {
                    if ($c_result) Message::insert(new Text('Color with ID = "%%_id" was deleted.'    , ['id' => $c_id])         );
                    else           Message::insert(new Text('Color with ID = "%%_id" was not deleted!', ['id' => $c_id]), 'error');
                }
                $form->components_build();
                $form->components_init();
                break;
        }
    }

    ###############
    ### helpers ###
    ###############

    static function _get_current_color       () {return isset($_COOKIE['current_custom_color'       ]) && Security::validate_hex_color('#'.$_COOKIE['current_custom_color'])                                                                                                                             ? '#'.$_COOKIE['current_custom_color'       ] : null;}
    static function _get_current_multiplier_l() {return isset($_COOKIE['current_custom_multiplier_l']) && Security::validate_str_int($_COOKIE['current_custom_multiplier_l']) && Security::sanitize_min_max(static::MULTIPLIER_L_MIN, static::MULTIPLIER_L_MAX, $_COOKIE['current_custom_multiplier_l']) ?     $_COOKIE['current_custom_multiplier_l'] : null;}
    static function _get_current_multiplier_r() {return isset($_COOKIE['current_custom_multiplier_r']) && Security::validate_str_int($_COOKIE['current_custom_multiplier_r']) && Security::sanitize_min_max(static::MULTIPLIER_R_MIN, static::MULTIPLIER_R_MAX, $_COOKIE['current_custom_multiplier_r']) ?     $_COOKIE['current_custom_multiplier_r'] : null;}

    static function _set_current_color       ($value) {setcookie('current_custom_color'       , $value, time() + Core::DATE_PERIOD_M, '/'); $_COOKIE['current_custom_color'       ] = $value;}
    static function _set_current_multiplier_l($value) {setcookie('current_custom_multiplier_l', $value, time() + Core::DATE_PERIOD_M, '/'); $_COOKIE['current_custom_multiplier_l'] = $value;}
    static function _set_current_multiplier_r($value) {setcookie('current_custom_multiplier_r', $value, time() + Core::DATE_PERIOD_M, '/'); $_COOKIE['current_custom_multiplier_r'] = $value;}

    static function _generate_palette($base_color_hex = null, $multiplier_l = null, $multiplier_r = null) {
        if ($base_color_hex === null) $base_color_hex = static::BASE_COLOR_HEX_DEFAULT;
        if ($multiplier_l   === null) $multiplier_l   = static::MULTIPLIER_L_DEFAULT;
        if ($multiplier_r   === null) $multiplier_r   = static::MULTIPLIER_R_DEFAULT;
        $result = [];
        $base_color = new Color(null, $base_color_hex);
        for ($i = 0; $i < 21; $i++) {
            if ($i < 11) $c_offset = ($i - 10) * abs($multiplier_l);
            if ($i > 10) $c_offset = ($i - 10) * abs($multiplier_r);
            $c_color_hex_value = $base_color->filter_shift(
                $c_offset,
                $c_offset,
                $c_offset, 1, Color::RETURN_HEX);
            $result[$i] = $c_color_hex_value; }
        return $result;
    }

}
