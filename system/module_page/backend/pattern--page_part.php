<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class page_part extends node {

  public $region = 'content';
  public $display;
  public $type; # code | link | text | …
  public $source;
  public $properties = [];

  function markup_get($page = null) {
    if (!isset($this->display) ||
        (isset($this->display) && $this->display->check == 'page_args' && preg_match($this->display->match, $page->args_get($this->display->where))) ||
        (isset($this->display) && $this->display->check == 'user' && $this->display->where == 'role' && preg_match($this->display->match.'m', implode(nl, user::current_get()->roles)))) {
      switch ($this->type) {
        case 'link': $result = storage::get('files')->select($this->source, true);
                     $result->_page = $page;
                     foreach ($this->properties as $c_key => $c_value) {
                       core::arrobj_value_insert($result, $c_key, $c_value);
                     }
                     return $result;
        case 'code': return call_user_func_array($this->source, ['page' => $page, 'args' => $this->properties]);
        case 'text': return new text($this->source);
        default    : return $this->source;
      }
    }
  }

}}