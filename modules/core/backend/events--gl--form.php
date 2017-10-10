<?php

  #############################################################
  ### Copyright © 2017 Maxim Rysevets. All rights reserved. ###
  #############################################################

namespace effectivecore {
          use \effectivecore\markup;
          use \effectivecore\urls_factory as urls;
          use \effectivecore\events_factory as events;
          use \effectivecore\messages_factory as messages;
          use \effectivecore\translations_factory as translations;
          use \effectivecore\modules\storage\storages_factory as storages;
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
  # - input[type=email]          : disabled, readonly, required, minlength, maxlength, PATTERN, MULTIPLE, NAME[]
  # - select                     : disabled,           required, multiple, name[]
  # - select::option             : disabled
  # - input[type=file]           : DISABLED,           REQUIRED, MULTIPLE, NAME[]
  # - input[type=checkbox]       : disabled,           REQUIRED, checked, name[]
  # - input[type=radio]          : disabled,           REQUIRED, checked, name[]
  # - input[type=number]         : disabled, readonly, required, min, max, STEP, name[]
  # - input[type=range]          : disabled,           required, min, max, STEP, name[]
  # - input[type=date]           : disabled, readonly, required, MIN, MAX, name[]
  # - input[type=time]           : disabled, readonly, required, MIN, MAX, name[]
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
  # - MIN                        : VALUE >= MIN (for date|time should compare timestamps)
  # - MAX                        : VALUE <= MAX (for date|time should compare timestamps)
  # - STEP                       : VALUE should be in valid step range: MIN + STEP * N, where N = [0, 1, 2 ...]
  # - PATTERN                    : VALUE should match the PATTERN (used FILTER_VALIDATE_REGEXP)
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
  #    but supported and recomend in this system
  # 3. not recommend to use DISABLED radio field with CHECKED state - 
  #    this element will be always CHECKED regardless of user choice.
  #    example (second field will be allways CHECKED):
  #    - input[type=radio]
  #    - input[type=radio,checked,disabled]
  # 4. not recommend to use DISABLED|READONLY text fields with shared
  #    NAME because user can remove DISABLED|READONLY state from field
  #    and change the field VALUE and submit form - after this action
  #    the new VALUE will be setted to the next field with shared NAME.
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
  # ─────────────────────────────────────────────────────────────────────

  static function on_validate($form, $fields, &$values) {
    $indexes = [];
    foreach ($fields as $c_npath => $c_field) {
      $c_element = $c_field->child_select('default');
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

        # conversion matrix (expected: undefined|string|array):
        # ─────────────────────────────────────────────────────────────────────
        # - unset($_POST[name])                 -> []
        # - $_POST[name] == ''                  -> [0 => '']
        # - $_POST[name] == 'value'             -> [0 => 'value']
        # ─────────────────────────────────────────────────────────────────────
        # - $_POST[name] == [0 => '']           -> [0 => '']
        # - $_POST[name] == [0 => '', ...]      -> [0 => '', ...]
        # - $_POST[name] == [0 => 'value']      -> [0 => 'value']
        # - $_POST[name] == [0 => 'value', ...] -> [0 => 'value', ...]
        # ─────────────────────────────────────────────────────────────────────

          $c_new_values = !isset($values[$c_name]) ? [] :
                       (is_array($values[$c_name]) ?
                                 $values[$c_name]  :
                                [$values[$c_name]]);

        # select validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_element->tag_name == 'select') {
            $c_allowed_values = [];
            foreach ($c_element->child_select_all() as $c_option) {
              if ($c_option instanceof node && $c_option->tag_name == 'option') {
                if (!$c_option->attribute_select('disabled')) {
                  $c_allowed_values[] = $c_option->attribute_select('value');
                }
              }
            }
            static::_validate_field_selector($form, $c_field, $c_element, $c_npath, $c_new_values, $c_allowed_values);
            foreach ($c_element->child_select_all() as $c_option) {
              if ($c_option instanceof node && $c_option->tag_name == 'option') {
                if (in_array($c_option->attribute_select('value'), $c_new_values))
                     $c_option->attribute_insert('selected', 'selected');
                else $c_option->attribute_delete('selected');
              }
            }
          }

        # input[type=file] validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_element->tag_name == 'input' &&
              $c_type == 'file') {
            # @todo: make functionality
          }

        # input[type=checkbox|radio] validation:
        # ─────────────────────────────────────────────────────────────────────
          if (($c_element->tag_name == 'input' && $c_type == 'checkbox') ||
              ($c_element->tag_name == 'input' && $c_type == 'radio')) {
          # delete default (from _init) and set new (from $_POST) CHECKED state
            if (in_array($c_element->attribute_select('value'), $c_new_values))
                 $c_element->attribute_insert('checked', 'checked');
            else $c_element->attribute_delete('checked');
          }

