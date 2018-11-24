<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class pluggable_class {

  public $name;
  public $args = [];
  public $properties = [];
  public $weight = 0;

  function name_get() {
    return $this->name[0] == '\\' ?
           $this->name : __NAMESPACE__.'\\'.
           $this->name;
  }

  function class_is_exists() {
    return core::structure_is_exist($this->name_get());
  }

  function object_get() {
    $object = core::class_instance_new_get($this->name_get(), $this->args, true);
    foreach ($this->properties as $c_name => $c_value) {
      $object->{$c_name} = $c_value;
    }
    return $object;
  }

  function render() {
    return '';
  }

}}