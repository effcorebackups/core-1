<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Fieldset extends Container {

    public $tag_name = 'fieldset';
    public $title_tag_name = 'label';
    public $title_attributes = [
        'data-fieldset-title' => true];
    public $content_tag_name = 'x-fieldset-content';
    public $content_attributes = [
        'data-fieldset-content' => true,
        'data-nested-content'   => true
    ];
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $title_position = 'top'; # opener not working in 'bottom' mode
    public $state = ''; # '' | opened | closed[checked]
    public $number;

    function __construct($title = null, $description = null, $attributes = [], $children = [], $weight = +0) {
        parent::__construct(null, $title, $description, $attributes, $children, $weight);
    }

    function build() {
        if (!$this->is_builded) {
            if ($this->number === null)
                $this->number = static::current_number_generate();
            $this->is_builded = true;
        }
    }

    function render() {
        $this->build();
        return parent::render();
    }

    function render_self() {
        if ($this->title) {
            $html_name = 'f_opener_'.$this->number;
            $opener = $this->render_opener();
            if ((bool)$this->title_is_visible === true && $opener !== '') return $opener.(new Markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name                         ], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
            if ((bool)$this->title_is_visible !== true && $opener !== '') return $opener.(new Markup($this->title_tag_name, $this->title_attributes + ['for' => $html_name, 'aria-hidden' => 'true'], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
            if ((bool)$this->title_is_visible !== true && $opener === '') return         (new Markup($this->title_tag_name, $this->title_attributes + [                     'aria-hidden' => 'true'], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
            if ((bool)$this->title_is_visible === true && $opener === '') return         (new Markup($this->title_tag_name, $this->title_attributes + [                                            ], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
        }
    }

    function render_opener() {
        if ($this->state === 'opened' ||
            $this->state === 'closed') {
            $html_name    = 'f_opener_'.$this->number;
            $is_submited  = Form::is_posted();
            $submit_value = Request::value_get($html_name);
            $has_error    = $this->has_error_in();
            if ($is_submited !== true && $this->state === 'opened'                    ) /*               default = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'title', 'title' => new Text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
            if ($is_submited !== true && $this->state === 'closed'                    ) /*               default = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'title', 'title' => new Text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
            if ($is_submited === true && $has_error !== true && $submit_value !== 'on') /* no error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'title', 'title' => new Text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null                          ]))->render();
            if ($is_submited === true && $has_error !== true && $submit_value === 'on') /* no error +    checked = closed */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'title', 'title' => new Text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => true                          ]))->render();
            if ($is_submited === true && $has_error === true && $submit_value !== 'on') /*    error + no checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'title', 'title' => new Text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
            if ($is_submited === true && $has_error === true && $submit_value === 'on') /*    error +    checked = opened */ return (new Markup_simple('input', ['type' => 'checkbox', 'data-opener-type' => 'title', 'title' => new Text('press to show or hide nested content'), 'name' => $html_name, 'id' => $html_name, 'checked' => null, 'aria-invalid' => 'true']))->render();
        }
        return '';
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $c_number = 0;

    static function current_number_generate() {
        return static::$c_number++;
    }

}
