<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\node;
          use \effcore\markup;
          use \effcore\factory;
          use \effcore\table;
          use \effcore\table_body_row;
          use \effcore\table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  static function on_show_block_classes_list($page) {
    $thead = [['type', 'name', 'file']];
    $tbody = [];
    foreach (factory::get_classes_map() as $c_class_name => $c_class_info) {
      $tbody[] = [
        new table_body_row_cell(['class' => ['type' => 'type']], $c_class_info->type == 'interface' ? 'intr.' : $c_class_info->type),
        new table_body_row_cell(['class' => ['name' => 'name']], $c_class_info->namespace.' \ '.$c_class_info->name),
        new table_body_row_cell(['class' => ['file' => 'file']], $c_class_info->file)
      ];
    }
    return new markup('x-block', ['id' => 'classes_list'], [
      new markup('h2', [], 'Classes list'),
      new table(['class' => ['classes-list' => 'classes-list']], $tbody, $thead)
    ]);
  }

  static function on_show_block_classes_diagrams($page) {
    $return = new markup('x-diagram-uml');
    foreach (factory::get_classes_map() as $c_class) {
      $c_reflection = new \ReflectionClass($c_class->namespace.'\\'.$c_class->name);
      $c_diagram    = new markup('x-class', [], new markup('x-class-name', [], ' '.$c_class->name.' '));
      $c_attributes = new markup('x-attributes');
      $c_operations = new markup('x-operations');
      $c_diagram->child_insert($c_attributes, 'attributes');
      $c_diagram->child_insert($c_operations, 'operations');
      $return->child_insert($c_diagram);
      foreach ($c_reflection->getProperties() as $c_attribute) {
        if ($c_attribute->isPublic())    $c_attributes->child_insert(new markup('x-attribute', ['class' => ['public'    => 'public']],    '+ '.$c_attribute->name), $c_attribute->name);
        if ($c_attribute->isProtected()) $c_attributes->child_insert(new markup('x-attribute', ['class' => ['protected' => 'protected']], '# '.$c_attribute->name), $c_attribute->name);
        if ($c_attribute->isPrivate())   $c_attributes->child_insert(new markup('x-attribute', ['class' => ['private'   => 'private']],   '- '.$c_attribute->name), $c_attribute->name);
      }
      foreach ($c_reflection->getMethods() as $c_operation) {
        if ($c_operation->isPublic())    $c_operations->child_insert(new markup('x-operation', ['class' => ['public'    => 'public']],    '+ '.$c_operation->name.'()'), $c_operation->name);
        if ($c_operation->isProtected()) $c_operations->child_insert(new markup('x-operation', ['class' => ['protected' => 'protected']], '# '.$c_operation->name.'()'), $c_operation->name);
        if ($c_operation->isPrivate())   $c_operations->child_insert(new markup('x-operation', ['class' => ['private'   => 'private']],   '- '.$c_operation->name.'()'), $c_operation->name);
      }
    }
    return new node([], [new markup('h2', [], 'UML Diagram'), $return]);
  }

}}