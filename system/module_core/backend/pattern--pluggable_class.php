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
                  $this->name :
    '\\effcore\\'.$this->name;
  }

  function is_exists_class() {
    return core::structure_is_exist($this->name_get());
  }

  function object_get() {
    $object = core::class_get_new_instance($this->name_get(), $this->args, true);
    foreach ($this->properties as $c_key => $c_value)
                        $object->{$c_key} = $c_value;
    return $object;
  }

  function render() {
    return '';
  }

}}