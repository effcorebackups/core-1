<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\dir_root;
          use const \effcore\br;
          use \effcore\console;
          use \effcore\core;
          use \effcore\field_file;
          abstract class events_storage {

  static function on_instance_delete_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_class)) {
        $c_reflection = new \ReflectionClass($c_field->managing_class);
        if ($c_reflection->newInstanceWithoutConstructor() instanceof field_file) {
          if (!empty($instance->{$c_name})) {
            @unlink(dir_root.$instance->{$c_name});
          }
        }
      }
    }
  }

  static function on_query_after($event, $storage, $query, $result, $errors) {
    if ($errors[0] != '00000') {
      $query_prepared = $query;
      $storage->query_prepare($query_prepared, true);
      $query_flat = core::array_values_select_recursive($query_prepared);
      $query_flat_string = implode(' ', $query_flat).';';
      $query_beautiful = str_replace([' ,', '( ', ' )'], [',', '(', ')'], $query_flat_string);
      $query_beautiful_args = '\''.implode('\', \'', $storage->args).'\'';
      console::log_insert('storage', 'query',  count($storage->args) ?
        'error state = %%_state'.br.'error code = %%_code'.br.'error text = %%_text'.br.'query = "%%_query"'.br.'arguments = [%%_args]' :
        'error state = %%_state'.br.'error code = %%_code'.br.'error text = %%_text'.br.'query = "%%_query"',
        'error', 0, [
        'state' => $errors[0],
        'code'  => $errors[1],
        'text'  => $errors[2],
        'query' => $query_beautiful,
        'args'  => $query_beautiful_args
      ]);
    }
  }

}}