        # textarea validation:
        # ─────────────────────────────────────────────────────────────────────
          if ($c_element->tag_name == 'textarea') {
            static::_validate_field_text($form, $c_field, $c_element, $c_npath, $c_new_values[$c_index]);
            $content = $c_element->child_select('content');
            $content->text = $c_new_values[$c_index];
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
            static::_validate_field_text($form, $c_field, $c_element, $c_npath, $c_new_values[$c_index]);
            $c_element->attribute_insert('value', $c_new_values[$c_index]);
          }

        }
      }
    }
  }

  ################################
  ### _validate_field_selector ###
  ################################

  static function _validate_field_selector($form, $field, $element, $npath, &$new_values, $allowed_values) {
    $title = translations::get(
      $field->title
    );

  # filter fake values from the user's side
    $new_values = array_unique(array_intersect($new_values, $allowed_values));

  # check required fields
    if ($element->attribute_select('required') && empty(array_filter($new_values, 'strlen'))) {
      $form->add_error($npath.'/default',
        translations::get('Field "%%_title" must be selected!', ['title' => $title])
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
    if (!$element->attribute_select('multiple') && count($new_values) > 1) {
      $new_values = array_slice($new_values, -1);
      $form->add_error($npath.'/default',
        translations::get('Field "%%_title" is not support multiple select!', ['title' => $title])
      );
    }
  }

  ############################
  ### _validate_field_text ###
  ############################

  static function _validate_field_text($form, $field, $element, $npath, &$new_value) {
    $title = translations::get(
      $field->title
    );

  # check required fields
    if ($element->attribute_select('required') && strlen($new_value) == 0) {
      $form->add_error($npath.'/default',
        translations::get('Field "%%_title" can not be blank!', ['title' => $title])
      );
      return;
    }

  # check minimum length
    if ($element->attribute_select('minlength') &&
        $element->attribute_select('minlength') > strlen($new_value)) {
      $form->add_error($npath.'/default',
        translations::get('Field "%%_title" contains too few characters!', ['title' => $title]).br.
        translations::get('Must be at least %%_value characters long.', ['value' => $element->attribute_select('minlength')])
      );
      return;
    }

  # check maximum length
    if ($element->attribute_select('maxlength') &&
        $element->attribute_select('maxlength') < strlen($new_value)) {
      $form->add_error($npath.'/default',
        translations::get('Field "%%_title" contains too much characters!', ['title' => $title]).br.
        translations::get('Must be no more than %%_value characters.', ['value' => $element->attribute_select('maxlength')]).br.
        translations::get('The value was trimmed to the required length!').br.
        translations::get('Check field again before submit.')
      );
    # trim value to maximum lenght
      $new_value = substr($new_value, 0, $element->attribute_select('maxlength'));
      return;
    }

  # number validation matrix - [number('...') => is_valid(0|1), ...]
  # ─────────────────────────────────────────────────────────────────────
  # ''   => 0, '-'   => 0, '0'   => 1, '-0'   => 0, '1'   => 1, '-1'   => 1, '01'   => 0, '-01'   => 0, '12'   => 1, '-12'   => 1, '012'   => 0, '-012'   => 0,
  # '.'  => 0, '-.'  => 0, '0.'  => 0, '-0.'  => 0, '1.'  => 0, '-1.'  => 0, '01.'  => 0, '-01.'  => 0, '12.'  => 0, '-12.'  => 0, '012.'  => 0, '-012.'  => 0,
  # '.0' => 0, '-.0' => 0, '0.1' => 1, '-0.1' => 1, '1.2' => 1, '-1.2' => 1, '01.2' => 0, '-01.2' => 0, '12.3' => 1, '-12.3' => 1, '012.3' => 0, '-012.3' => 0,
  # ─────────────────────────────────────────────────────────────────────

    if ($element->attribute_select('type') == 'number' ||
        $element->attribute_select('type') == 'range') {

    # check number value
      if (!preg_match('%^(?<integer>[-]?[1-9][0-9]*|0)$|'.
                       '^(?<float_s>[-]?[0-9][.][0-9]+)$|'.
                       '^(?<float_l>[-]?[1-9][0-9]+[.][0-9]+)$%S', $new_value)) {
        $form->add_error($npath.'/default',
          translations::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translations::get('Field value is not a valid number.')
        );
        return;
      }

    # check min value
      if ($element->attribute_select('min') !== null &&
          $element->attribute_select('min') > $new_value) {
        $form->add_error($npath.'/default',
          translations::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translations::get('Field value is less than %%_value.', ['value' => $element->attribute_select('min')])
        );
        return;
      }
    
    # check max value
      if ($element->attribute_select('max') !== null &&
          $element->attribute_select('max') < $new_value) {
        $form->add_error($npath.'/default',
          translations::get('Field "%%_title" contains incorrect value!', ['title' => $title]).br.
          translations::get('Field value is more than %%_value.', ['value' => $element->attribute_select('max')])
        );
        return;
      }
    }

  # check email field
    if ($element->attribute_select('type') == 'email' &&
        filter_var($new_value, FILTER_VALIDATE_EMAIL) == false) {
      $form->add_error($npath.'/default',
        translations::get('Field "%%_title" contains an incorrect email address!', ['title' => $title])
      );
      return;
    }
  }

  #####################
  ### form: install ###
  #####################

  static function on_validate_install($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'install':
        if (count($form->errors) == 0) {
          $db = storages::get('db');
          $result = $db->test([
            'driver'        => $values['driver'],
            'host_name'     => $values['host_name'],
            'database_name' => $values['database_name'],
            'user_name'     => $values['user_name'],
            'password'      => $values['password']
          ]);
          if (!$result) {
            $form->add_error('fieldset_default/field_database_name/default');
            $form->add_error('fieldset_default/field_host_name/default');
            $form->add_error('fieldset_default/field_user_name/default');
            $form->add_error('fieldset_default/field_password/default');
            messages::add_new('The database is not available with these credentials!', 'error');
          }
        }
        break;
    }
  }

  static function on_submit_install($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'install':
        events::start('on_module_install');
        messages::add_new('Modules was installed.');
        break;
      case 'cancel':
        urls::go(urls::get_back_url() ?: '/');
        break;
    }
  }

}}