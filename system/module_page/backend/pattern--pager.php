<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class pager extends markup {

  public $id = 0;
  public $prefix = 'page';
  public $tag_name = 'x-pager';
  public $has_error = false;
  public $min = 1;
  public $max = 1;
  public $cur = null;

  function __construct($attributes = [], $weight = 0) {
    parent::__construct($this->tag_name, $attributes, [], $weight);
  }

  function init() {
    $this->cur = (int)url::current_get()->query_arg_get($this->pager_name_get());
    if ($this->cur > $this->max) {
        $this->cur = $this->max;
        $this->has_error = true;
    }
  }

  function min_get() {return $this->min;}
  function max_get() {return $this->max;}
  function cur_get() {return $this->cur;}

  function pager_name_get() {
    return $this->id ? $this->prefix.$this->id :
                       $this->prefix;
  }

 # pager center part dynamic:
 # ─────────────────────────────────────────────────────────────────────
 #
 #
 #         cur = 3
 #             ◍
 #           ┌┐┼──────┐
 #           └┘┴──────┘
 #           ┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿
 #    min = 1││       │B_min = min + 1 + 8 = 10
 #            │
 #            │A_min = min + 1 = 2
 #
 #
 #             cur = 7
 #                 ◍
 #           ┌┐┌───┼───┐
 #           └┘└───┴───┘
 #           ┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿
 #    min = 1│ │       │B_min = cur + 4 = 11
 #             │
 #             │A_min = cur - 4 = 3
 #
 #
 #                                                      cur = 98
 #                                                          ◍
 #                                                   ┌──────┼┌┐
 #                                                   └──────┴└┘
 #           ┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿
 #                           A_max = max - 8 - 1 = 91│       ││max = 100
 #                                                           │
 #                                       B_max = max - 1 = 99│
 #
 #
 #                                                  cur = 94
 #                                                      ◍
 #                                                  ┌───┼───┐┌┐
 #                                                  └───┴───┘└┘
 #           ┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿┿
 #                              A_max = cur - 4 = 90│       │ │max = 100
 #                                                          │
 #                                      B_max = cur + 4 = 98│
 #
 #
 # ─────────────────────────────────────────────────────────────────────

  function render() {
    $this->init();
    $pager = new markup($this->tag_name);
    $pager_name = $this->pager_name_get();
    $url = url::current_get();
    if ($this->max - $this->min > 0) {
      $url->query_arg_set($pager_name, $this->min);
      $pager->child_insert(new markup('a', ['href' => $url->relative_get()], $this->min));
    }
    if ($this->max - $this->min > 1) {
      $a_min = $this->cur - $this->min < 6 ? $this->min + 1 : $this->cur - 4;
      $b_min = $this->cur - $this->min < 6 ? $this->min + 9 : $this->cur + 4;
      $a_max = $this->max - $this->cur < 6 ? $this->max - 9 : $this->cur - 4;
      $b_max = $this->max - $this->cur < 6 ? $this->max - 1 : $this->cur + 4;
      $a     = $this->cur - $this->min < 6 ? max($a_min, $a_max) : min($a_min, $a_max);
      $b     = $this->cur - $this->min < 6 ? max($b_min, $b_max) : min($b_min, $b_max);
    # generate center links
      if ($a > $this->min + 1) {
        $pager->child_insert(new text('...'));
      }
      for ($i = $a; $i <= $b; $i++) {
        if ($i > $this->min && $i < $this->max) {
          $url->query_arg_set($pager_name, $i);
          $pager->child_insert(new markup('a', ['href' => $url->relative_get()], $i));
        }
      }
      if ($b < $this->max - 1) {
        $pager->child_insert(new text('...'));
      }
    }
    if ($this->max - $this->min > 0) {
      $url->query_arg_set($pager_name, $this->max);
      $pager->child_insert(new markup('a', ['href' => $url->relative_get()], $this->max));
    }
    return $pager->render();
  }

}}