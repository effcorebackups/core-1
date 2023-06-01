<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class widget_selection_field_settings extends container {

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
        $form_id      = request::value_get('form_id');
        $submit_value = request::value_get($html_name);
        $has_error    = $this->has_error_in();
        if ($form_id === ''                                                 ) /*               default = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
        if ($form_id !== '' && $has_error !== true && $submit_value !== 'on') /* no error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
        if ($form_id !== '' && $has_error !== true && $submit_value === 'on') /* no error +    checked = closed */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
        if ($form_id !== '' && $has_error === true && $submit_value !== 'on') /*    error + no checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
        if ($form_id !== '' && $has_error === true && $submit_value === 'on') /*    error +    checked = opened */ return (new markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'settings', 'title' => new text('press to show more settings'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = new node;
        # control for title
        $widget_text_object_title = new widget_text_object;
        $widget_text_object_title->cform = $widget->parent_widget->cform;
        $widget_text_object_title->name_complex = $widget->parent_widget->name_get_complex().'__title__'.$c_row_id;
        $widget_text_object_title->attributes['data-role'] = 'title';
        $widget_text_object_title->field_text_title = 'Title';
        $widget_text_object_title->field_text_required = false;
        $widget_text_object_title->build();
        $widget_text_object_title->value_set($item->title instanceof text ?
                                             $item->title :
                                    new text($item->title));
        # control for value settings
        $group_value_settings = new group_checkboxes;
        $group_value_settings->title = 'Value settings';
        $group_value_settings->attributes['data-role'] = 'value-settings';
        $group_value_settings->element_attributes['name'] = $widget->parent_widget->name_get_complex().'__value_settings__'.$c_row_id.'[]';
        $group_value_settings->items_set([
            'is_apply_translation' => 'Is apply translation',
            'is_apply_tokens'      => 'Is apply tokens',
            'is_trimmed'           => 'Is trimmed',
            'is_not_formatted'     => 'Is do not apply formatting',
            'is_not_visible'       => 'Is not visible']);
        $group_value_settings->build();
        $group_value_settings->value_set($item->value_settings ?? []);
        # relate new controls with the widget
        $widget->controls['#title__'         .$c_row_id] = $widget_text_object_title;
        $widget->controls['*value_settings__'.$c_row_id] = $group_value_settings;
        $result->child_insert($widget_text_object_title, 'widget_text_object_title');
        $result->child_insert($group_value_settings,     'group_value_settings');
        return $result;
    }

    static function on_request_value_set($widget, $form, $npath) {
        $items = $widget->parent_widget->items_get();
        $items[$widget->c_row_id]->title = $widget->controls['#title__'.$widget->c_row_id]->value_get();
        $items[$widget->c_row_id]->value_settings = $widget->controls['*value_settings__'.$widget->c_row_id]->value_get();
        $widget->parent_widget->items_set($items);
    }

}
