<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
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
    console::add_log('storage', 'init.', 'storage %%_id was initialized', 'ok',
      timer::get_period('storage init', -1, -2), ['id' => $storage->get_id().' | storage_pdo']
    );
  }

  static function on_query_before($storage, $query) {
    $s_query = $storage->query_to_string($query);
    timer::tap('storage query: '.$s_query);
  }

  static function on_query_after($storage, $query, $result, $errors) {
    $buf_args = [];
    foreach ($storage->args as $c_arg) {
      $buf_args[] = strlen($c_arg) > 40 ? substr($c_arg, 0, 40).'…' : $c_arg;
    }
    $s_query = $storage->query_to_string($query);
    $s_query_beautiful = str_replace([' ,', '( ', ' )'], [',', '(', ')'], $s_query);
    $s_query_args_beautiful = '\''.implode('\', \'', $buf_args).'\'';
    timer::tap('storage query: '.$s_query);
    console::add_log('storage', 'query',
      count($storage->args) ? 'query = "%%_query"'.br.'args = [%%_args]' :
                              'query = "%%_query"',
      $errors[0] == '00000' ? 'ok' : 'error',
      timer::get_period('storage query: '.$s_query, -1, -2),
      ['query' => $s_query_beautiful,
        'args' => $s_query_args_beautiful]
    );
  }

}}