<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class control_actions_list extends container {

  public $title = 'actions';
  public $tag_name = 'x-actions-control';
  public $actions = [];
  public $active = [];

  function __construct($title = null, $attributes = [], $weight = 0) {
    if ($title) $this->title = $title;
    parent::__construct($this->tag_name, null, null, $attributes, [], $weight);
  }

  function action_add($url, $title, $is_enabled = true) {
    if ($is_enabled) {
      $this->actions[$url] = $title;
    }
  }

  function build() {
    $list = new markup('x-action-list');
    $this->child_insert($list, 'action_list');
    foreach ($this->actions as $c_url => $c_action) {
      $list->child_insert(
        new markup('x-action', isset($this->active[$c_action]) ? ['class' => ['active']] : [],
          new markup('a', ['href' => $c_url], $c_action)
        )
      );
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

}}