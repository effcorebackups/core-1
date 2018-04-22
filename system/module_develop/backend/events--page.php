<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\file;
          use \effcore\node;
          use \effcore\text;
          use \effcore\text_raw;
          use \effcore\markup;
          use \effcore\markup_simple;
          use \effcore\translation;
          use \effcore\factory;
          use \effcore\table;
          use \effcore\table_body_row;
          use \effcore\table_body_row_cell;
          abstract class events_page extends \effcore\events_page {

  ########################
  ### structures: list ###
  ########################

  static function on_show_block_structures_list($page) {
    $list = new markup('x-class-list');
    $groups_by_name = [];
    foreach (factory::get_classes_map() as $c_class_full_name => $c_class_info) {
      if ($c_class_info->type == $page->args_get('type')) {
        $c_file = new file($c_class_info->file);
        $c_result_info = new \stdClass();
        $c_result_info->name       = $c_class_info->name;
        $c_result_info->namespace  = $c_class_info->namespace;
        $c_result_info->dirs       = $c_file->get_dirs();
        $c_result_info->dirs_parts = $c_file->get_dirs_parts();
        $c_result_info->file       = $c_file->get_file();
        $groups_by_name[$c_class_info->name][$c_class_info->namespace ?: '-'] = $c_result_info;
      }
    }
    ksort($groups_by_name);
    foreach ($groups_by_name as $c_group) {
      ksort($c_group);
      foreach ($c_group as $c_item) {
        $c_file_parts = new markup('x-file-path');
        foreach ($c_item->dirs_parts as $c_part)
          $c_file_parts->child_insert(new markup('x-directory', [], new text_raw($c_part)), $c_part);
          $c_file_parts->child_insert(new markup('x-file', [], $c_item->file), $c_item->file);
        $c_return = new markup('x-item');
        $c_return->child_insert(new markup('x-name', [], new text_raw($c_item->name)), 'name');
        $c_return->child_insert(new markup('x-namespace', [], str_replace('\\', ' | ', $c_item->namespace)), 'namespace');
        $c_return->child_insert($c_file_parts, 'file');
        $list->child_insert($c_return);
      }
    }
    return new markup('x-block', ['class' => ['structures-list']], $list);
  }

  ###########################
  ### structures: diagram ###
  ###########################

  static function on_show_block_structures_diagram($page) {
    $classes_map = factory::get_classes_map();
    $diagram = new markup('x-diagram-uml');

  # build diagram for each class
    foreach ($classes_map as $c_class_full_name => $c_class_info) {
      if ($c_class_info->type == 'class') {
        $c_file       = new file($c_class_info->file);
        $c_reflection = new \ReflectionClass($c_class_full_name);
        $x_class_wr   = new markup('x-class-wrapper');
        $x_class      = new markup('x-class');
        $x_name       = new markup('x-name', ['title' => $c_class_info->file], new text_raw($c_class_info->name));
        $x_namespace  = new markup('x-namespace', [], '(from '.$c_class_info->namespace.')');
        $x_name_wr    = new markup('x-name-wrapper', [], [$x_name, $x_namespace]);
        $x_attributes = new markup('x-attributes');
        $x_operations = new markup('x-operations');
        $x_children   = new markup('x-children', [], [], 100);
        $x_class->child_insert($x_name_wr, 'name_wrapper');
        $x_class->child_insert($x_attributes, 'attributes');
        $x_class->child_insert($x_operations, 'operations');
        $x_class_wr->child_insert($x_class, 'class');
        $x_class_wr->child_insert($x_children, 'children');
        $diagram->child_insert($x_class_wr, $c_class_full_name);

      # set abstract mark
        if (!empty($c_class_info->modifier) &&
                   $c_class_info->modifier == 'abstract') {
          $x_class->attribute_insert('x-abstract', 'true');
        }

      # find properties
        foreach ($c_reflection->getProperties() as $c_info) {
          if ($c_info->getDeclaringClass()->name === $c_class_full_name) {
            $c_matches = [];
            preg_match('%(?<type>class|trait|interface)\\s+'.
                        '(?<class_name>'.$c_class_info->name.').*?'.
                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                        '(?<name>'.$c_info->name.') = '.
                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
            $c_defaults = isset($c_matches['value']) ?
                                $c_matches['value'] : null;
            $c_name = ($c_defaults !== null) ? new text_raw($c_info->name.' = '.$c_defaults) :
                                               new text_raw($c_info->name);
            if ($c_info->isPublic())    $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'public']    + ($c_info->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_info->name);
            if ($c_info->isProtected()) $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'protected'] + ($c_info->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_info->name);
            if ($c_info->isPrivate())   $x_attributes->child_insert(new markup('x-item', ['x-visibility' => 'private']   + ($c_info->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_info->name);
          }
        }

      # find methods
        foreach ($c_reflection->getMethods() as $c_info) {
          if ($c_info->getDeclaringClass()->name === $c_class_full_name) {
            $c_matches = [];
            preg_match('%(?<type>class|trait|interface)\\s+'.
                        '(?<class_name>'.$c_class_info->name.').*?'.
                        '(?<last_modifier>public|protected|private|static|final|)\\s'.
                        '(?:function)\\s'.
                        '(?<name>'.$c_info->name.')\\s*\\('.
                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
            $c_defaults = isset($c_matches['params']) ? preg_replace('#(\\$)([a-z_])#i', '$2',
                                $c_matches['params']) : null;
            $c_name = ($c_defaults !== null) ? new text_raw($c_info->name.' ('.$c_defaults.')') :
                                               new text_raw($c_info->name.' ()');
            if ($c_info->isPublic())    $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'public']    + ($c_info->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_info->name);
            if ($c_info->isProtected()) $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'protected'] + ($c_info->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_info->name);
            if ($c_info->isPrivate())   $x_operations->child_insert(new markup('x-item', ['x-visibility' => 'private']   + ($c_info->isStatic() ? ['x-static' => 'true'] : []), $c_name), $c_info->name);
          }
        }
      }
    }

  # move children to it's parent
    $items_to_delete = [];
    foreach ($diagram->child_select_all() as $c_class_full_name => $c_class_wr) {
      $c_class_parent_full_name = !empty($classes_map[$c_class_full_name]->extends) ?
                                         $classes_map[$c_class_full_name]->extends : null;
      if ($c_class_parent_full_name) {
        $c_parent = $diagram->child_select($c_class_parent_full_name);
        if ($c_parent) {
          $x_parent_children = $c_parent->child_select('children');
          $x_parent_children->child_insert($c_class_wr, $c_class_full_name);
          $items_to_delete[$c_class_full_name] = $c_class_full_name;
        }
      }
    }
  # delete free copies of moved items
    foreach ($items_to_delete as $c_item) {
      $diagram->child_delete($c_item);
    }

    $export_link = new markup('a', ['href' => '/develop/structures/class/diagram_export'], 'classes.mdj');
    return new markup('x-block', ['class' => ['structures-diagram']], [
      new markup('h2', [], 'UML Diagram'),
      new markup('x-export-link', [], translation::get('Export diagram to %%_file for using with StarUML software.', ['file' => $export_link->render()])),
      new markup_simple('input', ['type' => 'checkbox', 'id' => 'expand', 'checked' => 'checked']),
      new markup('label', [], new text('expand')),
      $diagram
    ]);
  }

  ##########################
  ### export UML diagram ###
  ##########################

  static function on_export_diagram($page) {
    if ($page->args_get('type') != 'class') {
      factory::send_header_and_exit('not_found');
    }
  # build class diagram
    $classes_map = factory::get_classes_map();
    $items = [];
    foreach ($classes_map as $c_class_full_name => $c_class_info) {
      if ($c_class_info->type == 'class') {
        $c_reflection = new \ReflectionClass($c_class_full_name);
        $c_file = new file($c_class_info->file);
        $c_return = new \stdClass();
        $c_return->_type = 'UMLClass';
        $c_return->_id = 'C'.md5($c_class_full_name);
        $c_return->name = $c_class_info->name;
        $c_return->visibility = 'public';
        $c_return->isAbstract = !empty($c_class_info->modifier) && $c_class_info->modifier == 'abstract';
        $c_return->isFinalSpecialization = !empty($c_class_info->modifier) && $c_class_info->modifier == 'final';
        $c_return->attributes = [];
        $c_return->operations = [];

      # add relation to parent class
        $c_class_parent_full_name = !empty($classes_map[$c_class_full_name]->extends) ?
                                           $classes_map[$c_class_full_name]->extends : null;
        if ($c_class_parent_full_name) {
          $c_relation = new \stdClass();
          $c_relation->_type = 'UMLGeneralization';
          $c_relation->source = new \stdClass();
          $c_relation->target = new \stdClass();
          $c_relation->source->{'$ref'} = 'C'.md5($c_class_full_name);
          $c_relation->target->{'$ref'} = 'C'.md5($c_class_parent_full_name);
          $c_return->ownedElements = [$c_relation];
        }

      # find properties
        foreach ($c_reflection->getProperties() as $c_info) {
          if ($c_info->getDeclaringClass()->name === $c_class_full_name) {
            $c_matches = [];
            preg_match('%(?<type>class|trait|interface)\\s+'.
                        '(?<class_name>'.$c_class_info->name.').*?'.
                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                        '(?<name>'.$c_info->name.') = '.
                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
            if ($c_info->isPublic())    $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_info->name, 'defaultValue' => isset($c_matches['value']) ? $c_matches['value'] : '', 'visibility' => 'public',    'isStatic' => $c_info->isStatic()];
            if ($c_info->isProtected()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_info->name, 'defaultValue' => isset($c_matches['value']) ? $c_matches['value'] : '', 'visibility' => 'protected', 'isStatic' => $c_info->isStatic()];
            if ($c_info->isPrivate())   $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_info->name, 'defaultValue' => isset($c_matches['value']) ? $c_matches['value'] : '', 'visibility' => 'private',   'isStatic' => $c_info->isStatic()];
          }
        }

      # find methods
        foreach ($c_reflection->getMethods() as $c_info) {
          if ($c_info->getDeclaringClass()->name === $c_class_full_name) {
            $c_matches = [];
            preg_match('%(?<type>class|trait|interface)\\s+'.
                        '(?<class_name>'.$c_class_info->name.').*?'.
                        '(?<last_modifier>public|protected|private|static|final|)\\s'.
                        '(?:function)\\s'.
                        '(?<name>'.$c_info->name.')\\s*\\('.
                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
            if ($c_info->isPublic())    $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_info->name, 'visibility' => 'public',    'isStatic' => $c_info->isStatic()];
            if ($c_info->isProtected()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_info->name, 'visibility' => 'protected', 'isStatic' => $c_info->isStatic()];
            if ($c_info->isPrivate())   $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_info->name, 'visibility' => 'private',   'isStatic' => $c_info->isStatic()];
            if (!empty($c_matches['params'])) {
              foreach (explode(',', $c_matches['params']) as $c_param) {
                $c_param_parts = explode('=', $c_param);
                $c_name = trim($c_param_parts[0], '$ ');
                $c_value = isset($c_param_parts[1]) ? trim($c_param_parts[1]) : '';
                $c_operation->parameters[] = (object)[
                  '_type' => 'UMLParameter',
                  'name' => $c_name,
                  'defaultValue' => $c_value,
                  'direction' => $c_name[0] == '&' ? 'inout' : 'in',
                ];
              }
            }
            $c_return->operations[] = $c_operation;
          }
        }

        $items[] = $c_return;
      }
    }

  # print result
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=classes.mdj');
    header('Pragma: no-cache');
    header('Expires: 0');
    print json_encode(
      (object)[
        '_type' => 'Project',
        'name' => 'ProjectNew',
        'ownedElements' => [
          (object)[
            '_type' => 'UMLModel',
            'name' => 'Model',
            'ownedElements' => [
              (object)[
                '_type' => 'UMLClassDiagram',
                'name' => 'Main',
                'ownedElements' => $items,
              ]
            ]
          ]
        ]
      ],
    JSON_PRETTY_PRINT);
    exit();
  }

}}