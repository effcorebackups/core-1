<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_access extends group_switchers {

  public $title = 'Access';
  public $description = 'Access settings not applicable if no one role is active!';
  public $attributes = ['data-type' => 'switchers', 'role' => 'group'];
  public $element_attributes = [
    'data-type' => 'switcher',
    'name'      => 'roles[]'
  ];

  function build() {
    foreach (access::get_roles() as $value => $title) {
      $this->field_insert($title, null, ['value' => $value]);
    }
  }

}}