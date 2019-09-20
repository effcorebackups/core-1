<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class group_selection_field_insert extends fieldset {

  public $tag_name = 'x-selection_field-insert';
  public $content_tag_name = null;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $options = ['not_selected' => '- no -'];
      $entities = entity::get_all();
      foreach ($entities as $c_entity) {
        if (!empty($c_entity->managing_is_on)) {
          foreach ($c_entity->fields_get_title() as $c_name => $c_title) {
            if (!isset($options[$c_entity->name])) {
                       $options[$c_entity->name] = new \stdClass;
                       $options[$c_entity->name]->title = $c_entity->title;}
            $options[$c_entity->name]->values[ $c_entity->name.'.'.$c_name ] = new text_multiline([
              'title' => $c_title, 'id' => '('.$c_entity->name.'.'.$c_name.')'], [], ' '
            );
          }
        }
      }
      $select_field = new field_select('Insert field');
      $select_field->values = $options;
      $select_field->build();
      $select_field->name_set('insert_field');
      $select_field->required_set(false);
      $button_insert = new button('', ['data-style' => 'narrow-insert']);
      $button_insert->build();
      $button_insert->value_set('button_field_insert');
      $this->child_insert($select_field,  'select');
      $this->child_insert($button_insert, 'button_field_insert');
      $this->is_builded = true;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function validate($group, $form, $npath) {
  }

  static function submit(&$group, $form, $npath) {
    $select        = $group->child_select('select');
    $button_insert = $group->child_select('button_field_insert');
    if ($button_insert->is_clicked() && $select->value_get()) {
      $fields = $form->validation_cache_get('fields');
      $entity_info = explode('.', $select->value_get());
      $fields[$select->value_get()] = (object)[
        'type'              => 'field',
        'entity_name'       => $entity_info[0],
        'entity_field_name' => $entity_info[1]];
      $form->validation_cache_is_persistent = true;
      $form->validation_cache_set('fields', $fields);
      $entity = entity::get(             $entity_info[0]);
      $entity_field = $entity->field_get($entity_info[1]);
      message::insert(new text('Field "%%_name" was inserted.', ['name' => translation::get($entity->title).': '.translation::get($entity_field->title)]));
      message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
      return true;
    }
  }

}}