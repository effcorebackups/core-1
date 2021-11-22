<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_blocks extends widget_items {

  public $title;
  public $title__not_selected__widget_insert = '- select -';
  public $item_title = 'Block';
  public $content_tag_name = null;
  public $attributes = ['data-type' => 'items-blocks'];
  public $name_complex = 'widget_blocks';
  public $id_area;

  function __construct($id_area, $attributes = [], $weight = 0) {
    $this->id_area = $id_area;
    parent::__construct($attributes, $weight);
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_get($widget, $item, $c_row_id) {
    $result = parent::widget_manage_get($widget, $item, $c_row_id);
    $widget_settings = new widget_block_settings($widget, $item, $c_row_id);
    $widget_settings->build();
  # info markup
    $presets = block_preset::select_all($widget->id_area);
    $title_markup = isset($presets[$item->id]) ?
                         [$presets[$item->id]->managing_group, ': ',
                          $presets[$item->id]->managing_title] : 'ORPHANED BLOCK';
    $info_markup = new markup('x-info',  [], [
        'title' => new markup('x-title', [], $title_markup),
        'id'    => new markup('x-id',    [], new text_simple($item->id) ) ]);
  # relate new controls with the widget
    $result->child_insert($info_markup, 'info');
    $result->child_insert($widget_settings, 'settings');
    return $result;
  }

  static function widget_insert_get($widget) {
    $result = new markup('x-widget', ['data-type' => 'insert']);
  # control with type of new item
    $presets = block_preset::select_all($widget->id_area);
    core::array_sort_by_text_property($presets, 'managing_group');
    $options = ['not_selected' => $widget->title__not_selected__widget_insert];
    foreach ($presets as $c_preset) {
      $c_group_id = core::sanitize_id($c_preset->managing_group);
      if (!isset($options[$c_group_id])) {
                 $options[$c_group_id] = new \stdClass;
                 $options[$c_group_id]->title = $c_preset->managing_group; }
      $options[$c_group_id]->values[$c_preset->id] = (new text_multiline([
        'title' => $c_preset->managing_title, 'id' => '('.$c_preset->id.')'], [], ' '
      ))->render();
    }
    foreach ($options as $c_group) {
      if ($c_group instanceof \stdClass) {
        core::array_sort_text($c_group->values);
      }
    }
    $select = new field_select('Insert block');
    $select->values = $options;
    $select->cform = $widget->cform;
    $select->build();
    $select->name_set($widget->name_get_complex().'__insert');
    $select->required_set(false);
  # button for insertion of the new item
    $button = new button(null, ['data-style' => 'insert', 'title' => new text('insert')]);
    $button->break_on_validate = true;
    $button->build();
    $button->value_set($widget->name_get_complex().'__insert');
    $button->_type = 'insert';
  # relate new controls with the widget
    $widget->controls['#insert'] = $select;
    $widget->controls['~insert'] = $button;
    $result->child_insert($select, 'select');
    $result->child_insert($button, 'button');
    return $result;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  static function on_button_click_insert($widget, $form, $npath, $button) {
    $widget->controls['#insert']->required_set(true);
    $result_validation = field_select::on_validate($widget->controls['#insert'], $form, $npath);
    $widget->controls['#insert']->required_set(false);
    if ($result_validation) {
      $min_weight = 0;
      $items = $widget->items_get();
      foreach ($items as $c_row_id => $c_item)
        $min_weight = min($min_weight, $c_item->weight);
      $new_item = new block_preset_link($widget->controls['#insert']->value_get());
      $new_item->weight = count($items) ? $min_weight - 5 : 0;
      $items[] = $new_item;
      $widget->items_set($items);
      $widget->controls['#insert']->value_set('');
      message::insert(new text_multiline([
        'Item of type "%%_type" with ID = "%%_id" was inserted.',
        'Do not forget to save the changes!'], [
        'type' => (new text($widget->item_title))->render(),
        'id'   => $new_item->id ]));
      return true;
    }
  }

}}