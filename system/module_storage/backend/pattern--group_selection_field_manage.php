<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_selection_field_manage extends fieldset {

  public $tag_name = 'x-part-manage';
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
          $button_delete->value_set('button_field_delete_'.$this->entity_field_name);
          $this->child_insert($button_delete, 'button');
          $this->child_insert(new markup('x-title', [], [$entity->title, ': ', $entity_field->title]), 'title');
          $this->child_insert(new markup('x-id',    [], [
            new text_simple($this->entity_name), '.',
            new text_simple($this->entity_field_name)]), 'id');
          $this->is_builded = true;
        }
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function submit(&$group, $form, $npath) {
    $button = $group->child_select('button');
    if ($button->is_clicked()) {
      $fields = $form->validation_cache_get('fields');
      foreach ($fields as $c_id => $c_field) {
        if ($c_field->entity_name       == $group->entity_name &&
            $c_field->entity_field_name == $group->entity_field_name) {
          unset($fields[$c_id]);
          $form->validation_cache_set('fields', $fields);
          message::insert(new text('Field from entity "%%_name" with id = "%%_id" was deleted.', ['name' => $group->entity_name, 'id' => $group->entity_field_name]));
          message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
          return true;
        }
      }
    }
  }

}}