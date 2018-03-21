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
    $return = new node();
    foreach (factory::get_classes_map() as $c_class) {
      $c_reflection = new \ReflectionClass($c_class->namespace.'\\'.$c_class->name);
      $c_diagram    = new markup('x-class');
      $c_properties = new markup('x-properties');
      $c_methods    = new markup('x-methods');
      $c_diagram->child_insert($c_properties, 'properties');
      $c_diagram->child_insert($c_methods, 'methods');
      $return->child_insert($c_diagram);
      foreach ($c_reflection->getProperties() as $c_prop) {
        if ($c_prop->isPublic())    $c_properties->child_insert(new markup('x-property', ['class' => ['public'    => 'public']],    '+'.$c_prop->getName()), $c_prop->getName());
        if ($c_prop->isProtected()) $c_properties->child_insert(new markup('x-property', ['class' => ['protected' => 'protected']], '#'.$c_prop->getName()), $c_prop->getName());
        if ($c_prop->isPrivate())   $c_properties->child_insert(new markup('x-property', ['class' => ['private'   => 'private']],   '-'.$c_prop->getName()), $c_prop->getName());
      }
    }
    return $return;
  }

}}