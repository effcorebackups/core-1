<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use effcore\core;
use effcore\file;
use effcore\markup_simple;
use effcore\markup;
use effcore\message;
use effcore\node;
use effcore\text_simple;
use effcore\text;
use effcore\text_multiline;
use effcore\url;
use ReflectionClass;
use stdClass;
use Throwable;

abstract class events_page_structures {

    static function on_redirect($event, $page) {
        $type = $page->args_get('type');
        $view = $page->args_get('view');
        if ($type === null) url::go($page->args_get('base'). '/class'.'/list');
        if ($view === null) url::go($page->args_get('base').'/'.$type.'/list');
    }

    ########################
    ### structures: list ###
    ########################

    static function block_markup__structures_list($page, $args = []) {
        $targets = new markup('x-targets');
        $list = new markup('x-structures-list', ['data-type' => core::sanitize_id($page->args_get('type'))]);
        $groups_by_name = [];
        $u_first_character = null;
        foreach (core::structures_select() as $c_item_full_name => $c_item_info) {
            if ($c_item_info->type === $page->args_get('type')) {
                $c_file = new file($c_item_info->file);
                $c_result = new stdClass;
                $c_result->name       = $c_item_info->name;
                $c_result->namespace  = $c_item_info->namespace;
                $c_result->dirs       = $c_file->dirs_get();
                $c_result->dirs_parts = $c_file->dirs_get_parts();
                $c_result->file       = $c_file->file_get();
                $groups_by_name[strtolower($c_item_info->name)][$c_item_info->namespace ?: '-'] = $c_result;
            }
        }
        ksort($groups_by_name);
        foreach ($groups_by_name as $c_group) {
            foreach ($c_group as $c_item) {
                $c_file_parts = new markup('x-file-path', ['title' => new text('file path')]);
                foreach ($c_item->dirs_parts as $c_part)
                    $c_file_parts->child_insert(new markup('x-directory', [], new text_simple($c_part)), $c_part      );
                    $c_file_parts->child_insert(new markup('x-file',      [], $c_item->file           ), $c_item->file);
                if ($u_first_character !== strtoupper($c_item->name[0])) {
                    $u_first_character  =  strtoupper($c_item->name[0]);
                    $l_first_character  =  strtolower($c_item->name[0]);
                    $targets->child_insert(new markup('a', ['href' => '#character_'.$l_first_character, 'title' => new text('go to section "%%_title"', ['title' => $u_first_character])], $u_first_character));
                    $list->child_insert(new markup('h2', ['id' => 'character_'.$l_first_character, 'data-role' => 'targets', 'title' => new text('Section "%%_title"', ['title' => $u_first_character])], $u_first_character));
                }
                $c_return = new markup('x-item');
                $c_return->child_insert(new markup('x-name',      ['title' => new text('name'     )], new text_simple($c_item->name)),               'name'     );
                $c_return->child_insert(new markup('x-namespace', ['title' => new text('namespace')], str_replace('\\', ' | ', $c_item->namespace)), 'namespace');
                $c_return->child_insert($c_file_parts, 'file');
                $list->child_insert($c_return);
            }
        }
        return new node([], [$targets, $list]);
    }

    ###########################
    ### structures: diagram ###
    ###########################

