<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class locale {

  static function     date_utc_to_loc($date)     {$date = \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC') ); if ($date) return $date->setTime    (0, 0)                                            ->format('Y-m-d'      );}
  static function     time_utc_to_loc($time)     {$date = \DateTime::createFromFormat(      'H:i:s', $time,     new \DateTimeZone('UTC') ); if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format(      'H:i:s');}
  static function datetime_utc_to_loc($datetime) {$date = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone('UTC') ); if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format('Y-m-d H:i:s');}

  static function     date_loc_to_utc($date)     {$date = \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC')                       ); if ($date) return $date->setTime    (0, 0)                      ->format('Y-m-d'      );}
  static function     time_loc_to_utc($time)     {$date = \DateTime::createFromFormat(      'H:i:s', $time,     new \DateTimeZone(core::timezone_get_client()) ); if ($date) return $date->setTimezone( new \DateTimeZone('UTC') )->format(      'H:i:s');}
  static function datetime_loc_to_utc($datetime) {$date = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone(core::timezone_get_client()) ); if ($date) return $date->setTimezone( new \DateTimeZone('UTC') )->format('Y-m-d H:i:s');}

  static function         date_format($date)     {$date = \DateTime::createFromFormat('Y-m-d',       $date,     new \DateTimeZone('UTC') );                                   if ($date) return $date->setTime    (0, 0)                                            ->format(module::get_settings('locales')->format_date    );}
  static function         time_format($time)     {$date = \DateTime::createFromFormat(      'H:i:s', $time,     new \DateTimeZone('UTC') );                                   if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format(module::get_settings('locales')->format_time    );}
  static function     datetime_format($datetime) {$date = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime, new \DateTimeZone('UTC') );                                   if ($date) return $date->setTimezone( new \DateTimeZone(core::timezone_get_client()) )->format(module::get_settings('locales')->format_datetime);}
  static function     timestmp_format($timestmp) {$date = \DateTime::createFromFormat('U',           $timestmp + core::timezone_get_offset_sec(core::timezone_get_client())); if ($date) return $date                                                               ->format(module::get_settings('locales')->format_datetime);}

  static function persent_format($number, $precision = 2) {return static::number_format(floatval($number), $precision).'%';}
  static function msecond_format($number, $precision = 6) {return static::number_format(floatval($number), $precision);}
  static function version_format($number)                 {return static::number_format(floatval($number), 3, null, null, false);}

  static function number_format($number, $precision = 0, $dec_point = null, $thousands = null, $no_zeros = true) {
    $dec_point = $dec_point === null ? module::get_settings('locales')->decimal_point       : $dec_point;
    $thousands = $thousands === null ? module::get_settings('locales')->thousands_separator : $thousands;
    return core::number_format($number, $precision, $dec_point, $thousands, $no_zeros);
  }

  static function human_bytes_format($bytes, $decimals = 2) {
    return core::bytes_to_human($bytes, $decimals, module::get_settings('locales')->decimal_point);
  }

}}