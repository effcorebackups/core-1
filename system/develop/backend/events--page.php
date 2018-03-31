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

  #####################
  ### classes: list ###
  #####################

  static function on_show_block_classes_list($page) {
    $thead = [['type', 'name', 'file']];
    $tbody = [];
    foreach (factory::get_classes_map() as $c_class_full_name => $c_class_info) {
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

  #########################
  ### classes: diagrams ###
  #########################

  static function on_show_block_classes_diagrams($page) {
    $return = new markup('x-diagram-uml');
    foreach (factory::get_classes_map() as $c_class_full_name => $c_class_info) {
      if ($c_class_info->type == 'class') {
        $c_file       = new file($c_class_info->file);
        $c_reflection = new \ReflectionClass($c_class_full_name);
        $x_diagram    = new markup('x-class');
        $x_name       = new markup('x-name', ['title' => $c_class_info->file], ' '.$c_class_info->name.' ');
        $x_namespace  = new markup('x-namespace', [], '(from '.$c_class_info->namespace.')');
        $x_name_wr    = new markup('x-name-wrapper', [], [$x_name, $x_namespace]);
        $x_attributes = new markup('x-attributes');
        $x_operations = new markup('x-operations');
        $x_diagram->child_insert($x_name_wr);
        $x_diagram->child_insert($x_attributes, 'attributes');
        $x_diagram->child_insert($x_operations, 'operations');
        $return->child_insert($x_diagram);

      # set abstract mark
        if ($c_reflection->isAbstract()) {
          $x_diagram->attribute_insert('x-abstract', 'true');
        }

      # find properties
        foreach ($c_reflection->getProperties() as $c_attribute) {
          if ($c_attribute->getDeclaringClass()->name === $c_class_full_name) {
            $c_matches = [];
            preg_match('%(?<type>class|trait|interface)\\s+'.
                        '(?<class_name>'.$c_class_info->name.').*?'.
                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                        '(?<name>'.$c_attribute->name.') = '.
                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
            $c_defaults = isset($c_matches['value']) ?
                                $c_matches['value'] : null;
            $c_name = ($c_defaults !== null) ? ' '.$c_attribute->name.' = '.$c_defaults :
                                               ' '.$c_attribute->name;
            if ($c_attribute->isPublic())    $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'public']    + ($c_attribute->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_attribute->name);
            if ($c_attribute->isProtected()) $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'protected'] + ($c_attribute->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_attribute->name);
            if ($c_attribute->isPrivate())   $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'private']   + ($c_attribute->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_attribute->name);
          }
        }

      # find methods
        foreach ($c_reflection->getMethods() as $c_operation) {
          if ($c_operation->getDeclaringClass()->name === $c_class_full_name) {
            $c_matches = [];
            preg_match('%(?<type>class|trait|interface)\\s+'.
                        '(?<class_name>'.$c_class_info->name.').*?'.
                        '(?<last_modifier>public|protected|private|static|final|)\\s'.
                        '(?:function)\\s'.
                        '(?<name>'.$c_operation->name.')\\s*\\('.
                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
            $c_defaults = isset($c_matches['params']) ? preg_replace('#(\\$)([a-z_])#i', '$2',
                                $c_matches['params']) : null;
            $c_name = ($c_defaults !== null) ? ' '.$c_operation->name.' ('.$c_defaults.')' :
                                               ' '.$c_operation->name.' ()';
            if ($c_operation->isPublic())    $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'public']    + ($c_operation->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_operation->name);
            if ($c_operation->isProtected()) $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'protected'] + ($c_operation->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_operation->name);
            if ($c_operation->isPrivate())   $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'private']   + ($c_operation->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_operation->name);
          }
        }
      }
    }

    return new markup('x-block', ['id' => 'classes_diagrams'], [
      new markup('h2', [], 'UML Diagram'),
      $return
    ]);
  }

}}