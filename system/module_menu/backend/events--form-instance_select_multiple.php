<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use \effcore\page;
          abstract class events_form_instance_select_multiple {

  static function on_init($form, &$items) {
    $entity_name        = page::get_current()->args_get('entity_name'       );
    $instances_group_by = page::get_current()->args_get('instances_group_by');
    if ($entity_name == 'tree_item' && $instances_group_by && !empty($form->_selection)) {
      $form->_selection->query_params['conditions'] = ['field_!f' => 'id_tree', '=', 'value_!v' => $instances_group_by];
    }
  }

}}