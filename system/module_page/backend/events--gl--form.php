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

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. attribute MULTIPLE in SELECT element is not supported on touch
  #    devices - tablets, phones, monitors with touch screens
  # 2. not recommend to use DISABLED|READONLY text fields with shared
  #    NAME (name="shared_name[]") because user can remove DISABLED|READONLY
  #    state from field and change the field VALUE and submit the form - after
  #    this action the new VALUE will be setted to the next field with
  #    shared NAME.
  #    example (default form state):
  #    - input[type=text,name=shared_name[],value=1,disabled|readonly]
  #    - input[type=text,name=shared_name[],value=2]
  #    - input[type=text,name=shared_name[],value=3]
  #    example (user made a fake changes):
  #    - input[type=text,name=shared_name[],value=fake_value]
  #    - input[type=text,name=shared_name[],value=2]
  #    - input[type=text,name=shared_name[],value=3]
  #    example (result form state after validate):
  #    - input[type=text,name=shared_name[],value=1,disabled|readonly]
  #    - input[type=text,name=shared_name[],value=fake_value]
  #    - input[type=text,name=shared_name[],value=2]
  # 3. if you used more than 1 element with attribute MULTIPLE and shared
  #    NAME (name="shared_name[]"), after submit you will get equivalent
  #    arrays of values.
  #    example (result form state before validate):
  #    - select[name=shared_name[],multiple]
  #      - option[value=1,selected]
  #      - option[value=2]
  #      - option[value=3]
  #    - select[name=shared_name[],multiple]
  #      - option[value=1]
  #      - option[value=2,selected]
  #      - option[value=3]
  #    example (result form state after validate):
  #    - select[name=shared_name[],multiple]
  #      - option[value=1,selected]
  #      - option[value=2,selected]
  #      - option[value=3]
  #    - select[name=shared_name[],multiple]
  #      - option[value=1,selected]
  #      - option[value=2,selected]
  #      - option[value=3]
  # ─────────────────────────────────────────────────────────────────────

  static function on_validate($form, $fields, &$values) {
    $indexes = [];
    foreach ($fields as $c_dpath => $c_field) {
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

        # define value index
          $c_index = !isset($indexes[$c_name]) ?
                           ($indexes[$c_name] = 0) :
                          ++$indexes[$c_name];

        # prepare value
          if (!isset($values[$c_name])) {
            $values[$c_name] = [];
          }

        # input[type=file] validation:
        # ─────────────────────────────────────────────────────────────────────
        if ($c_field instanceof field_file) {
            static::_validate_field_file($form, $c_field, $c_element, $c_dpath, $c_name, $values[$c_name]);
          }

        }
      }
    }
  }

  ############
  ### file ###
  ############

  static function _validate_field_file($form, $field, $element, $dpath, $name, &$new_values) {
    $title = translation::get(
      $field->title
    );

  # get maximum file size from field_file::max_file_size or php upload_max_filesize
    $max_size = $field->get_max_file_size();

  # break processing if some file from set of files is broken
    foreach ($new_values as $c_new_value) {
      switch ($c_new_value->error) {
        case UPLOAD_ERR_INI_SIZE   : $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('the size of uploaded file more than %%_size', ['size' => locale::format_human_bytes($max_size)])])); return;
        case UPLOAD_ERR_PARTIAL    : $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('the uploaded file was only partially uploaded')]));                                                  return;
        case UPLOAD_ERR_NO_TMP_DIR : $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('missing a temporary directory')]));                                                                  return;
        case UPLOAD_ERR_CANT_WRITE : $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('failed to write file to disk')]));                                                                   return;
        case UPLOAD_ERR_EXTENSION  : $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('a php extension stopped the file upload')]));                                                        return;
      }

      if ($c_new_value->size === 0) {
        $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('file is empty')]));
        return;
      }
      if ($c_new_value->size > $max_size) {
        $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => translation::get('the size of uploaded file more than %%_size', ['size' => locale::format_human_bytes($max_size)])]));
        return;
      }
      if ($c_new_value->error !== UPLOAD_ERR_OK) {
        $form->add_error($dpath.'/element', translation::get('Field "%%_title" after trying to upload the file returned an error: %%_error!', ['title' => $title, 'error' => $c_new_value->error]));
        return;
      }
    }

  # check if field is multiple or singular
  # ─────────────────────────────────────────────────────────────────────
    if (!$element->attribute_select('multiple') && count($new_values) > 1) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" is not support multiple select!', ['title' => $title])
      );
    }

  # build the pool with pool manager
  # ─────────────────────────────────────────────────────────────────────
    $field->pool_build($new_values);

  # check required
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('required') && count($new_values) == 0) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must be selected!', ['title' => $title])
      );
      return;
    }

  }

  static function on_submit_files($form, $fields, &$values) {
    foreach ($fields as $c_dpath => $c_field) {
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