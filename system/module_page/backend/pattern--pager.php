<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class pager extends markup {

  public $tag_name = 'x-pager';
  public $has_error = false;

  public $min = 1;
  public $max = 1;
  public $cur = null;
  public $name = 'page';
  public $id = 0;

  function __construct($min = 1, $max = 1, $name = 'page', $id = 0,  $attributes = [], $weight = 0) {
    $this->min  = $min;
    $this->max  = $max;
    $this->name = $name;
    $this->id   = $id;
    parent::__construct(null, $attributes, [], $weight);
  }

  function init() {
    $this->cur = url::current_get()->query_arg_select($this->pager_name_get()) ?: $this->min;
    if ((string)(int)$this->cur !== (string)$this->cur) {$this->cur = $this->min; $this->has_error = true;}
    if ($this->max < $this->min)                        {$this->max = $this->min; $this->has_error = true;}
    if ($this->cur < $this->min)                        {$this->cur = $this->min; $this->has_error = true;}
    if ($this->cur > $this->max)                        {$this->cur = $this->max; $this->has_error = true;}
  }

  function pager_name_get($optimized = true) {
    if (!$optimized)
         return             $this->name.$this->id;
    else return $this->id ? $this->name.$this->id :
                            $this->name;
  }

 # the dynamic of the pager center part:
 # ─────────────────────────────────────────────────────────────────────
 #
 #
 #         cur = 3
 #             ◍
 #           ┌┐┼──────┐
 #           └┘┴──────┘
 #           ┝┿┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷
 #    min = 1││       │B_min = min + 1 + 8 = 10
 #            │
 #            │A_min = min + 1 = 2
 #
 #
 #             cur = 7
 #                 ◍
 #           ┌┐┌───┼───┐
 #           └┘└───┴───┘
 #           ┝┷┿┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷
 #    min = 1│ │       │B_min = cur + 4 = 11
 #             │
 #             │A_min = cur - 4 = 3
 #
 #
 #                                                      cur = 98
 #                                                          ◍
 #                                                   ┌──────┼┌┐
 #                                                   └──────┴└┘
 #           ┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┿┥
 #                           A_max = max - 8 - 1 = 91│       ││max = 100
 #                                                           │
 #                                       B_max = max - 1 = 99│
 #
 #
 #                                                  cur = 94
 #                                                      ◍
 #                                                  ┌───┼───┐┌┐
 #                                                  └───┴───┘└┘
 #           ┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┷┿┷┷┷┷┷┷┷┿┷┥
 #                              A_max = cur - 4 = 90│       │ │max = 100
 #                                                          │
 #                                      B_max = cur + 4 = 98│
 #
 #
 # ─────────────────────────────────────────────────────────────────────

  function render() {
    $this->init();
    $pager = new markup($this->tag_name);
    $pager_name               = $this->pager_name_get();
    $pager_name_not_optimized = $this->pager_name_get(false);
    $url = clone url::current_get();
    $url->query_arg_delete($pager_name);
    $url->query_arg_delete($pager_name_not_optimized);

  # ─────────────────────────────────────────────────────────────────────
  # min part
  # ─────────────────────────────────────────────────────────────────────
    if ($this->max - $this->min > 0) {
      if ($this->cur == $this->min)
           $pager->child_insert(new markup('a', ['href' => $url->tiny_get(), 'class' => ['active' => 'active']], $this->min));
      else $pager->child_insert(new markup('a', ['href' => $url->tiny_get()], $this->min));
    }

  # ─────────────────────────────────────────────────────────────────────
  # central part
  # ─────────────────────────────────────────────────────────────────────
    if ($this->max - $this->min > 1) {
      $a_min = $this->cur - $this->min < 6 ? $this->min + 1 : $this->cur - 4;
      $b_min = $this->cur - $this->min < 6 ? $this->min + 9 : $this->cur + 4;
      $a_max = $this->max - $this->cur < 6 ? $this->max - 9 : $this->cur - 4;
      $b_max = $this->max - $this->cur < 6 ? $this->max - 1 : $this->cur + 4;
      $a     = $this->cur - $this->min < 6 ? max($a_min, $a_max) : min($a_min, $a_max);
      $b     = $this->cur - $this->min < 6 ? max($b_min, $b_max) : min($b_min, $b_max);

    # l-shoulder part
      if ($a > $this->min + 10) {
        $pager->child_insert(new text('…'));
        for ($j = 1; $j < 4; $j++) {
          $c_i = $this->min + (int)(($a - $this->min) / 4 * $j);
          $url->query_arg_insert($pager_name, $c_i);
          $pager->child_insert(new markup('a', ['href' => $url->tiny_get()], $c_i));
        }
      }

    # central links part
      if ($a > $this->min + 1) {
        $pager->child_insert(new text('…'));
      }
      for ($i = $a; $i <= $b; $i++) {
        if ($i > $this->min && $i < $this->max) {
          $url->query_arg_insert($pager_name, $i);
          if ($this->cur == $i)
               $pager->child_insert(new markup('a', ['href' => $url->tiny_get(), 'class' => ['active' => 'active']], $i));
          else $pager->child_insert(new markup('a', ['href' => $url->tiny_get()], $i));
        }
      }
      if ($b < $this->max - 1) {
        $pager->child_insert(new text('…'));
      }

    # r-shoulder part
      if ($b < $this->max - 10) {
        for ($j = 1; $j < 4; $j++) {
          $c_i = $b + (int)(($this->max - $b) / 4 * $j);
          $url->query_arg_insert($pager_name, $c_i);
          $pager->child_insert(new markup('a', ['href' => $url->tiny_get()], $c_i));
        }
        $pager->child_insert(new text('…'));
      }
    }

  # ─────────────────────────────────────────────────────────────────────
  # max part
  # ─────────────────────────────────────────────────────────────────────
    if ($this->max - $this->min > 0) {
      $url->query_arg_insert($pager_name, $this->max);
      if ($this->cur == $this->max)
           $pager->child_insert(new markup('a', ['href' => $url->tiny_get(), 'class' => ['active' => 'active']], $this->max));
      else $pager->child_insert(new markup('a', ['href' => $url->tiny_get()], $this->max));
    }

    return $pager->render();
  }

}}