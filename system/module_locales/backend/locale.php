<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class locale implements has_cache_cleaning {

  static protected $cache_settings;

  static function cache_cleaning() {
    static::$cache_settings = null;
  }

  static function init() {
    static::$cache_settings = storage::get('files')->select('settings/locales');
  }

  static function settings_get() {
    if    (static::$cache_settings == null) static::init();
    return static::$cache_settings;
  }

  ###############
  ### formats ###
  ###############

  static function format_time($time)                      {return \DateTime::createFromFormat('H:i:s',       $time,     new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( date_default_timezone_get() ))->format( static::settings_get()->format_time );}
  static function format_date($date)                      {return \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( date_default_timezone_get() ))->format( static::settings_get()->format_date );}
  static function format_datetime($datetime)              {return \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( date_default_timezone_get() ))->format( static::settings_get()->format_datetime );}
  static function format_timestamp($timestamp)            {return \DateTime::createFromFormat('U',           $timestamp                         )->setTimezone(new \DateTimeZone( date_default_timezone_get() ))->format( static::settings_get()->format_datetime );}
  static function format_persent($number, $precision = 2) {return static::format_number(floatval($number), $precision).'%';}
  static function format_msecond($number, $precision = 6) {return static::format_number(floatval($number), $precision);}
  static function format_version($number)                 {return static::format_number(floatval($number), 3, null, null, false);}

  static function format_number($number, $precision = 0, $dec_point = null, $thousands = null, $no_zeros = true) {
    $dec_point = $dec_point === null ? static::settings_get()->decimal_point       : $dec_point;
    $thousands = $thousands === null ? static::settings_get()->thousands_separator : $thousands;
    return core::format_number($number, $precision, $dec_point, $thousands, $no_zeros);
  }

  static function format_human_bytes($bytes, $decimals = 2) {
    return core::bytes_to_human($bytes, $decimals, static::settings_get()->decimal_point);
  }

}}