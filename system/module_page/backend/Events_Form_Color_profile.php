<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Color_profile;
use effcore\Color;
use effcore\Fieldset;
use effcore\Group_Palette;
use effcore\Message;
use effcore\Module;
use effcore\Page;

abstract class Events_Form_Color_profile {

    static function on_build($event, $form) {
        $profile_id = Page::get_current()->args_get('profile_id');
        $form->_profile = Color_profile::get($profile_id);
        if ($form->_profile) {
            setcookie('color_profile', $profile_id, time() + 1, '/');
            foreach ($form->_profile->groups as $c_group_row_id => $c_group) {
                $c_fieldset = new Fieldset($c_group->title);
                foreach ($c_group->colors as $c_scope => $c_color_info) {
                    $c_palette = new Group_Palette;
                    $c_palette->title = $c_color_info->title;
                    $c_palette->element_attributes['name'] = 'color_'.$c_scope;
                    $c_fieldset->child_insert($c_palette, $c_scope);
                }
                $form->child_select('assign')->child_insert(
                    $c_fieldset, $c_group_row_id
                );
            }
        }
    }

    static function on_init($event, $form, $items) {
        $profile_id = Page::get_current()->args_get('profile_id');
        $form->_profile = Color_profile::get($profile_id);
        if ($form->_profile) {
            foreach ($form->_profile->groups as $c_group) {
                foreach ($c_group->colors as $c_scope => $c_color_info) {
                    $items['*color_'.$c_scope]->value_set(
                        $c_color_info->color_id
                    );
                }
            }
            $items['#is_user_selectable']->checked_set($form->_profile->is_user_selectable);
            $items['~save'    ]->disabled_set(false);
            $items['~reset'   ]->disabled_set(false);
            $items['~activate']->disabled_set(Module::settings_get('page')->color_profile === $profile_id);
            # export custom colors link
            $colors_statistics = Color_profile::get_colors_statistics(Color_profile::STATISTICS_MODE_PROFILE_BY_COLOR)[$profile_id];
            $colors_custom = Color::get_all('nosql-dynamic');
            if (!array_intersect_key($colors_custom, $colors_statistics))
                 $form->child_select('settings')->child_select('export_custom_colors')->attribute_insert('aria-hidden', 'true');
            else $form->child_select('settings')->child_select('export_custom_colors')->attribute_delete('aria-hidden');
        }
    }

    static function on_submit($event, $form, $items) {
        $profile_id = Page::get_current()->args_get('profile_id');
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $new_values = [];
                foreach ($form->_profile->groups as $c_group_row_id => $c_group) {
                    foreach ($c_group->colors as $c_scope => $c_color_info) {
                        $new_values[$c_group_row_id][$c_scope] = $items['*color_'.$c_scope]->value_get();
                    }
                }
                $result = Color_profile::changes_store([
                    'colors'             => $new_values,
                    'is_user_selectable' => $items['#is_user_selectable']->checked_get()],
                    $form->_profile->module_id,
                    $form->_profile->id
                );
                if ($result) Message::insert('Changes was saved.'             );
                else         Message::insert('Changes was not saved!', 'error');
                Color_profile::cache_cleaning();
                $form->components_init();
                break;
            case 'reset':
                $new_values = [];
                foreach ($form->_profile->groups as $c_group_row_id => $c_group) {
                    foreach ($c_group->colors as $c_scope => $c_color_info) {
                        $new_values[$c_group_row_id][$c_scope] = null;
                    }
                }
                $result = Color_profile::changes_store([
                    'colors'             => $new_values,
                    'is_user_selectable' => true],
                    $form->_profile->module_id,
                    $form->_profile->id
                );
                if ($result) Message::insert('Changes was deleted.'             );
                else         Message::insert('Changes was not deleted!', 'error');
                Color_profile::cache_cleaning();
                $form->components_init();
                break;
            case 'activate':
                $result = Color_profile::set_current($profile_id);
                if ($result) Message::insert('Color profile was activated.'             );
                else         Message::insert('Color profile was not activated!', 'error');
                Color_profile::cache_cleaning();
                $form->components_init();
                break;
        }
    }

}
