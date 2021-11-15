<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class block extends markup {

  public $tag_name = 'section';
  public $template = 'block';
  public $attributes = ['data-block' => true];
# ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
  public $title;
  public $title_tag_name = 'h2';
  public $title_attributes = ['data-block-title' => true];
  public $title_is_visible = 1;
  public $content_tag_name = 'x-section-content';
  public $content_attributes = ['data-block-content' => true];
  public $extra_t;
  public $extra_b;
  public $display;
  public $type; # copy | link | code | text
  public $source;
  public $properties = [];
  public $args       = [];

  function __construct($title = null, $attributes = [], $children = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct(null, $attributes, $children, $weight);
  }

  function build($page = null) {
    if (!$this->is_builded) {
      $result = null;
      event::start('on_block_build_before', null, ['block' => &$this]);
      if (!isset($this->display) ||
          (isset($this->display) && $this->display->check === 'page_args' && preg_match($this->display->match,         $page->args_get($this->display->where))) ||
          (isset($this->display) && $this->display->check === 'user'      &&            $this->display->where === 'role' && preg_match($this->display->match.'m', implode(nl, user::get_current()->roles)))) {
        switch ($this->type) {
          case 'copy':
          case 'link': if ($this->type === 'copy') $result = core::deep_clone(storage::get('data')->select($this->source, true));
                       if ($this->type === 'link') $result =                  storage::get('data')->select($this->source, true);
                       foreach ($this->properties     as    $c_key => $c_value)
                         core::arrobj_insert_value($result, $c_key,   $c_value);
                       break;
          case 'code': $result = @call_user_func_array($this->source, ['page' => $page, 'args' => $this->args]); break;
          case 'text': $result = new text($this->source); break;
          default    : $result =          $this->source;
        }
      }
      if ($result) $this->child_insert($result, 'result');
      event::start('on_block_build_after', null, ['block' => &$this]);
      $this->is_builded = true;
    }
  }

  function render() {
    if ($this->template) {
      return (template::make_new($this->template, [
        'tag_name'   => $this->tag_name,
        'attributes' => $this->render_attributes(),
        'extra_t'    => $this->render_extra_t(),
        'extra_b'    => $this->render_extra_b(),
        'self'       => $this->render_self(),
        'children'   => $this->content_tag_name ? (new markup($this->content_tag_name, $this->content_attributes,
                        $this->render_children($this->children_select(true)) ))->render() :
                        $this->render_children($this->children_select(true))
      ]))->render();
    } else {
      return $this->render_extra_t().
             $this->render_self().
             $this->render_children($this->children_select(true)).
             $this->render_extra_b();
    }
  }

  function render_self() {
    if ($this->title && (bool)$this->title_is_visible !== true) return (new markup($this->title_tag_name, $this->title_attributes + ['aria-hidden' => 'true'], $this->title))->render();
    if ($this->title && (bool)$this->title_is_visible === true) return (new markup($this->title_tag_name, $this->title_attributes + [                       ], $this->title))->render();
  }

  function render_extra_t() {
    if ($this->extra_t !== null) {
      if (is_string($this->extra_t) || is_numeric($this->extra_t)) return (new text($this->extra_t))->render();
      else                                                         return           $this->extra_t  ->render();
    }
  }

  function render_extra_b() {
    if ($this->extra_b !== null) {
      if (is_string($this->extra_b) || is_numeric($this->extra_b)) return (new text($this->extra_b))->render();
      else                                                         return           $this->extra_b  ->render();
    }
  }

}}