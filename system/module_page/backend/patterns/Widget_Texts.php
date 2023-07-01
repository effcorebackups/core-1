<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

class Widget_Texts extends Widget_Items {

    public $attributes = ['data-type' => 'items-texts'];
    public $name_complex = 'widget_texts';

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = parent::widget_manage_get($widget, $item, $c_row_id);
        # control for text
        $field_text = new Field_Text;
        $field_text->cform = $widget->cform;
        $field_text->attributes['data-role'] = 'question';
        $field_text->attributes['data-style'] = 'inline';
        $field_text->description_state = 'hidden';
        $field_text->build();
        $field_text->name_set($widget->name_get_complex().'__text__'.$c_row_id);
        $field_text->value_set($item->text ?? '');
        # relate new controls with the widget
        $widget->controls['#text__'.$c_row_id] = $field_text;
        $result->child_insert($field_text, 'field_text');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_button_click_insert($widget, $form, $npath, $button) {
        $min_weight = 0;
        $items = $widget->items_get();
        foreach ($items as $c_row_id => $c_item)
            $min_weight = min($min_weight, $c_item->weight);
        $new_item = new stdClass;
        $new_item->weight = count($items) ? $min_weight - 5 : 0;
        $new_item->id = 0;
        $new_item->text = '';
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
            $c_item->text   =      $widget->controls['#text__'  .$c_row_id]->value_get(); }
        $widget->items_set($items);
    }

}
