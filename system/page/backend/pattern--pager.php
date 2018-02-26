<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class pager extends \effcore\markup {

  public $id;
  public $tag_name = 'x-pager';
  public $has_error = false;
  public $page_num_min = 1;
  public $page_num_max = 100;
  public $page_num_cur = 50;

  function __construct($attributes = [], $weight = 0) {
    parent::__construct($this->tag_name, $attributes, [], $weight);
  }

  function get_page_num_cur() {
    return $this->page_num_cur;
  }

  function render() { # @todo: make functionality
    $pager = new markup($this->tag_name);
    $pager->child_insert(new markup('a', ['href' => '#'], 1));
    $pager->child_insert(new text('...'));
    $pager->child_insert(new markup('a', ['href' => '#'], 25));
    $pager->child_insert(new text('...'));
    $pager->child_insert(new markup('a', ['href' => '#'], 47));
    $pager->child_insert(new markup('a', ['href' => '#'], 48));
    $pager->child_insert(new markup('a', ['href' => '#'], 49));
    $pager->child_insert(new markup('a', ['href' => '#', 'class' => ['active']], 50));
    $pager->child_insert(new markup('a', ['href' => '#'], 51));
    $pager->child_insert(new markup('a', ['href' => '#'], 52));
    $pager->child_insert(new markup('a', ['href' => '#'], 53));
    $pager->child_insert(new text('...'));
    $pager->child_insert(new markup('a', ['href' => '#'], 75));
    $pager->child_insert(new text('...'));
    $pager->child_insert(new markup('a', ['href' => '#'], 100));
    $pager->child_insert(new markup('div', [], 'UNDER CONSTRUCTION'));
    return $pager->render();
  }

}}