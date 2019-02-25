<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class actions_list extends markup {

  public $title = 'actions';
  public $tag_name = 'x-actions';
  public $title_tag_name = 'x-actions-title';
  public $template = 'actions_list';
  public $list_state = 'hidden'; # hidden | opened
  public $actions = [];
  public $active = [];

  function __construct($title = null, $attributes = [], $weight = 0) {
    parent::__construct(null, $attributes, [], $weight);
    $this->title = $title;
  }

  function action_add($action_name, $title) {
    $this->actions[$action_name] = $title;
  }

  function build() {
    $this->attribute_insert('data-list_state', $this->list_state);
    $list = new markup('x-action-list');
    $this->child_insert($list, 'action_list');
    foreach ($this->actions as $c_name => $c_title) {
      $c_href = $c_name[0] == '/' ? $c_name : page::current_get()->args_get('base').'/'.($c_name);
      $c_link = new markup('a', ['href' => $c_href], token::replace(translation::get($c_title)));
      $list->child_insert(new markup('x-action', ['data-name' => $c_name],
        $c_link
      ));
    }
  }

  function render() {
    $this->build();
    return (template::make_new($this->template, [
      'tag_name'   => $this->tag_name,
      'attributes' => $this->render_attributes(),
      'self'       => $this->render_self(),
      'children'   => $this->render_children($this->children_select())
    ]))->render();
  }

  function render_self() {
    return (new markup($this->title_tag_name, [], [
      new text($this->title)
    ]))->render();
  }

}}