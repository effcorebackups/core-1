<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_parts extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'area'];
  public $id_area;

  function __construct($id_area, $attributes = [], $weight = 0) {
    $this->id_area = $id_area;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $widgets_group_manage = new markup('x-widgets-group', [
        'data-type'              => 'manage',
        'data-has-rearrangeable' => 'true']);
    # widgets for manage each item
      $c_widget_manage_weight = 0;
      foreach ($this->cform->validation_cache_get('parts_'.$this->id_area) ?? [] as $c_preset) {
        if ($c_preset instanceof page_part_preset_link) {
          $c_widget_manage = new widget_area_part_manage($this->id_area, $c_preset->id, [], $c_widget_manage_weight);
          $c_widget_manage->build();
          $c_widget_manage_weight -= 5;
          $widgets_group_manage->child_insert($c_widget_manage, $c_preset->id);
          $c_widget_manage->on_click_delete_handler = function ($group, $form, $npath) {
            $this->on_click_delete($group, $form, $npath);
          };
        }
      }
    # widget for insert new item
      $widget_insert = new widget_area_part_insert($this->id_area);
      $widget_insert->build();
      $widget_insert->on_click_insert_handler = function ($group, $form, $npath, $value) {
        $this->on_click_insert($group, $form, $npath, $value);
      };
    # insert all widgets
      $this->child_insert($widgets_group_manage, 'manage');
      $this->child_insert($widget_insert, 'insert');
      $this->is_builded = true;
    }
  }

  function items_get() {
    $result = $this->cform->validation_cache_get('parts_'.$this->id_area) ?: null;
    if ($result == null) return;
    if ($result != null) {
      $buffer = [];
      $sorted = [];
      foreach ($result as $c_row_id => $c_object) {
        $c_field_name_suffix = $c_object->id.'_'.$this->id_area;
        $c_weight = (int)(field::request_value_get('weight_'.$c_field_name_suffix));
        $c_buffer_new_item = new \stdClass;
        $c_buffer_new_item->row_id = $c_row_id;
        $c_buffer_new_item->weight = $c_weight;
        $c_buffer_new_item->object = $c_object;
        $buffer[] = $c_buffer_new_item;}
      core::array_sort_by_weight($buffer);
      foreach ($buffer as $c_sorted)
        $sorted[$c_sorted->row_id] =
                $c_sorted->object;
      return $sorted;
    }
  }

  function items_set($items) {
    $this->cform->validation_cache_is_persistent = true;
    $this->cform->validation_cache_set('parts_'.$this->id_area, $items);
    if ($this->is_builded) {
        $this->is_builded = false;
        $this->build();
    }
  }

  function items_set_once($items) {
    if ($this->cform->validation_cache_get('parts_'.$this->id_area) === null) {
      $this->items_set($items);
    }
  }

  function on_click_insert($group, $form, $npath, $value) {
    $preset = page_part_preset::select($value);
    $parts = $form->validation_cache_get('parts_'.$this->id_area);
    $parts[$preset->id] = new page_part_preset_link($preset->id);
    $this->items_set($parts);
    message::insert(new text_multiline([
      'Part of the page with ID = "%%_id_page_part" was inserted to the area with ID = "%%_id_area".',
      'Click the button "%%_name" to save your changes!'], [
      'id_page_part' => $preset->id,
      'id_area'      => $group->id_area,
      'name'         => translation::get('update')]));
    return true;
  }

  function on_click_delete($group, $form, $npath) {
    $parts = $form->validation_cache_get('parts_'.$this->id_area);
    unset($parts[$group->id_preset]);
    $this->items_set($parts);
    message::insert(new text_multiline([
      'Part of the page with ID = "%%_id_page_part" was deleted from the area with ID = "%%_id_area".',
      'Click the button "%%_name" to save your changes!'], [
      'id_page_part' => $group->id_preset,
      'id_area'      => $group->id_area,
      'name'         => translation::get('update')]));
    return true;
  }

}}