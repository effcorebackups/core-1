<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class locale {

  static function     date_utc_to_loc($date)     {$date = \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC') ); if ($date) return $date->setTime    (0, 0)                                            ->format('Y-m-d'      );}
  static function     time_utc_to_loc($time)     {$date = \DateTime::createFromFormat(      'H:i:s', $time,     new \DateTimeZone('UTC') ); if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format(      'H:i:s');}
  static function datetime_utc_to_loc($datetime) {$date = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone('UTC') ); if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format('Y-m-d H:i:s');}

  static function     date_loc_to_utc($date)     {$date = \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC')                       ); if ($date) return $date->setTime    (0, 0)                      ->format('Y-m-d'      );}
  static function     time_loc_to_utc($time)     {$date = \DateTime::createFromFormat(      'H:i:s', $time,     new \DateTimeZone(core::timezone_get_client()) ); if ($date) return $date->setTimezone( new \DateTimeZone('UTC') )->format(      'H:i:s');}
  static function datetime_loc_to_utc($datetime) {$date = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone(core::timezone_get_client()) ); if ($date) return $date->setTimezone( new \DateTimeZone('UTC') )->format('Y-m-d H:i:s');}

  static function     format_date    ($date)     {$date = \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC') );                                   if ($date) return $date->setTime    (0, 0)                                            ->format(module::settings_get('locales')->format_date    );}
  static function     format_time    ($time)     {$date = \DateTime::createFromFormat(      'H:i:s', $time,     new \DateTimeZone('UTC') );                                   if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format(module::settings_get('locales')->format_time    );}
  static function     format_datetime($datetime) {$date = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone('UTC') );                                   if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format(module::settings_get('locales')->format_datetime);}
  static function     format_timestmp($timestmp) {$date = \DateTime::createFromFormat('U',           $timestmp + core::timezone_get_offset_sec(core::timezone_get_client())); if ($date) return $date                                                               ->format(module::settings_get('locales')->format_datetime);}

  static function format_persent($number, $precision = 2) {return static::format_number(floatval($number), $precision).'%';}
  static function format_msecond($number, $precision = 6) {return static::format_number(floatval($number), $precision);}
  static function format_version($number)                 {return static::format_number(floatval($number), 3, null, null, false);}

  static function format_number($number, $precision = 0, $dec_point = null, $thousands = null, $no_zeros = true) {
    $dec_point = $dec_point === null ? module::settings_get('locales')->decimal_point       : $dec_point;
    $thousands = $thousands === null ? module::settings_get('locales')->thousands_separator : $thousands;
    return core::format_number($number, $precision, $dec_point, $thousands, $no_zeros);
  }

  static function format_bytes($bytes) {
    $translations = [
      'KiB' => translation::get('KiB'),
      'MiB' => translation::get('MiB'),
      'GiB' => translation::get('GiB'),
      'TiB' => translation::get('TiB')];
    return str_replace(array_keys($translations), array_values($translations), core::bytes_to_abbreviated($bytes, true));
  }

}}