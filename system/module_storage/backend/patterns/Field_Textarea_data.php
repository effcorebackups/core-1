<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_Textarea_data extends Field_Textarea {

    public $title = 'Data in "data" format';
    public $classes_allowed = [];
    public $data_validator_id;

    function value_data_get() {
        if ($this->value_get()) {
            return Storage_Data::text_to_data($this->value_get(), $this->classes_allowed)->data;
        }
    }

    function value_data_set($value, $root_name) {
        if ($value) {
            $this->value_set(Storage_Data::data_to_text($value, $root_name));
        }
    }

    function render_description() {
        $classes = [];
        foreach (array_filter($this->classes_allowed, 'strlen') as $c_class_name) {
            if (Core::structure_is_local($c_class_name))
                 $classes[$c_class_name] = '|'.substr($c_class_name, strlen('\\effcore\\'));
            else $classes[$c_class_name] = '|'.       $c_class_name; }
        $this->description = static::description_prepare($this->description);
        if (!isset($this->description['classes-allowed']) && count($classes))
                   $this->description['classes-allowed'] = new Markup('p', ['data-id' => 'classes-allowed'], new Text('Field value can contain only the next classes: %%_classes', ['classes' => implode(', ', $classes)]));
        return parent::render_description();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function validate_value($field, $form, $element, &$new_value) {
        if (strlen($new_value)) {
            $parsed_result = Storage_Data::text_to_data($new_value, $field->classes_allowed);
            if (count($parsed_result->errors)) {
                foreach ($parsed_result->errors as $c_error) {
                    switch ($c_error->code) {
                        case Storage_Data::ERR_CODE_EMPTY_LINE_WAS_FOUND:
                            $field->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'An empty line was found.',
                                'Line: %%_line'], [
                                'title' => (new Text($field->title))->render(),
                                'line'  => $c_error->line]));
                            break;
                        case Storage_Data::ERR_CODE_LEADING_TAB_CHARACTER_FOUND:
                            $field->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Leading tab character found.',
                                'Line: %%_line'], [
                                'title' => (new Text($field->title))->render(),
                                'line'  => $c_error->line]));
                            break;
                        case Storage_Data::ERR_CODE_INDENT_SIZE_IS_NOT_EVEN:
                            $field->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Indent size is not even.',
                                'Line: %%_line'], [
                                'title' => (new Text($field->title))->render(),
                                'line'  => $c_error->line]));
                            break;
                        case Storage_Data::ERR_CODE_INDENT_OVERSIZE:
                            $field->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Indent oversize is detected.',
                                'Line: %%_line'], [
                                'title' => (new Text($field->title))->render(),
                                'line'  => $c_error->line]));
                            break;
                        case Storage_Data::ERR_CODE_CLASS_WAS_NOT_FOUND:
                            $field->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Class "%%_class_name" was not found.',
                                'Line: %%_line'], [
                                'title'      => (new Text($field->title))->render(),
                                'line'       => $c_error->line,
                                'class_name' => $c_error->args['class_name']]));
                            break;
                        case Storage_Data::ERR_CODE_CLASS_NOT_ALLOWED:
                            $field->error_set(new Text_multiline([
                                'Field "%%_title" contains an error!',
                                'Class "%%_class_name" not allowed.',
                                'Line: %%_line'], [
                                'title'      => (new Text($field->title))->render(),
                                'line'       => $c_error->line,
                                'class_name' => $c_error->args['class_name']]));
                            break;
                    }
                }
            } else {
                if ($field->data_validator_id) {
                    $validate_result = Data_validator::get($field->data_validator_id)->validate($parsed_result->data);
                    if (count($validate_result['errors'])) {
                        foreach ($validate_result['errors'] as $c_error_message)
                            $field->error_set($c_error_message);
                    } else return true;
                }     else return true;
            }
        } else {
            return true;
        }
    }

}
