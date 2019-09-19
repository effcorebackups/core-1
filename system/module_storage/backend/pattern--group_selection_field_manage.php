<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_selection_field_manage extends fieldset {

  public $tag_name = 'x-selection_field-manage';
  public $content_tag_name = null;
  public $entity_name;
  public $entity_field_name;

 function build() {
    if (!$this->is_builded) {
      parent::build();
      $entity = entity::get($this->entity_name);
      if ($entity) {
        $entity_field = $entity->field_get($this->entity_field_name);
        if ($entity_field) {
          $button_delete = new button('', ['data-style' => 'narrow-delete']);
          $button_delete->build();
          $button_delete->value_set('button_delete_field_'.$this->entity_field_name);
          $this->child_insert($button_delete, 'button');
          $this->child_insert(new markup('x-title', [], $entity->title ?? 'LOST PART'            ), 'title');
          $this->child_insert(new markup('x-id',    [], new text_simple($this->entity_field_name)), 'id'   );
          $this->is_builded = true;
        }
      }
    }
  }

}}