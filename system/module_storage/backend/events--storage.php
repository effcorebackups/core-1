<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use const \effcore\dir_root;
          use \effcore\dynamic;
          use \effcore\field_file;
          use \effcore\file;
          use \effcore\file_uploaded;
          use \effcore\media;
          use \effcore\token;
          use \effcore\widget_files;
          use \effcore\widget_files_pictures;
          abstract class events_storage {

  static function on_instance_update_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_control_class)) {
        $c_control = (new \ReflectionClass($c_field->managing_control_class))->newInstanceWithoutConstructor();
        foreach ($c_field->managing_control_properties           ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
        foreach ($c_field->managing_control_properties_on_update ?? [] as $c_prop_name => $c_prop_value) $c_control->{$c_prop_name} = $c_prop_value;
      # deleting thumbnails associated with widget_files_pictures
        if ($c_control instanceof widget_files_pictures) {
          token::insert('item_id_context', '%%_instance_id_context', 'text', $instance->id);
          token::insert('item_id_context', '%%_item_id_context',     'text', '');
          $dirs = dynamic::dir_files.$c_control->upload_dir;
          $name =       token::apply($c_control->fixed_name);
          media::picture_thumbnails_cleaning($dirs, $name);
        }
      }
    }
  }

  static function on_instance_delete_before($event, $instance) {
    $entity = $instance->entity_get();
    foreach ($entity->fields as $c_name => $c_field) {
      if (!empty($c_field->managing_control_class)) {
        $c_control = (new \ReflectionClass($c_field->managing_control_class))->newInstanceWithoutConstructor();
        foreach ($c_field->managing_control_properties ?? [] as $c_prop_name => $c_prop_value)
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
              if ($c_item->object instanceof file_uploaded) {
                @unlink($c_item->object->get_current_path());
              }
            }
          }
        }
      # deleting thumbnails associated with widget_files_pictures
        if ($c_control instanceof widget_files_pictures) {
          token::insert('item_id_context', '%%_instance_id_context', 'text', $instance->id);
          token::insert('item_id_context', '%%_item_id_context',     'text', '');
          $dirs = dynamic::dir_files.$c_control->upload_dir;
          $name =       token::apply($c_control->fixed_name);
          media::picture_thumbnails_cleaning($dirs, $name);
        }
      }
    }
  }

}}