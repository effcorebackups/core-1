<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effectivecore {
          abstract class locale {

  static protected $countries;
  static protected $settings;

  static function init() {
    static::$settings = storage::get('files')->select('settings/locales');
    foreach (storage::get('files')->select('countries') as $c_module_id => $c_module_countries) {
      foreach ($c_module_countries as $c_row_id => $c_country) {
        static::$countries[$c_country->code] = $c_country;
      }
    }
  }

  static function get_countries() {
    if   (!static::$countries) static::init();
    return static::$countries;
  }

  static function get_settings() {
    if   (!static::$settings) static::init();
    return static::$settings;
  }

  ###############
  ### formats ###
  ###############

  static function format_time($time)                      {return \DateTime::createFromFormat('H:i:s',       $time,     new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( static::get_settings()->timezone ))->format( static::get_settings()->format_time );}
  static function format_date($date)                      {return \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( static::get_settings()->timezone ))->format( static::get_settings()->format_date );}
  static function format_datetime($datetime)              {return \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( static::get_settings()->timezone ))->format( static::get_settings()->format_datetime );}
  static function format_timestamp($timestamp)            {return \DateTime::createFromFormat('U', $timestamp,          new \DateTimeZone('UTC'))->setTimezone(new \DateTimeZone( static::get_settings()->timezone ))->format( static::get_settings()->format_datetime );}
  static function format_persent($number, $precision = 2) {return static::format_number(floatval($number), $precision).'%';}
  static function format_msecond($number, $precision = 6) {return static::format_number(floatval($number), $precision);}
  static function format_version($number)                 {return static::format_number(floatval($number), 2);}

  static function format_number($number, $precision = 0, $dec_point = null, $thousands = null, $no_zeros = true) {
    $dec_point = is_null($dec_point) ? static::get_settings()->decimal_point       : $dec_point;
    $thousands = is_null($thousands) ? static::get_settings()->thousands_separator : $thousands;
    return factory::format_number($number, $precision, $dec_point, $thousands, $no_zeros);
  }

  static function format_human_bytes($bytes, $decimals = 2) {
    return factory::bytes_to_human($bytes, $decimals, static::get_settings()->decimal_point);
  }

}}