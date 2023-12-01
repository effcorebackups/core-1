<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Widget_Selection_field_settings extends Container {

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
        $widget_text_object_title = new Widget_Text_object;
        $widget_text_object_title->cform = $widget->parent_widget->cform;
        $widget_text_object_title->name_complex = $widget->parent_widget->name_get_complex().'__title__'.$c_row_id;
        $widget_text_object_title->attributes['data-role'] = 'title';
        $widget_text_object_title->field_text_title = 'Title';
        $widget_text_object_title->field_text_required = false;
        $widget_text_object_title->build();
        $widget_text_object_title->value_set($item->title instanceof Text ?
                                             $item->title :
                                    new Text($item->title));
        # control for format type
        $field_format = new Field_Select;
        $field_format->cform = $widget->parent_widget->cform;
        $field_format->title = 'Format';
        $field_format->items_set([
            ''             => 'by field type in the DB',
            'raw'          => ' RAW',
            'boolean'      => ' boolean',
            'real'         => ' real',
            'integer'      => ' integer',
            'time'         => ' time',
            'date'         => ' date',
            'datetime'     => ' datetime',
            'time_utc'     => ' time_utc',
            'date_utc'     => ' date_utc',
            'datetime_utc' => ' datetime_utc']);
        $field_format->build();
        $field_format->required_set(false);
        $field_format->name_set($widget->parent_widget->name_get_complex().'__format__'.$c_row_id);
        $field_format->value_set($item->format ?? '');
        # control for value settings
        $group_value_settings = new Group_Checkboxes;
        $group_value_settings->title = 'Value settings';
        $group_value_settings->attributes['data-role'] = 'value-settings';
        $group_value_settings->element_attributes['name'] = $widget->parent_widget->name_get_complex().'__value_settings__'.$c_row_id.'[]';
        $group_value_settings->items_set([
            'is_apply_translation' => 'Is apply translation',
            'is_apply_tokens'      => 'Is apply tokens',
            'is_not_visible'       => 'Is not visible']);
        $group_value_settings->build();
        $value_settings = [];
        if (!empty($item->is_apply_translation)) $value_settings['is_apply_translation'] = 'is_apply_translation';
        if (!empty($item->is_apply_tokens     )) $value_settings['is_apply_tokens'     ] = 'is_apply_tokens';
        if (!empty($item->is_not_visible      )) $value_settings['is_not_visible'      ] = 'is_not_visible';
        $group_value_settings->value_set($value_settings);
        # relate new controls with the widget
        $widget->controls['#title__'         .$c_row_id] = $widget_text_object_title;
        $widget->controls['#format__'        .$c_row_id] = $field_format;
        $widget->controls['*value_settings__'.$c_row_id] = $group_value_settings;
        $result->child_insert($widget_text_object_title, 'widget_text_object_title');
        $result->child_insert($field_format            , 'field_format');
        $result->child_insert($group_value_settings    , 'group_value_settings');
        return $result;
    }

    static function on_request_value_set($widget, $form, $npath) {
        $items = $widget->parent_widget->items_get();
        $value_settings                   = $widget->controls['*value_settings__'.$widget->c_row_id]->value_get();
        $items[$widget->c_row_id]->title  = $widget->controls['#title__'         .$widget->c_row_id]->value_get();
        $items[$widget->c_row_id]->format = $widget->controls['#format__'        .$widget->c_row_id]->value_get();
        $items[$widget->c_row_id]->is_apply_translation = isset($value_settings['is_apply_translation']);
        $items[$widget->c_row_id]->is_apply_tokens      = isset($value_settings['is_apply_tokens'     ]);
        $items[$widget->c_row_id]->is_not_visible       = isset($value_settings['is_not_visible'      ]);
        $widget->parent_widget->items_set($items);
    }

}
