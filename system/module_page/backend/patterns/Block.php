<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Block extends Markup {

    public $tag_name = 'section';
    public $template = 'block';
    public $attributes = [
        'data-block' => true];
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $title;
    public $title_is_visible = 1;
    public $title_is_apply_translation = 1;
    public $title_is_apply_tokens = 0;
    public $title_tag_name = 'h2';
    public $title_attributes = [
        'data-block-title' => true];
    public $content_tag_name = 'x-section-content';
    public $content_attributes = [
        'data-block-content' => true];
    public $display;
    public $type; # copy | link | code | text
    public $source;
    public $properties = [];
    public $args       = [];
    public $header;
    public $footer;

    function __construct($title = null, $attributes = [], $children = [], $weight = +0) {
        if ($title) $this->title = $title;
        parent::__construct(null, $attributes, $children, $weight);
    }

    function build($page = null) {
        if (!$this->is_builded) {
            $result = null;
            Event::start('on_block_build_before', null, ['block' => &$this]);
            if (!isset($this->display) ||
                (isset($this->display) && $this->display->check === 'page_args' && preg_match($this->display->match,         $page->args_get($this->display->where))) ||
                (isset($this->display) && $this->display->check === 'user'      &&            $this->display->where === 'role' && preg_match($this->display->match.'m', implode(NL, User::get_current()->roles)))) {
                switch ($this->type) {
                    case 'copy':
                    case 'link': if ($this->type === 'copy') $result = Core::deep_clone(Storage::get('data')->select($this->source, true));
                                 if ($this->type === 'link') $result =                  Storage::get('data')->select($this->source, true);
                                 foreach ($this->properties      as     $c_key => $c_value)
                                     Core::arrobj_insert_value($result, $c_key,   $c_value);
                                 break;
                    case 'code': $result = @call_user_func_array($this->source, ['page' => $page, 'args' => $this->args]); break;
                    case 'text': $result = new Text($this->source); break;
                    default    : $result =          $this->source;
                }
            }
            if ($result) $this->child_insert($result, 'result');
            Event::start('on_block_build_after', null, ['block' => &$this]);
            $this->is_builded = true;
        }
    }

    function render() {
        if ($this->template) {
            return (Template::make_new(Template::pick_name($this->template), [
                'tag_name'   => $this->tag_name,
                'attributes' => $this->render_attributes(),
                'header'     => $this->render_header(),
                'self'       => $this->render_self(),
                'footer'     => $this->render_footer(),
                'children'   => $this->content_tag_name ? (new Markup($this->content_tag_name, $this->content_attributes,
                                $this->render_children($this->children_select(true)) ))->render() :
                                $this->render_children($this->children_select(true))
            ]))->render();
        } else {
            return $this->render_header().
                   $this->render_self().
                   $this->render_children($this->children_select(true)).
                   $this->render_footer();
        }
    }

    function render_self() {
        if ($this->title && (bool)$this->title_is_visible !== true) return (new Markup($this->title_tag_name, $this->title_attributes + ['aria-hidden' => 'true'], is_string($this->title) ? is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title : $this->title))->render();
        if ($this->title && (bool)$this->title_is_visible === true) return (new Markup($this->title_tag_name, $this->title_attributes + [                       ], is_string($this->title) ? is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title : $this->title))->render();
    }

    function render_header() {
        if ($this->header !== null) {
            if (is_string($this->header) || is_numeric($this->header)) return (new Text($this->header))->render();
            else                                                       return           $this->header  ->render();
        }
    }

    function render_footer() {
        if ($this->footer !== null) {
            if (is_string($this->footer) || is_numeric($this->footer)) return (new Text($this->footer))->render();
            else                                                       return           $this->footer  ->render();
        }
    }

}