    static function block_markup__structures_diagram($page, $args = []) {
        $map = core::structures_select();
        $diagram = new markup('x-diagram-uml');

        # build diagram for each class
        foreach ($map as $c_item_full_name => $c_item_info) {
            if ($c_item_info->type === $page->args_get('type')) {
                try {

                    $c_file          = new file($c_item_info->file);
                    $c_reflection    = new ReflectionClass($c_item_full_name);
                    $x_class_wrapper = new markup('x-class-wrapper');
                    $x_class         = new markup('x-class',     ['title' => $c_item_info->file  ]);
                    $x_name          = new markup('x-name',      ['title' => new text('name'     )], new text_simple($c_item_info->name));
                    $x_namespace     = new markup('x-namespace', ['title' => new text('namespace')], $c_item_info->namespace ? '(from '.$c_item_info->namespace.')' : '');
                    $x_name_wrapper  = new markup('x-name-wrapper', [], [$x_name, $x_namespace]);
                    $x_attributes    = new markup('x-attributes');
                    $x_operations    = new markup('x-operations');
                    $x_children      = new markup('x-children', [], [], -100);
                    $x_class        ->child_insert($x_name_wrapper, 'name_wrapper');
                    $x_class        ->child_insert($x_attributes,   'attributes'  );
                    $x_class        ->child_insert($x_operations,   'operations'  );
                    $x_class_wrapper->child_insert($x_class,        'class'       );
                    $x_class_wrapper->child_insert($x_children,     'children'    );
                    $diagram->child_insert($x_class_wrapper, $c_item_full_name);

                    # set abstract mark
                    if (!empty($c_item_info->modifier) &&
                               $c_item_info->modifier === 'abstract') {
                        $x_class->attribute_insert('data-abstract', true);
                    }

                    # find properties
                    foreach ($c_reflection->getProperties() as $c_refl_property) {
                        if ($c_refl_property->getDeclaringClass()->name === $c_item_full_name) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                                        '(?<name>'.$c_refl_property->name.') = '.
                                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
                            $c_defaults = isset($c_matches['value']) ? str_replace(' => ', ' = ',
                                                $c_matches['value']) : null;
                            $c_name = ($c_defaults !== null) ?
                                new text_simple($c_refl_property->name.' = '.$c_defaults) :
                                new text_simple($c_refl_property->name);
                            if ($c_refl_property->isPublic   ()) $x_attributes->child_insert(new markup('x-item', ['data-visibility' => 'public',    'title' => new text('property public'   )] + ($c_refl_property->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_property->name);
                            if ($c_refl_property->isProtected()) $x_attributes->child_insert(new markup('x-item', ['data-visibility' => 'protected', 'title' => new text('property protected')] + ($c_refl_property->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_property->name);
                            if ($c_refl_property->isPrivate  ()) $x_attributes->child_insert(new markup('x-item', ['data-visibility' => 'private',   'title' => new text('property private'  )] + ($c_refl_property->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_property->name);
                        }
                    }

                    # find methods
                    foreach ($c_reflection->getMethods() as $c_refl_method) {
                        if ($c_refl_method->getDeclaringClass()->name === $c_item_full_name) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>public|protected|private|static|final|)\\s'.
                                        '(?:function)\\s'.
                                        '(?<name>'.$c_refl_method->name.')\\s*\\('.
                                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
                            $c_defaults = isset($c_matches['params']) ? str_replace(' => ', ' = ', preg_replace('%(\\$)([a-zA-Z_])%S', '$2',
                                                $c_matches['params'])) : null;
                            $c_name = ($c_defaults !== null) ?
                                new text_simple($c_refl_method->name.' ('.$c_defaults.')') :
                                new text_simple($c_refl_method->name.' ('.            ')');
                            if ($c_refl_method->isPublic   ()) $x_operations->child_insert(new markup('x-item', ['data-visibility' => 'public',    'title' => new text('method public'   )] + ($c_refl_method->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_method->name);
                            if ($c_refl_method->isProtected()) $x_operations->child_insert(new markup('x-item', ['data-visibility' => 'protected', 'title' => new text('method protected')] + ($c_refl_method->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_method->name);
                            if ($c_refl_method->isPrivate  ()) $x_operations->child_insert(new markup('x-item', ['data-visibility' => 'private',   'title' => new text('method private'  )] + ($c_refl_method->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_method->name);
                        }
                    }

                } catch (Throwable $e) {
                    message::insert(new text_multiline([
                        'File: %%_file',
                        'Item: %%_item',
                        'Message: %%_message'], [
                        'file' => $c_item_info->file,
                        'item' => $c_item_full_name,
                        'message' => $e->getMessage()]), 'warning'
                    );
                }
            }
        }

        # move children to it is parent
        $items_to_delete = [];
        foreach ($diagram->children_select() as $c_item_full_name => $c_item_wrapper) {
            $c_item_parent_full_name = !empty($map[$c_item_full_name]->extends) ?
                                              $map[$c_item_full_name]->extends : null;
            if ($c_item_parent_full_name) {
                $c_parent = $diagram->child_select($c_item_parent_full_name);
                if ($c_parent) {
                    $x_parent_children = $c_parent->child_select('children');
                    $x_parent_children->child_insert($c_item_wrapper, $c_item_full_name);
                    $items_to_delete[$c_item_full_name] = $c_item_full_name;
                }
            }
        }

        # delete free copies of moved items
        foreach ($items_to_delete as $c_item) {
            $diagram->child_delete($c_item);
        }

        $export_link = new markup('a', ['href' => $page->args_get('base').'/'.$page->args_get('type').'/export'], $page->args_get('type').'.mdj');
        return new node([], [
            new markup('p', [], new text('Export diagram to file "%%_file" for using with "StarUML" software.', ['file' => $export_link->render()])),
            new markup_simple('input', ['type' => 'checkbox', 'data-type' => 'switcher', 'id' => 'expand', 'checked' => true]),
            new markup('label', [], new text('expand')),
            $diagram
        ]);
    }

    ##########################
    ### export UML diagram ###
    ##########################

    static function export($page, $args = []) {
        # build class diagram
        $map = core::structures_select();
        $result = [];
        foreach ($map as $c_item_full_name => $c_item_info) {
            if ($c_item_info->type === $page->args_get('type')) {
                try {

                    $c_reflection = new ReflectionClass($c_item_full_name);
                    $c_file = new file($c_item_info->file);
                    $c_return = new stdClass;
                    $c_return->_type = 'UMLClass';
                    $c_return->_id = 'CLASS-'.core::hash_get($c_item_full_name);
                    $c_return->name = ucfirst($c_item_info->name);
                    $c_return->visibility = 'public';
                    $c_return->isAbstract            = !empty($c_item_info->modifier) && $c_item_info->modifier === 'abstract';
                    $c_return->isFinalSpecialization = !empty($c_item_info->modifier) && $c_item_info->modifier === 'final';
                    $c_return->attributes = [];
                    $c_return->operations = [];

                    # insert relation to parent class
                    $c_item_parent_full_name = !empty($map[$c_item_full_name]->extends) ?
                                                      $map[$c_item_full_name]->extends : null;
                    if ($c_item_parent_full_name) {
                        $c_relation = new stdClass;
                        $c_relation->_type = 'UMLGeneralization';
                        $c_relation->source = new stdClass;
                        $c_relation->target = new stdClass;
                        $c_relation->source->{'$ref'} = 'CLASS-'.core::hash_get($c_item_full_name       );
                        $c_relation->target->{'$ref'} = 'CLASS-'.core::hash_get($c_item_parent_full_name);
                        $c_return->ownedElements = [$c_relation];
                    }

                    # find properties
                    foreach ($c_reflection->getProperties() as $c_refl_property) {
                        if ($c_refl_property->getDeclaringClass()->name === $c_item_full_name) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                                        '(?<name>'.$c_refl_property->name.') = '.
                                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
                            if ($c_refl_property->isPublic   ()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_refl_property->name, 'defaultValue' => $c_matches['value'] ?? '', 'visibility' => 'public',    'isStatic' => $c_refl_property->isStatic()];
                            if ($c_refl_property->isProtected()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_refl_property->name, 'defaultValue' => $c_matches['value'] ?? '', 'visibility' => 'protected', 'isStatic' => $c_refl_property->isStatic()];
                            if ($c_refl_property->isPrivate  ()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_refl_property->name, 'defaultValue' => $c_matches['value'] ?? '', 'visibility' => 'private',   'isStatic' => $c_refl_property->isStatic()];
                        }
                    }

                    # find methods
                    foreach ($c_reflection->getMethods() as $c_refl_method) {
                        if ($c_refl_method->getDeclaringClass()->name === $c_item_full_name) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>public|protected|private|static|final|)\\s'.
                                        '(?:function)\\s'.
                                        '(?<name>'.$c_refl_method->name.')\\s*\\('.
                                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
                            if ($c_refl_method->isPublic   ()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_refl_method->name, 'visibility' => 'public',    'isStatic' => $c_refl_method->isStatic()];
                            if ($c_refl_method->isProtected()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_refl_method->name, 'visibility' => 'protected', 'isStatic' => $c_refl_method->isStatic()];
                            if ($c_refl_method->isPrivate  ()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_refl_method->name, 'visibility' => 'private',   'isStatic' => $c_refl_method->isStatic()];
                            if (!empty($c_matches['params'])) {
                                foreach (explode(',', $c_matches['params']) as $c_param) {
                                    $c_param_parts = explode('=', $c_param);
                                    $c_name = trim($c_param_parts[0], '$ ');
                                    $c_value = isset($c_param_parts[1]) ? trim($c_param_parts[1]) : '';
                                    $c_operation->parameters[] = (object)[
                                        '_type' => 'UMLParameter',
                                        'name' => $c_name,
                                        'defaultValue' => $c_value,
                                        'direction' => $c_name[0] === '&' ? 'inout' : 'in',
                                    ];
                                }
                            }
                            $c_return->operations[] = $c_operation;
                        }
                    }

                    $result[] = $c_return;

                } catch (Throwable $e) {
                }

            }
        }

        # print result
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$page->args_get('type').'.mdj');
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
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
                                '_id' => 'MAIN-CLASSDIAGRAM',
                                'name' => 'Main',
                                'defaultDiagram' => true,
                                'ownedElements' => $result,
                                'ownedViews' => [
                                    (object)[
                                        '_type' => 'UMLTextView',
                                        '_id' => 'MAIN-NOTE',
                                        '_parent' => (object)[
                                            '$ref' => 'MAIN-CLASSDIAGRAM'
                                        ],
                                        'font' => 'Arial;13;0',
                                        'left' => 24,
                                        'top' => 24,
                                        'width' => 305,
                                        'height' => 25,
                                        'text' => 'note: insert any class from the right sidebar'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        JSON_PRETTY_PRINT);
        exit();
    }

}
