<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class File_lock {

    # ────────────────────────────────────────────────────────────────────────────
    # lock usage example:
    # ════════════════════════════════════════════════════════════════════════════
    #
    #    $file = new File('test.txt');
    #    $lock_life_time         = 3;
    #    $lock_checks_sleep_time = 1;
    #    $lock_checks_count      = 10;
    #
    #    for ($i = 0; $i < $lock_checks_count; $i++) {
    #      if (File_lock::is_set($file, $lock_life_time) === File_lock::LOCK_IS_ACTIVE)
    #           sleep($lock_checks_sleep_time);
    #      else break;
    #    }
    #
    #    File_lock::insert($file);
    #      # … code …
    #    File_lock::delete($file);
    #
    # ────────────────────────────────────────────────────────────────────────────

    const LOCK_IS_ABSENT   = 0b00;
    const LOCK_IS_ACTIVE   = 0b01;
    const LOCK_WAS_EXPIRED = 0b10;
    const LOCK_LIFE_TIME   = 3;

    static function is_set($file, $life_time = null) {
        if ($life_time === null)
            $life_time = static::LOCK_LIFE_TIME;
        $lock_path = $file->path_get_absolute().'.lock';
        $lock_file_is_exists = file_exists($lock_path);
        if ($lock_file_is_exists === false) return static::LOCK_IS_ABSENT;
        if ($lock_file_is_exists !== false) {
            if (time() < (int)@file_get_contents($lock_path) + $life_time)
                 return static::LOCK_IS_ACTIVE;
            else return static::LOCK_WAS_EXPIRED;
        }
    }

    static function insert($file) {
        $lock_path = $file->path_get_absolute().'.lock';
        return @file_put_contents($lock_path, time());
    }

    static function delete($file) {
        $lock_path = $file->path_get_absolute().'.lock';
        if (file_exists($lock_path)) {
            @unlink($lock_path);
        }
    }

}
