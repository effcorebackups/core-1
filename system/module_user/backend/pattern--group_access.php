<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_access extends group_switchers {

  public $title = 'Access';
  public $description = 'Access settings are not applicable if no one role is active!';
  public $attributes = [
    'data-type' => 'switchers',
    'role'      => 'group'];
  public $element_attributes = [
    'data-type' => 'switcher',
    'name'      => 'roles[]'
  ];

  function build() {
    if (!$this->is_builded) {
      foreach (access::roles_get() as $value => $title)
        $this->field_insert($title, null, ['value' => $value], $value);
      $this->is_builded = true;
    }
  }

  function roles_get() {
    return core::array_kmap(
      $this->values_get()
    );
  }

  function roles_set($roles) {
    $this->checked = core::array_kmap($roles);
  }

}}