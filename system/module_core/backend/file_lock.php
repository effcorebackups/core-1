<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class file_lock {

  # ────────────────────────────────────────────────────────────────────────────
  # lock usage example:
  # ════════════════════════════════════════════════════════════════════════════
  #
  #    $file = new file('test.txt');
  #    $lock_life_time         = 3;
  #    $lock_checks_sleep_time = 1;
  #    $lock_checks_count      = 10;
  #
  #    for ($i = 0; $i < $lock_checks_count; $i++) {
  #      if (file_lock::is_set($file, $lock_life_time) === file_lock::lock_is_active)
  #           sleep($lock_checks_sleep_time);
  #      else break;
  #    }
  #
  #    file_lock::insert($file);
  #      # … code …
  #    file_lock::delete($file);
  #
  # ────────────────────────────────────────────────────────────────────────────

  const lock_is_absent   = 0b00;
  const lock_is_active   = 0b01;
  const lock_was_expired = 0b10;
  const lock_life_time   = 3;

  static function is_set($file, $life_time = null) {
    if ($life_time === null)
        $life_time = static::lock_life_time;
    $lock_path = $file->path_get_absolute().'.lock';
    $lock_file_is_exists = file_exists($lock_path);
    if ($lock_file_is_exists === false) return static::lock_is_absent;
    if ($lock_file_is_exists !== false) {
      if (time() < (int)@file_get_contents($lock_path) + $life_time)
           return static::lock_is_active;
      else return static::lock_was_expired;
    }
  }

  static function insert($file) {
    $lock_path = $file->path_get_absolute().'.lock';
    return @file_put_contents($lock_path, time());
  }

  static function delete($file) {
    $lock_path = $file->path_get_absolute().'.lock';
    if (file_exists($lock_path))
            @unlink($lock_path);
  }

}}