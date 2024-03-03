<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Group_Palette extends Group_Radiobuttons {

    public $title_tag_name = 'label';
    public $required_any = true;
    public $attributes = [
        'data-type' => 'palette',
        'role'      => 'group'
    ];

    function build() {
        if (!$this->is_builded) {
            $this->items = static::items_generate();
            parent::build();
        }
    }

    function render_self() {
        $html_name = 'f_opener_'.$this->name_get_complex();
        if ($this->title && (bool)$this->title_is_visible !== true) return $this->render_opener().(new Markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name, 'aria-hidden' => 'true'], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
        if ($this->title && (bool)$this->title_is_visible === true) return $this->render_opener().(new Markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name                         ], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
    }

    function render_opener() {
        $color_id        = $this->value_get() ?: 'white';
        $color_value_hex = Color::get($color_id)->value_hex ?: '#ffffff';
        $html_name       = 'f_opener_'.$this->name_get_complex();
        $is_submited     = Form::is_posted();
        $submit_value    = Request::value_get($html_name);
        $has_error       = $this->has_error_in();
        if ($is_submited !== true                                                      ) /*               default = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => true,                           'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($is_submited === true && $has_error !== true && $submit_value !== $color_id) /* no error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => null,                           'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($is_submited === true && $has_error !== true && $submit_value === $color_id) /* no error +    checked = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => true,                           'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($is_submited === true && $has_error === true && $submit_value !== $color_id) /*    error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true', 'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
        if ($is_submited === true && $has_error === true && $submit_value === $color_id) /*    error +    checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'palette', 'title' => new Text('press to show or hide available colors'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true', 'value' => $color_id, 'style' => ['background: '.$color_value_hex]]))->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function items_generate() {
        $result = [];
        foreach (Color::get_all() as $c_color) {
            $c_color_value_hex = $c_color->value_hex ?: '#ffffff';
            $c_color_value     = $c_color->value_hex ?: 'transparent';
            $result[$c_color->id] = new stdClass;
            $result[$c_color->id]->title = '';
            $result[$c_color->id]->description = null;
            $result[$c_color->id]->weight = +0;
            $result[$c_color->id]->group = $c_color->group;
            $result[$c_color->id]->element_attributes = [
                'value' => $c_color->id,
                'title' => (new Text('color ID = "%%_id" and value = "%%_value"', ['id' => $c_color->id, 'value' => $c_color_value]))->render(),
                'style' => ['background-color: '.$c_color_value_hex]
            ];
        }
        return $result;
    }

}
