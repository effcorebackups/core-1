<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\dir_root;
          use \effcore\field_file;
          abstract class events_storage {

  static function on_instance_delete_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_field_class)) {
        $c_reflection = new \ReflectionClass($c_field->managing_field_class);
        if ($c_reflection->newInstanceWithoutConstructor() instanceof field_file) {
          if (!empty($instance->{$c_name})) {
            @unlink(dir_root.$instance->{$c_name});
          }
        }
      }
    }
  }

}}