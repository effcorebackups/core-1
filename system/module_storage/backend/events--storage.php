<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\br;
          use \effcore\console;
          use \effcore\timer;
          abstract class events_storage {

  static function on_storage_init_before($storage) {
    timer::tap('storage init');
  }

  static function on_storage_init_after($storage) {
    timer::tap('storage init');
    console::log_insert('storage', 'init.', 'storage %%_name was initialized', 'ok',
      timer::period_get('storage init', -1, -2), ['name' => $storage->name]
    );
  }

  static function on_query_before($storage, $query) {
    $query_string = implode(' ', $query).';';
    timer::tap('storage query: '.$query_string);
  }

  static function on_query_after($storage, $query, $result, $errors) {
    $args_trimmed = [];
    foreach ($storage->args as $c_arg) {
      $args_trimmed[] = mb_strimwidth($c_arg, 0, 40, '…', 'UTF-8');
    }
    $query_string = implode(' ', $query).';';
    $query_string_beautiful = str_replace([' ,', '( ', ' )'], [',', '(', ')'], $query_string);
    $query_args_beautiful = '\''.implode('\', \'', $args_trimmed).'\'';
    timer::tap('storage query: '.$query_string);
    console::log_insert('storage', 'query',
      count($storage->args) ? 'sql query = "%%_query"'.($errors[0] == '00000' ? br : '; ').'args = [%%_args]' :
                              'sql query = "%%_query"',
      $errors[0] == '00000' ? 'ok' : 'error',
      timer::period_get('storage query: '.$query_string, -1, -2), [
      'query' => $query_string_beautiful,
      'args'  => $query_args_beautiful
    ]);
  }

}}