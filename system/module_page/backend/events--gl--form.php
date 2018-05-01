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

  # attributes support:
  # ─────────────────────────────────────────────────────────────────────
  # - textarea                   : disabled, readonly, required, minlength, maxlength, PATTERN, name[]
  # - input[type=text]           : disabled, readonly, required, minlength, maxlength, PATTERN, name[]
  # - input[type=password]       : disabled, readonly, required, minlength, maxlength, PATTERN, name[]
  # - input[type=search]         : disabled, readonly, required, minlength, maxlength, PATTERN, name[]
  # - input[type=url]            : disabled, readonly, required, minlength, maxlength, PATTERN, name[]
  # - input[type=tel]            : disabled, readonly, required, minlength, maxlength, PATTERN, name[]
  # - input[type=email]          : disabled, readonly, required, minlength, maxlength, PATTERN, multiple, name[]
  # - select                     : disabled,           required, multiple, name[]
  # - select::option             : disabled
  # - input[type=file]           : disabled,           required, multiple, name[]
  # - input[type=checkbox]       : disabled,           required, checked, name[]
  # - input[type=radio]          : disabled,           required, checked, name[]
  # - input[type=number]         : disabled, readonly, required, min, max, step, name[]
  # - input[type=range]          : disabled,           required, min, max, step, name[]
  # - input[type=date]           : disabled, readonly, required, min, max, name[]
  # - input[type=time]           : disabled, readonly, required, min, max, name[]
  # - input[type=color]          : disabled,           required, name[]
  # ─────────────────────────────────────────────────────────────────────
  # - input[type=hidden]         : not processed element
  # - input[type=button]         : not processed element
  # - input[type=reset]          : not processed element
  # - input[type=submit]         : not processed element
  # - input[type=image]          : not processed element
  # - input[type=week]           : not processed element
  # - input[type=month]          : not processed element
  # - input[type=datetime]       : not processed element
  # - input[type=datetime-local] : not processed element
  # ─────────────────────────────────────────────────────────────────────

  # attributes validation plan:
  # ─────────────────────────────────────────────────────────────────────
  # - DISABLED                   : disable any processing of element
  # - READONLY                   : disable any processing of element
  # - REQUIRED                   : VALUE != '' (value must be present in $_POST)
  # - MINLENGTH                  : VALUE >= MINLENGTH
  # - MAXLENGTH                  : VALUE <= MAXLENGTH
  # - MIN                        : VALUE >= MIN
  # - MAX                        : VALUE <= MAX
  # - STEP                       : VALUE should be in valid step range: MIN + STEP * N, where N = [0, 1, 2 ...]
  # - PATTERN                    : VALUE should match the PATTERN
  # - MULTIPLE                   : VALUE must be singular if MULTIPLE attribute is not present
  # ─────────────────────────────────────────────────────────────────────
  # - input[type=email]          : VALUE should filtered via FILTER_VALIDATE_EMAIL
  # - input[type=url]            : VALUE should filtered via FILTER_VALIDATE_URL
  # - input[type=date]           : VALUE should match the pattern YYYY-MM-DD
  # - input[type=time]           : VALUE should match the pattern HH:MM:SS|HH:MM
  # - input[type=color]          : VALUE should match the pattern #dddddd
  # ─────────────────────────────────────────────────────────────────────

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. attribute MULTIPLE in SELECT element is not supported on touch
  #    devices - tablets, phones, monitors with touch screens
  # 2. attribute REQUIRED is not standart for input[type=color|range]
  #    but supported and recommended in this system
  # 3. not recommend to use DISABLED|READONLY text fields with shared
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
  # 4. if you used more than 1 element with attribute MULTIPLE and shared
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

        # select validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_element->tag_name == 'select') {
            $c_allowed_values = [];
            foreach ($c_element->children_select_recursive() as $c_option) {
              if ($c_option instanceof node && $c_option->tag_name == 'option') {
                if (!$c_option->attribute_select('disabled')) {
                  $c_allowed_values[] = $c_option->attribute_select('value');
                }
              }
            }
            static::_validate_field_selector($form, $c_field, $c_element, $c_dpath, $c_name, $values[$c_name], $c_allowed_values);
            foreach ($c_element->children_select_recursive() as $c_option) {
              if ($c_option instanceof node && $c_option->tag_name == 'option') {
                if (factory::in_array_string_compare($c_option->attribute_select('value'), $values[$c_name]))
                     $c_option->attribute_insert('selected', 'selected');
                else $c_option->attribute_delete('selected');
              }
            }
          }

        # input[type=file] validation:
        # ─────────────────────────────────────────────────────────────────────
        if ($c_field instanceof field_file) {
            static::_validate_field_file($form, $c_field, $c_element, $c_dpath, $c_name, $values[$c_name]);
          }

        # input[type=checkbox|radio] validation:
        # ─────────────────────────────────────────────────────────────────────
          if (($c_element->tag_name == 'input' && $c_type == 'checkbox') ||
              ($c_element->tag_name == 'input' && $c_type == 'radio')) {
            static::_validate_field_boxes($form, $c_field, $c_element, $c_dpath, $c_name, $values[$c_name]);
          }

        # textarea validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_element->tag_name == 'textarea') {
            static::_validate_field_text($form, $c_field, $c_element, $c_dpath, $c_name, $values[$c_name][$c_index]);
            $content = $c_element->child_select('content');
            $content->text = $values[$c_name][$c_index];
          }

        # input[type=text|password|search|email|url|tel|number|range|date|time|color] validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_element->tag_name == 'input' && (
              $c_type == 'text'     ||
              $c_type == 'password' ||
              $c_type == 'search'   ||
              $c_type == 'url'      ||
              $c_type == 'tel'      ||
              $c_type == 'email'    ||
              $c_type == 'number'   ||
              $c_type == 'range'    ||
              $c_type == 'date'     ||
              $c_type == 'time'     ||
              $c_type == 'color')) {
            static::_validate_field_text($form, $c_field, $c_element, $c_dpath, $c_name, $values[$c_name][$c_index]);
            $c_element->attribute_insert('value', $values[$c_name][$c_index]);
          }

        }
      }
    }
  }

  ################################
  ### _validate_field_selector ###
  ################################

  static function _validate_field_selector($form, $field, $element, $dpath, $name, &$new_values, $allowed_values) {
    $title = translation::get(
      $field->title
    );

  # filter fake values from the user's side
  # ─────────────────────────────────────────────────────────────────────
    $new_values = array_unique(array_intersect($new_values, $allowed_values));

  # check required
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('required') && empty(array_filter($new_values, 'strlen'))) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must be selected!', ['title' => $title])
      );
      return;
    }

  # deleting empty value '' in array with many values
  # ─────────────────────────────────────────────────────────────────────
  # - ['' => '']          -> ['' => '']
  # - ['' => '', ...]     -> [...]
  # ─────────────────────────────────────────────────────────────────────
    $new_values = array_filter($new_values, 'strlen') ?: $new_values;

  # check if field is multiple or singular
  # ─────────────────────────────────────────────────────────────────────
    if (!$element->attribute_select('multiple') && count($new_values) > 1) {
      $new_values = array_slice($new_values, -1);
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" is not support multiple select!', ['title' => $title])
      );
    }
  }

  ############################
  ### _validate_field_file ###
  ############################

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

  #############################
  ### _validate_field_boxes ###
  #############################

  static function _validate_field_boxes($form, $field, $element, $dpath, $name, &$new_values) {
    $title = translation::get(
      $field->title
    );

  # delete default (from _init) and set new (from $_POST) CHECKED state
  # ─────────────────────────────────────────────────────────────────────
    if (factory::in_array_string_compare($element->attribute_select('value'), $new_values))
         $element->attribute_insert('checked', 'checked');
    else $element->attribute_delete('checked');

  # check required
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('required') && !factory::in_array_string_compare($element->attribute_select('value'), $new_values)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must be checked!', ['title' => $title])
      );
      return;
    }

  }

  ############################
  ### _validate_field_text ###
  ############################

  static function _validate_field_text($form, $field, $element, $dpath, $name, &$new_value) {
    $title = translation::get(
      $field->title
    );

  # check required
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" can not be blank!', ['title' => $title])
      );
      return;
    }

  # check minlength
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('minlength') && strlen($new_value) &&
        $element->attribute_select('minlength')  > strlen($new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must contain a minimum of %%_num characters!', ['title' => $title, 'num' => $element->attribute_select('minlength')])
      );
      return;
    }

  # check maxlength
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('maxlength') &&
        $element->attribute_select('maxlength') < strlen($new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" must contain a maximum of %%_num characters!', ['title' => $title, 'num' => $element->attribute_select('maxlength')]).br.
        translation::get('Value was trimmed to the required length!').br.
        translation::get('Check field again before submit.')
      );
    # trim value to maximum lenght
      $new_value = substr($new_value, 0, $element->attribute_select('maxlength'));
      return;
    }

  # check number/range
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('type') == 'number' ||
        $element->attribute_select('type') == 'range') {

    # value validation matrix - [number('...') => is_valid(0|1|2), ...]
    # ─────────────────────────────────────────────────────────────────────
    # ''   => 0, '-'   => 0 | '0'   => 1, '-0'   => 0 | '1'   => 1, '-1'   => 1 | '01'   => 0, '-01'   => 0 | '10'   => 1, '-10'   => 1
    # '.'  => 0, '-.'  => 0 | '0.'  => 0, '-0.'  => 0 | '1.'  => 0, '-1.'  => 0 | '01.'  => 0, '-01.'  => 0 | '10.'  => 0, '-10.'  => 0
    # '.0' => 0, '-.0' => 0 | '0.0' => 1, '-0.0' => 2 | '1.0' => 1, '-1.0' => 1 | '01.0' => 0, '-01.0' => 0 | '10.0' => 1, '-10.0' => 1
    # ─────────────────────────────────────────────────────────────────────
      if (!preg_match('%^(?<integer>[-]?[1-9][0-9]*|0)$|'.
                       '^(?<float_s>[-]?[0-9][.][0-9]{1,3})$|'.
                       '^(?<float_l>[-]?[1-9][0-9]+[.][0-9]{1,3})$%S', $new_value)) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is not a valid number.')
        );
        return;
      }

      $c_step = $element->attribute_select('step') ?: 1;
      if ($element->attribute_select('type') == 'number') {
        $c_min = $element->attribute_select('min') ?: (float)form_input_min_number;
        $c_max = $element->attribute_select('max') ?: (float)form_input_max_number; } else {
        $c_min = $element->attribute_select('min') ?: 0;
        $c_max = $element->attribute_select('max') ?: 100;
      }

    # check min
      if ($c_min > $new_value) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is less than %%_value.', ['value' => $c_min])
        );
        return;
      }

    # check max
      if ($c_max < $new_value) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is more than %%_value.', ['value' => $c_max])
        );
        return;
      }

      if ((int)round(($c_min - $new_value) / $c_step, 5) !=
               round(($c_min - $new_value) / $c_step, 5)) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is not in valid range.')
        );
        return;
      }

    }

  # check date
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('type') == 'date') {

    # check value
      if (!(preg_match('%^(?<Y>[0-9]{4})-(?<m>[0-1][0-9])-(?<d>[0-3][0-9])$%S', $new_value, $matches) &&
            checkdate($matches['m'],
                      $matches['d'],
                      $matches['Y']))) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains an incorrect date!', ['title' => $title])
        );
        return;
      }

      $c_min = $element->attribute_select('min') ?: form_input_min_date;
      $c_max = $element->attribute_select('max') ?: form_input_max_date;

    # check min
      if ($c_min > $new_value) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is less than %%_value.', ['value' => $c_min])
        );
        return;
      }

    # check max
      if ($c_max < $new_value) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is more than %%_value.', ['value' => $c_max])
        );
        return;
      }

    }

  # check time
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('type') == 'time') {

    # check value
      if (!preg_match('%^(?<H>[0-1][0-9]|20|21|22|23)'.
                    '(?::(?<i>[0-5][0-9]))'.
                    '(?::(?<s>[0-5][0-9])|)$%S', $new_value, $matches)) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains an incorrect time!', ['title' => $title])
        );
        return;
      }

      $c_min = $element->attribute_select('min') ?: form_input_min_time;
      $c_max = $element->attribute_select('max') ?: form_input_max_time;
      $c_min     = strlen($c_min)     == 5 ? $c_min.':00'     : $c_min;
      $c_max     = strlen($c_max)     == 5 ? $c_max.':00'     : $c_max;
      $new_value = strlen($new_value) == 5 ? $new_value.':00' : $new_value;

    # check min
      if ($c_min > $new_value) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is less than %%_value.', ['value' => $c_min])
        );
        return;
      }

    # check max
      if ($c_max < $new_value) {
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translation::get('Field value is more than %%_value.', ['value' => $c_max])
        );
        return;
      }

    }

  # check email field
  # ─────────────────────────────────────────────────────────────────────
    if ($element->attribute_select('type') == 'email') {
      $emails = explode(',', $new_value);
      if (!$element->attribute_select('multiple') && count($emails) > 1) {
        $new_values = $emails[0];
        $form->add_error($dpath.'/element',
          translation::get('Field "%%_title" is not support multiple select!', ['title' => $title])
        );
        return;
      }
      if (strlen($new_value)) {
        foreach ($emails as $c_email) {
          if (factory::filter_email($c_email) == false) {
            $form->add_error($dpath.'/element',
              translation::get('Field "%%_title" contains an incorrect email address!', ['title' => $title])
            );
            return;
          }
        }
      }
    }

  # check captcha
  # ─────────────────────────────────────────────────────────────────────
    if ($name == 'captcha' && !$field->captcha_check($new_value)) {
      $form->add_error($dpath.'/element',
        translation::get('Field "%%_title" contains an incorrect characters from image!', ['title' => $title])
      );
      return;
    }

  }

  #######################
  ### on_submit_files ###
  #######################

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
              $c_new_file = new file(dir_dynamic.'files/'.$c_field->upload_dir.$c_file_info->name);
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