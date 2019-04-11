<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_relation extends field_select {

  public $attributes = ['data-type' => 'relation'];
  public $element_attributes = [
    'name'     => 'relation',
    'required' => true
  ];
# ─────────────────────────────────────────────────────────────────────
  public $entity_name;
  public $entity_field_id_name;
  public $entity_field_title_name;
  public $query_params = [];

  function build() {
    parent::build();
    $this->option_insert('- no -', 'not_selected');
    $entity = entity::get($this->entity_name);
    $instances = $entity->instances_select($this->query_params);
    foreach ($instances as $c_instance) {
      $this->option_insert(
        $c_instance->{$this->entity_field_title_name},
        $c_instance->{$this->entity_field_id_name   }
      );
    }
  }

}}