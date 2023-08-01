<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Color_preset {

    public $id;
    public $title;
    public $colors;

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('colors_presets') as $c_module_id => $c_presets) {
                foreach ($c_presets as $c_row_id => $c_preset) {
                    if (isset(static::$cache[$c_preset->id])) Console::report_about_duplicate('colors_presets', $c_preset->id, $c_module_id, static::$cache[$c_preset->id]);
                              static::$cache[$c_preset->id] = $c_preset;
                              static::$cache[$c_preset->id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function get($id) {
        static::init();
        return static::$cache[$id] ?? null;
    }

    static function get_all() {
        static::init();
        return static::$cache;
    }

    static function is_all_colors_available() {
        $result = true;
        $colors = Color::get_all();
        $settings = Module::settings_get('page');
        foreach ($settings as $c_color_type => $c_color_id)
            if (strpos($c_color_type, 'color__') === 0)
                $result&= !empty($colors[$c_color_id]);
        return $result;
    }

    static function apply($id, $selected = null, $reset = false) {
        $preset = static::get($id);
        if ($preset) {
            $result = true;
            $storage = Storage::get('data');
            foreach ($preset->colors as $c_color_type => $c_color_id)
                if (is_null($selected) || (is_array($selected) && isset($selected[$c_color_type])))
                    $result&= $storage->changes_insert('page', 'update', 'settings/page/'.$c_color_type, $c_color_id, false);
            if ($reset) Storage_NoSQL_data::cache_update();
            return $result;
        }
    }

    static function apply_with_custom_ids($selected = [], $reset = false) {
        $result = true;
        $storage = Storage::get('data');
        foreach ($selected as $c_color_type => $c_color_id)
            $result&= $storage->changes_insert('page', 'update', 'settings/page/'.$c_color_type, $c_color_id, false);
        if ($reset) Storage_NoSQL_data::cache_update();
        return $result;
    }

    static function reset($reset = true) {
        $result = true;
        $storage = Storage::get('data');
        $settings = Module::settings_get('page');
        foreach ($settings as $c_color_type => $c_color_id)
            if (strpos($c_color_type, 'color__') === 0)
                $result&= $storage->changes_delete('page', 'update', 'settings/page/'.$c_color_type, false);
        if ($reset) Storage_NoSQL_data::cache_update();
        return $result;
    }

}
