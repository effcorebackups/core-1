<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class widget_selection_decorator_settings extends control implements control_complex {

  public $tag_name = 'x-widget';
  public $title_tag_name = 'label';
  public $title_attributes = ['data-widget-title' => true];
  public $content_tag_name = 'x-widget-content';
  public $content_attributes = ['data-widget-content' => true, 'data-nested-content' => true];
  public $name_complex = 'decorator_settings';
  public $attributes = [
    'data-type' => 'decorator-settings',
    'role'      => 'group'
  ];

  function build() {
    if (!$this->is_builded) {
      $this->child_insert(static::widget_manage_get($this), 'manage');
      $this->is_builded = true;
    }
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function value_get($options = []) { # return: array | serialize(array)
    $result = [];
    $result['view_type'    ] = $this->controls['#view_type'    ]->     value_get();
    $result['template'     ] = $this->controls['#template'     ]->     value_get();
    $result['template_item'] = $this->controls['#template_item']->     value_get();
    $result['mapping'      ] = $this->controls['#mapping'      ]->value_data_get()->mapping ?? [];
    if (!empty($options['return_serialized']))
         return serialize($result);
    else return           $result;
  }

  function value_set($value) {
    $this->value_set_initial($value);
    if (core::data_is_serialized($value)) $value = unserialize($value);
    if ($value === null) $value = [];
    if ($value ===  '' ) $value = [];
    if (is_array($value)) {
      if (!empty($value['view_type'    ])) $this->controls['#view_type'    ]->     value_set($value['view_type']);
      if (!empty($value['template'     ])) $this->controls['#template'     ]->     value_set($value['template']);
      if (!empty($value['template_item'])) $this->controls['#template_item']->     value_set($value['template_item']);
      if (!empty($value['mapping'      ])) $this->controls['#mapping'      ]->value_data_set($value['mapping'] ?? null, 'mapping');
    }
  }

  function name_get_complex() {
    return $this->name_complex;
  }

  function disabled_get() {
    return false;
  }

  ###########################
  ### static declarations ###
  ###########################

  static function widget_manage_get($widget) {
    $result = new node;
  # control for type of view
    $field_select_view_type = new field_select;
    $field_select_view_type->title = 'View type';
    $field_select_view_type->cform = $widget->cform;
    $field_select_view_type->disabled['tree'] = 'tree';
    $field_select_view_type->items_set([
      'not_selected'   => '- select -',
      'table'          => 'Table',
      'table-adaptive' => 'Table (adaptive)',
      'table-dl'       => 'Table (DL)',
      'ul'             => 'Unordered list',
      'dl'             => 'Definition list',
      'template'       => 'Template',
      'tree'           => 'Tree']);
    $field_select_view_type->build();
    $field_select_view_type->name_set($widget->name_get_complex().'__view_type');
    $field_select_view_type->value_set('table');
  # controls for templates
    $template_items = [];
    $templates = template::get_all();
    foreach ($templates as $c_template) {
      if ($c_template->type === 'text') {
        $c_name = preg_replace('%_embedded$%S', '', $c_template->name);
        $template_items[$c_name] =
                        $c_name; }}
    core::array_sort($template_items);
    $field_select_template = new field_select;
    $field_select_template->title = 'Template';
    $field_select_template->cform = $widget->cform;
    $field_select_template->items_set(['not_selected' => '- select -'] + $template_items);
    $field_select_template->build();
    $field_select_template->name_set($widget->name_get_complex().'__template');
    $field_select_template->value_set('markup_html');
    $field_select_template_item = new field_select;
    $field_select_template_item->title = 'Template (item)';
    $field_select_template_item->cform = $widget->cform;
    $field_select_template_item->items_set(['not_selected' => '- select -'] + $template_items);
    $field_select_template_item->build();
    $field_select_template_item->name_set($widget->name_get_complex().'__template_item');
    $field_select_template_item->value_set('content');
  # control for mapping
    $field_textarea_data_mapping = new field_textarea_data;
    $field_textarea_data_mapping->title = 'Mapping';
    $field_textarea_data_mapping->cform = $widget->cform;
    $field_textarea_data_mapping->data_validator_id = 'mapping';
    $field_textarea_data_mapping->element_attributes['rows'] = 17;
    $field_textarea_data_mapping->build();
    $field_textarea_data_mapping->name_set($widget->name_get_complex().'__mapping');
    $field_textarea_data_mapping->required_set(false);
    $field_textarea_data_mapping->maxlength_set(0xffff);
    $field_textarea_data_mapping->value_data_set([
      'id'              => 'id',
      'id_tree'         => 'id_tree',
      'id_parent'       => 'id_parent',
      'description'     => 'description',
      'title'           => 'title',
      'url'             => 'url',
      'path'            => 'path',
      'text'            => 'text',
      'attributes'      => 'this_attributes',
      'link_attributes' => 'link_attributes',
      'created'         => 'created',
      'updated'         => 'updated',
      'is_embedded'     => 'is_embedded',
      'weight'          => 'weight',
      'children'        => 'items',
      'items'           => 'items',
    ], 'mapping');
  # relate new controls with the widget
    $widget->controls['#view_type'    ] = $field_select_view_type;
    $widget->controls['#template'     ] = $field_select_template;
    $widget->controls['#template_item'] = $field_select_template_item;
    $widget->controls['#mapping'      ] = $field_textarea_data_mapping;
    $result->child_insert($field_select_view_type, 'field_select_view_type');
    $result->child_insert($field_select_template, 'field_select_template');
    $result->child_insert($field_select_template_item, 'field_select_template_item');
    $result->child_insert($field_textarea_data_mapping, 'field_textarea_data_mapping');
    return $result;
  }

}}