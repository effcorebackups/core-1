<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class control_actions_list extends container {

  public $title = 'actions';
  public $tag_name = 'x-actions';
  public $title_tag_name = 'x-actions-title';
  public $actions = [];
  public $active = [];

  function __construct($title = null, $attributes = [], $weight = 0) {
    parent::__construct(null, $title, null, $attributes, [], $weight);
  }

  function action_add($action_name, $title) {
    $this->actions[$action_name] = $title;
  }

  function build() {
    $list = new markup('x-action-list');
    $this->child_insert($list, 'action_list');
    foreach ($this->actions as $c_action_name => $c_title) {
      $c_href = $c_action_name[0] == '/' ? $c_action_name : page::current_get()->args_get('base').'/'.($c_action_name);
      $c_link = new markup('a', ['href' => $c_href], token::replace(translation::get($c_title)));
      $list->child_insert(new markup('x-action', [], $c_link));
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

}}