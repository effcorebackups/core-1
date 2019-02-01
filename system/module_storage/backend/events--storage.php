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
    $s_query = $storage->query_to_string($query);
    timer::tap('storage query: '.$s_query);
  }

  static function on_query_after($storage, $query, $result, $errors) {
    $buf_args = [];
    foreach ($storage->args as $c_arg) {
      $buf_args[] = mb_strimwidth($c_arg, 0, 40, '…', 'UTF-8');
    }
    $s_query = $storage->query_to_string($query);
    $s_query_beautiful = str_replace([' ,', '( ', ' )'], [',', '(', ')'], $s_query);
    $s_query_args_beautiful = '\''.implode('\', \'', $buf_args).'\'';
    timer::tap('storage query: '.$s_query);
    console::log_insert('storage', 'query',
      count($storage->args) ? 'sql query = "%%_query"'.($errors[0] == '00000' ? br : '; ').'args = [%%_args]' :
                              'sql query = "%%_query"',
      $errors[0] == '00000' ? 'ok' : 'error',
      timer::period_get('storage query: '.$s_query, -1, -2), [
      'query' => $s_query_beautiful,
      'args'  => $s_query_args_beautiful
    ]);
  }

}}