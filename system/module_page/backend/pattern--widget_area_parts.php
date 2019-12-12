<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_area_parts extends container {

  public $tag_name = 'x-widget';
  public $attributes = ['data-type' => 'area-manage'];
  public $id_area;
  public $presets;

  function __construct($id_area, $presets, $attributes = [], $weight = 0) {
    $this->id_area = $id_area;
    $this->presets = $presets;
    parent::__construct(null, null, null, $attributes, [], $weight);
  }

  function build() {
    if (!$this->is_builded) {
      $c_weight = 0;
      $widgets_manage_group = new markup('x-widgets-group', ['data-has-rearrangeable' => 'true']);
      foreach ($this->presets as $c_id_preset) {
        $c_widget_manage = new widget_area_part_manage($this->id_area, $c_id_preset, [], $c_weight);
        $c_widget_manage->build();
        $c_widget_manage->on_click_delete_handler = function ($group, $form, $npath) {$this->on_click_delete($group, $form, $npath);};
        $widgets_manage_group->child_insert($c_widget_manage, $c_id_preset);
        $c_weight -= 5;}
      $widget_insert = new widget_area_part_insert($this->id_area);
      $widget_insert->on_click_insert_handler = function ($group, $form, $npath, $value) {$this->on_click_insert($group, $form, $npath, $value);};
      $widget_insert->build();
      $this->child_insert($widgets_manage_group, 'widgets_manage_group');
      $this->child_insert($widget_insert, 'widget_insert');
      $this->is_builded = true;
    }
  }

  function on_click_insert($group, $form, $npath, $value) {
    $preset = page_part_preset::select($value);
    $parts = $form->validation_cache_get('parts');
    $parts[$group->id_area][$preset->id] = new page_part_preset_link($preset->id);
    $form->validation_cache_is_persistent = true;
    $form->validation_cache_set('parts', $parts);
    message::insert(new text('Part of the page with id = "%%_id_page_part" was inserted to the area with id = "%%_id_area".', ['id_page_part' => $preset->id, 'id_area' => $group->id_area]));
    message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
    return true;
  }

  function on_click_delete($group, $form, $npath) {
    $parts = $form->validation_cache_get('parts');
    unset($parts[$group->id_area][$group->id_preset]);
    if   ($parts[$group->id_area] == [])
    unset($parts[$group->id_area]);
    $form->validation_cache_is_persistent = true;
    $form->validation_cache_set('parts', $parts);
    message::insert(new text('Part of the page with id = "%%_id_page_part" was deleted from the area with id = "%%_id_area".', ['id_page_part' => $group->id_preset, 'id_area' => $group->id_area]));
    message::insert(new text('Click the button "%%_name" to save your changes!', ['name' => translation::get('update')]), 'warning');
    return true;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function on_submit(&$group, $form, $npath) {
  }

}}