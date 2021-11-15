<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use const \effcore\dir_root;
          use \effcore\field_file;
          use \effcore\file_history;
          use \effcore\widget_files;
          abstract class events_storage {

  static function on_instance_delete_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_control_class)) {
        $c_control = (new \ReflectionClass($c_field->managing_control_class))->newInstanceWithoutConstructor();
        if (isset($c_field->managing_control_properties) && is_array($c_field->managing_control_properties))
          foreach ($c_field->managing_control_properties as $c_prop_name => $c_prop_value)
            $c_control->{$c_prop_name} = $c_prop_value;
      # deleting the file associated with field_file
        if ($c_control instanceof field_file) {
          if (!empty($instance->{$c_name})) {
            @unlink(dir_root.$instance->{$c_name});
          }
        }
      # deleting files associated with widget_files
        if ($c_control instanceof widget_files) {
          if (!empty($instance->{$c_name})) {
            foreach ($instance->{$c_name} as $c_item) {
              if ($c_item->object instanceof file_history) {
                @unlink($c_item->object->get_current_path());
              }
            }
          }
        }
      }
    }
  }

}}