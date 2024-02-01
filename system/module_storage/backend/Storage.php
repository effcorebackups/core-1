<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Storage {

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init($name) {
        if (!isset(static::$cache[$name])) {
            Storage_Data::init('storages');
            foreach (Storage_Data::$data['storages'] as $c_module_id => $c_module_storages) {
                foreach ($c_module_storages as $c_row_id => $c_storage) {
                    if ($c_storage->name === $name) {
                        static::$cache[$c_storage->name] = $c_storage;
                    }
                }
            }
        }
    }

    static function get($name, $load = true) {
        static::init($name);
        if (isset(static::$cache[$name]) === false) return;
        if (static::$cache[$name] instanceof External_cache && $load)
               static::$cache[$name] =
               static::$cache[$name]->load_from_nosql_storage();
        return static::$cache[$name];
    }

    static function changes_store($values) {
        return Storage::get('data')->changes_register('core', 'insert', 'storages/storage/sql', $values);
    }

}
