<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_textarea_data extends field_textarea {

  public $title = 'Data in "data" format';
  public $classes_allowed = [];
  public $class_std_by_default = true;

  function value_data_get() {
    return storage_nosql_data::text_to_data($this->value_get(), $this->classes_allowed)->data;
  }

  function value_data_set($value, $root_name) {
    if ($value) {
      $this->value_set(storage_nosql_data::data_to_text($value, $root_name));
    }
  }

  function render_description() {
    $classes = [];
    foreach (array_filter($this->classes_allowed, 'strlen') as $c_class_name) {
      if (core::structure_is_local($c_class_name))
           $classes[$c_class_name] = '|'.substr($c_class_name, strlen('\\effcore\\'));
      else $classes[$c_class_name] = '|'.       $c_class_name;
    }
    $this->description = static::description_prepare($this->description);
    if (!isset($this->description['classes-allowed']) && count($classes))
               $this->description['classes-allowed'] = new markup('p', ['data-id' => 'classes-allowed'], new text('Field value can contain only the next classes: %%_classes', ['classes' => implode(', ', $classes)]));
    return parent::render_description();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate_value($field, $form, $element, &$new_value) {
    $result = storage_nosql_data::text_to_data($new_value, $field->classes_allowed);
    if (count($result->errors)) {
      foreach ($result->errors as $c_error) {
        switch ($c_error->code) {
          case storage_nosql_data::ERR_CODE_EMPTY_LINE_WAS_FOUND:
            $field->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'An empty line was found.',
              'Line: %%_line'], [
              'title' => (new text($field->title))->render(),
              'line'  => $c_error->line]));
            break;
          case storage_nosql_data::ERR_CODE_LEADING_TAB_CHARACTER_FOUND:
            $field->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'Leading tab character found.',
              'Line: %%_line'], [
              'title' => (new text($field->title))->render(),
              'line'  => $c_error->line]));
            break;
          case storage_nosql_data::ERR_CODE_INDENT_SIZE_IS_NOT_EVEN:
            $field->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'Indent size is not even.',
              'Line: %%_line'], [
              'title' => (new text($field->title))->render(),
              'line'  => $c_error->line]));
            break;
          case storage_nosql_data::ERR_CODE_INDENT_OVERSIZE:
            $field->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'Indent oversize is detected.',
              'Line: %%_line'], [
              'title' => (new text($field->title))->render(),
              'line'  => $c_error->line]));
            break;
          case storage_nosql_data::ERR_CODE_CLASS_WAS_NOT_FOUND:
            $field->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'Class "%%_classname" was not found.',
              'Line: %%_line'], [
              'title'     => (new text($field->title))->render(),
              'line'      => $c_error->line,
              'classname' => $c_error->args['classname']]));
            break;
          case storage_nosql_data::ERR_CODE_CLASS_NOT_ALLOWED:
            $field->error_set(new text_multiline([
              'Field "%%_title" contains an error!',
              'Class "%%_classname" not allowed.',
              'Line: %%_line'], [
              'title'     => (new text($field->title))->render(),
              'line'      => $c_error->line,
              'classname' => $c_error->args['classname']]));
            break;
        }
      }
    } else {
      return true;
    }
  }

}}