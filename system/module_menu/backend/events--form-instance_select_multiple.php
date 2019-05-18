<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\field_hidden;
          use \effcore\node;
          use \effcore\page;
          abstract class events_form_instance_select_multiple {

  static function on_init($form, &$items) {
    $entity_name        = page::get_current()->args_get('entity_name'       );
    $instances_group_by = page::get_current()->args_get('instances_group_by');
    if ($entity_name == 'tree_item' && $instances_group_by && !empty($form->_selection)) {
      $form->_selection->query_params['conditions'] = ['field_!f' => 'id_tree', '=', 'value_!v' => $instances_group_by];
      $form->_selection->field_insert_code('extra', '', function($c_row, $c_instance){
        $c_hidden_parent = new field_hidden('parent-'.$c_instance->id, $c_instance->id_parent, ['data-parent' => 'true']);
        $c_hidden_weight = new field_hidden('weight-'.$c_instance->id, $c_instance->weight,    ['data-weight' => 'true']);
        return new node([], [
          'actions'       => $c_row['actions']['value'],
          'hidden_parent' => $c_hidden_parent,
          'hidden_weight' => $c_hidden_weight
        ]);
      });
    }
  }

  static function on_submit($form, $items) {
    $entity_name = page::get_current()->args_get('entity_name');
    if ($entity_name == 'tree_item') {
    }
  }

}}