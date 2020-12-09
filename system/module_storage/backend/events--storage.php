<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\dir_root;
          use \effcore\field_file;
          use \effcore\file;
          use \effcore\file_uploaded;
          use \effcore\media;
          use \effcore\widget_files;
          use \effcore\widget_files_pictures;
          abstract class events_storage {

  static function on_instance_update_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_control_class)) {
        $c_reflection = new \ReflectionClass($c_field->managing_control_class);
        $c_reflection_instance = $c_reflection->newInstanceWithoutConstructor();
      # deleting thumbnails associated with widget_files_pictures
        if ($c_reflection_instance instanceof widget_files_pictures) {
          if (!empty($instance->{$c_name})) {
            foreach ($instance->{$c_name} as $c_item) {
              if ($c_item->object instanceof file_uploaded) {
                $thumbnail = new file($c_item->object->get_current_path());
                media::picture_thumbnails_cleaning($thumbnail->dirs_get(), $thumbnail->name_get());
              }
            }
          }
        }
      }
    }
  }

  static function on_instance_delete_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_control_class)) {
        $c_reflection = new \ReflectionClass($c_field->managing_control_class);
        $c_reflection_instance = $c_reflection->newInstanceWithoutConstructor();
      # deleting the file associated with field_file
        if ($c_reflection_instance instanceof field_file) {
          if (!empty($instance->{$c_name})) {
            @unlink(dir_root.$instance->{$c_name});
          }
        }
      # deleting files associated with widget_files
        if ($c_reflection_instance instanceof widget_files) {
          if (!empty($instance->{$c_name})) {
            foreach ($instance->{$c_name} as $c_item) {
              if ($c_item->object instanceof file_uploaded) {
                @unlink($c_item->object->get_current_path());
              }
            }
          }
        }
      # deleting thumbnails associated with widget_files_pictures
        if ($c_reflection_instance instanceof widget_files_pictures) {
          if (!empty($instance->{$c_name})) {
            foreach ($instance->{$c_name} as $c_item) {
              if ($c_item->object instanceof file_uploaded) {
                $thumbnail = new file($c_item->object->get_current_path());
                media::picture_thumbnails_cleaning($thumbnail->dirs_get(), $thumbnail->name_get());
              }
            }
          }
        }
      }
    }
  }

}}