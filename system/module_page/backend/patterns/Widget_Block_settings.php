<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Widget_Block_settings extends Container {

    public $tag_name = null;
    public $content_tag_name = 'x-settings';
    public $template = 'container_content';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $parent_widget;
    public $item;
    public $c_row_id;

    function __construct($parent_widget, $item, $c_row_id) {
        $this->parent_widget = $parent_widget;
        $this->item          = $item;
        $this->c_row_id      = $c_row_id;
        parent::__construct(null, null, null, [], [], 0);
    }

    function build() {
        if (!$this->is_builded) {
            $this->child_insert(static::widget_manage_get($this, $this->item, $this->c_row_id), 'manage');
            $this->is_builded = true;
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function render_self() {
        return $this->render_opener();
    }

    function render_opener() {
        $html_name    = $this->parent_widget->name_get_complex().'__settings_opener__'.$this->c_row_id;
        $form_id      = Request::value_get('form_id');
        $submit_value = Request::value_get($html_name);
        $has_error    = $this->has_error_in();
        if ($form_id === ''                                                 ) /*               default = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new Text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
        if ($form_id !== '' && $has_error !== true && $submit_value !== 'on') /* no error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new Text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
        if ($form_id !== '' && $has_error !== true && $submit_value === 'on') /* no error +    checked = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new Text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
        if ($form_id !== '' && $has_error === true && $submit_value !== 'on') /*    error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new Text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
        if ($form_id !== '' && $has_error === true && $submit_value === 'on') /*    error +    checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new Text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = new Node;
        # control for title
        $field_title = new Widget_Text_object;
        $field_title->cform = $widget->parent_widget->cform;
        $field_title->name_complex = $widget->parent_widget->name_get_complex().'__title__'.$c_row_id;
        $field_title->attributes['data-role'] = 'title';
        $field_title->field_text_title = 'Title';
        $field_title->field_text_required = false;
        $field_title->build();
        $field_title->value_set($item->title instanceof Text ?
                                $item->title :
                       new Text($item->title));
        # control for title visibility
        $field_title_is_visible = new Field_Select_logic;
        $field_title_is_visible->cform = $widget->parent_widget->cform;
        $field_title_is_visible->attributes['data-role'] = 'title-is_visible';
        $field_title_is_visible->title = 'Title is visible';
        $field_title_is_visible->build();
        $field_title_is_visible->name_set($widget->parent_widget->name_get_complex().'__title_is_visible__'.$c_row_id);
        $field_title_is_visible->value_set($item->title_is_visible ?? false);
        # control for attributes
        $field_textarea_data_attributes = new Field_Textarea_data;
        $field_textarea_data_attributes->cform = $widget->parent_widget->cform;
        $field_textarea_data_attributes->attributes['data-role'] = 'data-attributes';
        $field_textarea_data_attributes->title = 'Attributes';
        $field_textarea_data_attributes->classes_allowed['Text'] = 'Text';
        $field_textarea_data_attributes->classes_allowed['Text_simple'] = 'Text_simple';
        $field_textarea_data_attributes->data_validator_id = 'attributes';
        $field_textarea_data_attributes->build();
        $field_textarea_data_attributes->name_set($widget->parent_widget->name_get_complex().'__attributes__'.$c_row_id);
        $field_textarea_data_attributes->value_data_set($item->attributes ?? null, 'attributes');
        $field_textarea_data_attributes->required_set(false);
        $field_textarea_data_attributes->maxlength_set(0xffff);
        # relate new controls with the widget
        $widget->controls['#title__'           .$c_row_id] = $field_title;
        $widget->controls['#title_is_visible__'.$c_row_id] = $field_title_is_visible;
        $widget->controls['#attributes__'      .$c_row_id] = $field_textarea_data_attributes;
        $result->child_insert($field_title,                    'field_title');
        $result->child_insert($field_title_is_visible,         'field_title_is_visible');
        $result->child_insert($field_textarea_data_attributes, 'field_textarea_data_attributes');
        return $result;
    }

    static function on_request_value_set($widget, $form, $npath) {
        $items = $widget->parent_widget->items_get();
        $items[$widget->c_row_id]->title            = $widget->controls['#title__'           .$widget->c_row_id]->     value_get();
        $items[$widget->c_row_id]->title_is_visible = $widget->controls['#title_is_visible__'.$widget->c_row_id]->     value_get();
        $items[$widget->c_row_id]->attributes       = $widget->controls['#attributes__'      .$widget->c_row_id]->value_data_get()->attributes ?? [];
        $widget->parent_widget->items_set($items);
    }

}
