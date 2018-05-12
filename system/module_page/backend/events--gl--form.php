<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class events_form {

  ###############
  ### on_init ###
  ###############

  static function on_init($form, $fields) {
  }

  ###################
  ### on_validate ###
  ###################

  static function on_validate($form, $fields, &$values) {
  }

  ###################
  ### on_submit ###
  ###################

  static function on_submit($form, $fields, &$values) {
  }

  ############
  ### file ###
  ############

  static function on_submit_files($form, $fields, &$values) {
    foreach ($fields as $c_npath => $c_field) {
      $c_element = $c_field->child_select('element');
      if ($c_element instanceof markup ||
          $c_element instanceof markup_simple) {
        $c_name = rtrim($c_element->attribute_select('name'), '[]');
        $c_type =       $c_element->attribute_select('type');
        if ($c_name) {

        # disable processing if element disabled or readonly
          if ($c_element->attribute_select('disabled') ||
              $c_element->attribute_select('readonly')) {
            continue;
          }

        # prepare value
          if (!isset($values[$c_name])) {
            $values[$c_name] = [];
          }

        # input[type=file] validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_field instanceof field_file) {
          # final copying of files
            foreach ($values[$c_name] as $c_hash => $c_file_info) {
              $c_tmp_file = new file($c_file_info->tmp_name);
              $c_new_file = new file(dynamic::directory_files.$c_field->upload_dir.$c_file_info->name);
              if ($c_field->fixed_name) $c_new_file->set_name(token::replace($c_field->fixed_name));
              if ($c_field->fixed_type) $c_new_file->set_type(token::replace($c_field->fixed_type));
              if ($c_tmp_file->is_exist() &&
                  $c_tmp_file->get_hash() == $c_hash &&
                  $c_tmp_file->move($c_new_file->get_dirs(), $c_new_file->get_file())) {
                $c_file_info->new_path = $c_new_file->get_path();
              }
            }
          # cleaning the manager
            $c_field->pool_manager_clean();
          }

        }
      }
    }
  # delete the stack
    $validation_id = form::validation_id_get();
    temporary::delete('files-'.$validation_id);
  }

}}