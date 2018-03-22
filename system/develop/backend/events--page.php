<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\file;
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
      $c_file       = new file($c_class->file);
      $c_reflection = new \ReflectionClass($c_class->namespace.'\\'.$c_class->name);
      $c_defs       = $c_reflection->getDefaultProperties();
      $x_diagram    = new markup('x-class');
      $x_name       = new markup('x-name', [], ' '.$c_class->name.' ');
      $x_attributes = new markup('x-attributes');
      $x_operations = new markup('x-operations');
      $x_diagram->child_insert($x_name, 'name');
      $x_diagram->child_insert($x_attributes, 'attributes');
      $x_diagram->child_insert($x_operations, 'operations');
      $return->child_insert($x_diagram);

    # find default value for each property
      foreach ($c_defs as $c_key => $c_value) {
        $matches = [];
        preg_match('%(?<visibility>static|public|)\\s\\$'.
                    '(?<name>'.$c_key.') = '.
                    '(?<value>.+?);%', $c_file->load(), $matches);
        $c_defs[$c_key] = isset($matches['value']) ?
                                $matches['value'] : null;
      }

    # set abstract mark
      if ($c_reflection->isAbstract()) {
        $x_diagram->attribute_insert('x-abstract', 'true');
      }

    # find non static properties
      foreach ($c_reflection->getProperties() as $c_attribute) {
        $c_name = ' '.$c_attribute->name;
        if (array_key_exists($c_attribute->name, $c_defs) && $c_defs[$c_attribute->name] !== null) $c_name.= ' = '.$c_defs[$c_attribute->name];
        if ($c_attribute->isPublic())    $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'public'],    $c_name), $c_attribute->name);
        if ($c_attribute->isProtected()) $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'protected'], $c_name), $c_attribute->name);
        if ($c_attribute->isPrivate())   $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'private'],   $c_name), $c_attribute->name);
      }

    # find static properties
      foreach ($c_reflection->getStaticProperties() as $c_key => $c_value) {
        $c_name = ' '.$c_key;
        if (array_key_exists($c_attribute->name, $c_defs) && $c_defs[$c_attribute->name] !== null) $c_name.= ' = '.$c_defs[$c_attribute->name];
        if ($c_attribute->isPublic())    $x_attributes->child_insert(new markup('x-item', ['x-static' => 'true', 'x-visibility' => 'public'],    $c_name), $c_attribute->name);
        if ($c_attribute->isProtected()) $x_attributes->child_insert(new markup('x-item', ['x-static' => 'true', 'x-visibility' => 'protected'], $c_name), $c_attribute->name);
        if ($c_attribute->isPrivate())   $x_attributes->child_insert(new markup('x-item', ['x-static' => 'true', 'x-visibility' => 'private'],   $c_name), $c_attribute->name);
      }

    # find methods
      foreach ($c_reflection->getMethods() as $c_operation) {
        $c_name = ' '.$c_operation->name.'()';
        if ($c_operation->isPublic())    $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'public']    + ($c_operation->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_operation->name);
        if ($c_operation->isProtected()) $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'protected'] + ($c_operation->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_operation->name);
        if ($c_operation->isPrivate())   $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'private']   + ($c_operation->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_operation->name);
      }
    }
    return new node([], [new markup('h2', [], 'UML Diagram'), $return]);
  }

}}