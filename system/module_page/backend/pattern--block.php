<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class block extends markup {

  public $tag_name = 'section';
  public $template = 'block';
# ─────────────────────────────────────────────────────────────────────
  public $title;
  public $title_tag_name = 'h2';
  public $title_attributes = ['data-section-title' => true];
  public $content_tag_name = 'x-section-content';
  public $display;
  public $type; # code | link | text | …
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
      if (!isset($this->display) ||
          (isset($this->display) && $this->display->check === 'page_args' && preg_match($this->display->match,         $page->args_get($this->display->where))) ||
          (isset($this->display) && $this->display->check === 'user'      &&            $this->display->where === 'role' && preg_match($this->display->match.'m', implode(nl, user::get_current()->roles)))) {
        switch ($this->type) {
          case 'copy':
          case 'link': if ($this->type === 'copy') $result = core::deep_clone(storage::get('files')->select($this->source, true));
                       if ($this->type === 'link') $result =                  storage::get('files')->select($this->source, true);
                       foreach ($this->properties     as    $c_key => $c_value)
                         core::arrobj_insert_value($result, $c_key,   $c_value);
                       break;
          case 'code': $result = @call_user_func_array($this->source, ['page' => $page, 'args' => $this->args]); break;
          case 'text': $result = new text($this->source); break;
          default    : $result =          $this->source;
        }
      }
      if ($result) $this->child_insert($result, 'result');
      $this->is_builded = true;
    }
  }

  function render() {
    if ($this->template) {
      return (template::make_new($this->template, [
        'tag_name'   => $this->tag_name,
        'attributes' => $this->render_attributes(),
        'self'       => $this->render_self(),
        'children'   => $this->content_tag_name ? (new markup($this->content_tag_name, ['data-section-content' => true],
                        $this->render_children($this->children_select(true)) ))->render() :
                        $this->render_children($this->children_select(true))
      ]))->render();
    } else {
      return $this->render_self().
             $this->render_children($this->children_select(true));
    }
  }

  function render_self() {
    if ($this->title) {
      return (new markup($this->title_tag_name, $this->title_attributes, [
        $this->title
      ]))->render();
    }
  }

}}