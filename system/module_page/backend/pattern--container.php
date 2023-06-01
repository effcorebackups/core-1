<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class container extends markup {

    public $tag_name = 'x-container';
    public $template = 'container';
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $title;
    public $title_tag_name = 'x-title';
    public $title_position = 'top';
    public $title_attributes = [];
    public $title_is_visible = true;
    public $content_tag_name;
    public $content_attributes = [];
    public $description;
    public $description_tag_name = 'x-description';
    public $description_position = 'bottom';

    function __construct($tag_name = null, $title = null, $description = null, $attributes = [], $children = [], $weight = 0) {
        if ($title !== null) $this->title       = $title;
        if ($description   ) $this->description = $description;
        parent::__construct($tag_name, $attributes, $children, $weight);
    }

    function render() {
        $is_bottom_title    = !empty($this->title_position)       && $this->title_position       === 'bottom';
        $is_top_description = !empty($this->description_position) && $this->description_position === 'top';
        return (template::make_new($this->template, [
            'tag_name'      => $this->tag_name,
            'attributes'    => $this->render_attributes(),
            'self_t'        => $is_bottom_title    ? '' : $this->render_self(),
            'self_b'        => $is_bottom_title    ?      $this->render_self()        : '',
            'description_t' => $is_top_description ?      $this->render_description() : '',
            'description_b' => $is_top_description ? '' : $this->render_description(),
            'children'      => $this->content_tag_name ? (new markup($this->content_tag_name, $this->content_attributes,
                               $this->render_children($this->children_select(true)) ))->render() :
                               $this->render_children($this->children_select(true))
        ]))->render();
    }

    function render_self() {
        if ($this->title && (bool)$this->title_is_visible !== true) return (new markup($this->title_tag_name, $this->title_attributes + ['data-mark-required' => $this->attribute_select('required') ? true : null, 'aria-hidden' => 'true'], $this->title))->render();
        if ($this->title && (bool)$this->title_is_visible === true) return (new markup($this->title_tag_name, $this->title_attributes + ['data-mark-required' => $this->attribute_select('required') ? true : null                         ], $this->title))->render();
    }

    function render_description() {
        $this->description = static::description_prepare($this->description);
        if (count($this->description)) {
            return (new markup($this->description_tag_name, [], $this->description))->render();
        }
    }

    # ─────────────────────────────────────────────────────────────────────
    # functionality for errors
    # ─────────────────────────────────────────────────────────────────────

    function has_error_in($root = null) {
        foreach (($root ?: $this)->children_select_recursive() as $c_child) {
            if ($c_child instanceof field &&
                $c_child->has_error()) {
                return true;
            }
        }
    }

    function error_set_in($root = null) {
        foreach (($root ?: $this)->children_select_recursive() as $c_child) {
            if ($c_child instanceof field) {
                $c_child->error_set();
            }
        }
    }

}
