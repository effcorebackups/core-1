<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Group_Palette extends Group_Radiobuttons {

    public $title_tag_name = 'label';
    public $required_any = true;
    public $attributes = [
        'data-type' => 'palette',
        'role'      => 'group'
    ];

    function build() {
        if (!$this->is_builded) {
            $previous_group_name = '';
            foreach (Color::get_all() as $c_color) {
                if ($previous_group_name !== '' &&
                    $previous_group_name !== $c_color->group) $this->child_insert(HR);
                    $previous_group_name  =  $c_color->group;
                if (!$this->child_select($c_color->id)) {
                    $c_color_id        = $c_color->id;
                    $c_color_value_hex = $c_color->value_hex ?: '#ffffff';
                    $c_color_value     = $c_color->value_hex ?: 'transparent';
                    $c_element_attributes = [
                        'value' => $c_color_id,
                        'title' => new Text('color ID = "%%_id" and value = "%%_value"', ['id' => $c_color_id, 'value' => $c_color_value]),
                        'style' => ['background-color: '.$c_color_value_hex]];
                    $c_field                     = new $this->field_class;
                    $c_field->tag_name           = $this->field_tag_name;
                    $c_field->title_tag_name     = $this->field_title_tag_name;
                    $c_field->title_position     = $this->field_title_position;
                    $c_field->title              = null;
                    $c_field->description        = null;
                    $c_field->element_attributes = $c_element_attributes + $this->attributes_select('element_attributes') + $c_field->attributes_select('element_attributes');
                    $c_field->weight             = 0;
                    $c_field->build();
                    $c_field->required_set(isset($this->required[$c_color->id]));
                    $c_field-> checked_set(isset($this->checked [$c_color->id]));
                    $c_field->disabled_set(isset($this->disabled[$c_color->id]));
                    $this->child_insert($c_field, $c_color->id);
                }
            }
            $this->is_builded = true;
        }
    }

    function render_self() {
        $html_name = 'f_opener_'.$this->name_get_complex();
        if ($this->title && (bool)$this->title_is_visible !== true) return $this->render_opener().(new Markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name, 'aria-hidden' => 'true'], $this->title))->render();
        if ($this->title && (bool)$this->title_is_visible === true) return $this->render_opener().(new Markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name                         ], $this->title))->render();
    }

    function render_opener() {
        $color_id        = $this->value_get() ?: 'white';
        $color_value_hex = Color::get($color_id)->value_hex ?: '#ffffff';
        $html_name       = 'f_opener_'.$this->name_get_complex();
        $form_id         = Request::value_get('form_id');
        $submit_value    = Request::value_get($html_name);
        $has_error       = $this->has_error_in();
        if ($form_id === ''                                                      ) /*               default = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => true,                           'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($form_id !== '' && $has_error !== true && $submit_value !== $color_id) /* no error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => null,                           'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($form_id !== '' && $has_error !== true && $submit_value === $color_id) /* no error +    checked = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => true,                           'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($form_id !== '' && $has_error === true && $submit_value !== $color_id) /*    error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true', 'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($form_id !== '' && $has_error === true && $submit_value === $color_id) /*    error +    checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'role' => 'button', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true', 'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
    }

}
