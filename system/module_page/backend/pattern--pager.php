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
  public $max = 10;
  public $cur = 1;

  function __construct($attributes = [], $weight = 0) {
    parent::__construct($this->tag_name, $attributes, [], $weight);
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

  function render() {
    $pager = new markup($this->tag_name);
    $pager_name = $this->pager_name_get();
    $url = url::current_get();
    if ($this->min == 1) {
      $url->query_arg_set($pager_name, $this->min);
      $pager->child_insert(new markup('a', ['href' => $url->relative_get()], $this->min));
    }
    if ($this->max >= 3) {
      $start_pos = $this->cur - 8 > $this->min ?
                   $this->cur - 8 : $this->min + 1;
      for ($i = $start_pos;
           $i < $start_pos + 16; $i++) {
        if ($i > $this->min && $i < $this->max) {
          $url->query_arg_set($pager_name, $i);
          $pager->child_insert(new markup('a', ['href' => $url->relative_get()], $i));
        }
      }
    }
    if ($this->max >= 2) {
      $url->query_arg_set($pager_name, $this->max);
      $pager->child_insert(new markup('a', ['href' => $url->relative_get()], $this->max));
    }
    return $pager->render();
  }

}}