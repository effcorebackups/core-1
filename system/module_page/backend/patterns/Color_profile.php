<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Color_profile {

    const PROFILE_DEFAULT = 'dark_blue';
    const STATISTICS_MODE_COLOR_BY_PROFILE = 0b01;
    const STATISTICS_MODE_PROFILE_BY_COLOR = 0b10;

    public $id;
    public $title;
    public $is_dark = false;
    public $is_user_selectable = true;
    public $groups = [];
    public $colors = [];
    public $weight = +0;

    ###########################
    ### static declarations ###
    ###########################

    protected static $current;
    protected static $cache;

    static function cache_cleaning() {
        static::$current = null;
        static::$cache   = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('color_profiles') as $c_module_id => $c_profiles) {
                foreach ($c_profiles as $c_row_id => $c_profile) {
                    if (isset(static::$cache[$c_profile->id])) Console::report_about_duplicate('color_profiles', $c_profile->id, $c_module_id, static::$cache[$c_profile->id]);
                              static::$cache[$c_profile->id] = $c_profile;
                              static::$cache[$c_profile->id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get_current($reset = false, $ignore_cookie = false, $ignore_user = false) {
        static::init();
        if (!static::$current || $reset) {
            # trying to return the color profile by value from an external source
            if ($ignore_cookie === false) {
                if (isset($_COOKIE['color_profile'])) {
                    $profile_id = $_COOKIE['color_profile'];
                    if (is_string($profile_id) && strlen($profile_id) && isset(static::$cache[$profile_id])) {
                        static::$current = static::$cache[$profile_id];
                        return static::$current;
                    }
                }
            }
            # trying to return the color profile by value from user settings
            if ($ignore_user === false) {
                $user = User::get_current();
                $profile_id = $user->id ? $user->color_profile : '';
                if (isset(static::$cache[$profile_id])) {
                    if (static::$cache[$profile_id]->is_user_selectable) {
                        static::$current = static::$cache[$profile_id];
                        return static::$current;
                    }
                }
            }
            # trying to return the color profile by value from system settings
            $settings = Module::settings_get('page');
            $profile_id = $settings->color_profile;
            if (isset(static::$cache[$profile_id])) {
                static::$current = static::$cache[$settings->color_profile];
                return static::$current;
            }
            # return default value
            static::$current = static::$cache[static::PROFILE_DEFAULT];
            return static::$current;
        }
        return static::$current;
    }

    static function set_current($id) {
        if ($id === static::PROFILE_DEFAULT)
             return Storage::get('data')->changes_unregister('page', 'update', 'settings/page/color_profile');
        else return Storage::get('data')->changes_register  ('page', 'update', 'settings/page/color_profile', $id);
    }

    static function get($id) {
        static::init();
        return static::$cache[$id] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

    static function get_color_info($profile_id = self::PROFILE_DEFAULT, $scope = 'main') {
        $profile = static::get($profile_id);
        if ($profile) {
            foreach ($profile->groups as $c_group) {
                foreach ($c_group->colors as $c_scope => $c_color_info) {
                    if ($c_scope === $scope) {
                        return $c_color_info;
                    }
                }
            }
        }
    }

    static function get_colors_statistics($mode = self::STATISTICS_MODE_COLOR_BY_PROFILE) {
        $result = [];
        $profiles = static::get_all();
        foreach ($profiles as $c_profile) {
            foreach ($c_profile->groups as $c_group) {
                foreach ($c_group->colors as $c_scope => $c_color) {
                    if ($mode === static::STATISTICS_MODE_COLOR_BY_PROFILE) {
                        if (!isset($result[$c_color->color_id][$c_profile->id]))
                                   $result[$c_color->color_id][$c_profile->id] = 1;
                        else       $result[$c_color->color_id][$c_profile->id]++;
                    }
                    if ($mode === static::STATISTICS_MODE_PROFILE_BY_COLOR) {
                        if (!isset($result[$c_profile->id][$c_color->color_id]))
                                   $result[$c_profile->id][$c_color->color_id] = 1;
                        else       $result[$c_profile->id][$c_color->color_id]++;
                    }
                }
            }
        }
        return $result;
    }

    static function changes_store($values, $module_id, $profile_id) {
        $result = true;
        if (array_key_exists('is_user_selectable', $values)) {
            if ($values['is_user_selectable'] === false) $result&= Storage::get('data')->changes_register  ($module_id, 'update', 'color_profiles/'.$module_id.'/'.$profile_id.'/is_user_selectable', false, false);
            if ($values['is_user_selectable'] !== false) $result&= Storage::get('data')->changes_unregister($module_id, 'update', 'color_profiles/'.$module_id.'/'.$profile_id.'/is_user_selectable',        false);
        }
        if (isset($values['colors'])) {
            foreach ($values['colors'] as $c_group_row_id => $c_group) {
                foreach ($c_group as $c_scope => $c_new_value) {
                    if ($c_new_value !== null) $result&= Storage::get('data')->changes_register  ($module_id, 'update', 'color_profiles/'.$module_id.'/'.$profile_id.'/groups/'.$c_group_row_id.'/colors/'.$c_scope.'/color_id', $c_new_value, false);
                    if ($c_new_value === null) $result&= Storage::get('data')->changes_unregister($module_id, 'update', 'color_profiles/'.$module_id.'/'.$profile_id.'/groups/'.$c_group_row_id.'/colors/'.$c_scope.'/color_id',               false);
                }
            }
        }
        $result&= Storage_Data::cache_update();
        return $result;
    }

}
