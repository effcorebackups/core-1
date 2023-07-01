<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

class Widget_Attributes extends Widget_Items {

    public $title = 'Attributes';
    public $item_title = 'Attribute';
    public $attributes = ['data-type' => 'items-attributes'];
    public $name_complex = 'widget_attributes';
    public $state = 'closed';
    public $attribute_name_maxlength = 0xff;
    public $attribute_value_maxlength = 0xffff;

    ###########################
    ### static declarations ###
    ###########################

    static function value_to_attributes($value) {
        if ($value) {
            Core::array_sort_by_number($value);
            $attributes = [];
            foreach ($value as $c_item)
                $attributes[$c_item->name] = new Text(
                            $c_item->value, [],
                     !empty($c_item->is_apply_translation),
                     !empty($c_item->is_apply_tokens));
            return $attributes;
        }
    }

    static function value_to_markup($value) {
        if ($value) {
            return Core::data_to_attributes(
                static::value_to_attributes($value)
            );
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = parent::widget_manage_get($widget, $item, $c_row_id);
        # control for attribute name
        $field_text_name = new Field_Text('Name', null, [], +400);
        $field_text_name->cform = $widget->cform;
        $field_text_name->attributes['data-role'] = 'name';
        $field_text_name->attributes['data-style'] = 'inline';
        $field_text_name->description_state = 'hidden';
        $field_text_name->build();
        $field_text_name->name_set($widget->name_get_complex().'__name__'.$c_row_id);
        $field_text_name->maxlength_set($widget->attribute_name_maxlength);
        $field_text_name->value_set($item->name);
        # control for attribute value
        $widget_text_object_value = new Widget_Text_object;
        $widget_text_object_value->cform = $widget->cform;
        $widget_text_object_value->name_complex = $widget->name_get_complex().'__'.$c_row_id;
        $widget_text_object_value->attributes['data-role'] = 'value';
        $widget_text_object_value->field_text_title = null;
        $widget_text_object_value->field_text_maxlength = $widget->attribute_value_maxlength;
        $widget_text_object_value->field_text_required = false;
        $widget_text_object_value->build();
        $widget_text_object_value->value_set(new Text($item->value, [],
            !empty($item->is_apply_translation),
            !empty($item->is_apply_tokens)));
        # relate new controls with the widget
        $widget->controls['#name__'. $c_row_id] = $field_text_name;
        $widget->controls['#value__'.$c_row_id] = $widget_text_object_value;
        $result->child_insert($field_text_name,          'field_text_name');
        $result->child_insert($widget_text_object_value, 'widget_text_object_value');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_button_click_insert($widget, $form, $npath, $button) {
        $min_weight = 0;
        $items = $widget->items_get();
        foreach ($items as $c_row_id => $c_item)
            $min_weight = min($min_weight, $c_item->weight);
        $new_item = new stdClass;
        $new_item->weight               = count($items) ? $min_weight - 5 : 0;
        $new_item->name                 = '';
        $new_item->value                = '';
        $new_item->is_apply_translation = false;
        $new_item->is_apply_tokens      = false;
        $items[] = $new_item;
        $widget->items_set($items);
        Message::insert(new Text_multiline([
            'Item of type "%%_type" was inserted.',
            'Do not forget to save the changes!'], [
            'type' => (new Text($widget->item_title))->render() ]));
        return true;
    }

    static function on_request_value_set($widget, $form, $npath) {
        $items = $widget->items_get();
        foreach ($items as $c_row_id => $c_item) {
            $c_item->weight = (int)$widget->controls['#weight__'.$c_row_id]->value_get();
            $c_item->name   =      $widget->controls['#name__'  .$c_row_id]->value_get();
            $c_value        =      $widget->controls['#value__' .$c_row_id]->value_get();
            if ($c_value instanceof Text) {
                $c_item->value                = $c_value->text;
                $c_item->is_apply_translation = $c_value->is_apply_translation;
                $c_item->is_apply_tokens      = $c_value->is_apply_tokens; }}
        $widget->items_set($items);
    }

}
