<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_access extends control implements complex_control {

  public $tag_name = 'x-group';
  public $title = 'Access';
  public $title_attributes = ['data-group-title' => true];
  public $description = 'Access settings are not applicable if no one role is active!';
  public $name_complex = 'access';
  public $attributes = [
    'data-type' => 'access',
    'role'      => 'group'
  ];

  function build() {
    if (!$this->is_builded) {
      $group_roles = new group_switchers;
      $group_roles->build();
      foreach (access::roles_get() as $value => $title)
        $group_roles->field_insert($title, null, ['name' => $this->name_complex.'__roles[]', 'value' => $value], $value);
      $this->child_insert($group_roles, 'group_roles');
      $this->is_builded = true;
    }
  }

  function name_get_complex() {
    return $this->name_complex;
  }

  function value_get_complex() {
    $roles = $this->child_select('group_roles')->values_get();
    return $roles ? (object)[
      'roles' => core::array_kmap($roles)
    ] : null;
  }

  function value_set_complex($value) {
    $this->value_set_initial($value);
    $this->child_select('group_roles')->values_set(
      core::array_kmap($value->roles ?? [])
    );
  }

  function disabled_get() {
    return false;
  }

}}