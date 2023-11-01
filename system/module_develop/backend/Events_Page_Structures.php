<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\develop;

use effcore\Core;
use effcore\File;
use effcore\Markup_simple;
use effcore\Markup;
use effcore\Message;
use effcore\Node;
use effcore\Security;
use effcore\Text_multiline;
use effcore\Text_simple;
use effcore\Text;
use effcore\Url;
use ReflectionClass;
use stdClass;
use Throwable;

abstract class Events_Page_Structures {

    static function on_redirect($event, $page) {
        $type = $page->args_get('type');
        $view = $page->args_get('view');
        if ($type === null) Url::go($page->args_get('base'). '/class'.'/list');
        if ($view === null) Url::go($page->args_get('base').'/'.$type.'/list');
    }

    ########################
    ### structures: list ###
    ########################

    static function block_markup__structures_list($page, $args = []) {
        $targets = new Markup('x-targets');
        $list = new Markup('x-structures-list', ['data-type' => Security::sanitize_id($page->args_get('type'))]);
        $groups_by_name = [];
        $u_first_character = null;
        foreach (Core::structures_select() as $c_item_full_name => $c_item_info) {
            if ($c_item_info->type === $page->args_get('type')) {
                $c_file = new File($c_item_info->file);
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
                $c_file_parts = new Markup('x-file-path', ['title' => new Text('file path')]);
                foreach ($c_item->dirs_parts as $c_part)
                    $c_file_parts->child_insert(new Markup('x-directory', [], new Text_simple($c_part)), $c_part      );
                    $c_file_parts->child_insert(new Markup('x-file',      [], $c_item->file           ), $c_item->file);
                if ($u_first_character !== strtoupper($c_item->name[0])) {
                    $u_first_character  =  strtoupper($c_item->name[0]);
                    $l_first_character  =  strtolower($c_item->name[0]);
                    $targets->child_insert(new Markup('a', ['href' => '#character_'.$l_first_character, 'title' => new Text('go to section "%%_title"', ['title' => $u_first_character])], $u_first_character));
                    $list->child_insert(new Markup('h2', ['id' => 'character_'.$l_first_character, 'data-role' => 'targets', 'title' => new Text('Section "%%_title"', ['title' => $u_first_character])], $u_first_character));
                }
                $c_return = new Markup('x-item');
                $c_return->child_insert(new Markup('x-name',      ['title' => new Text('name'     )], new Text_simple($c_item->name)),               'name'     );
                $c_return->child_insert(new Markup('x-namespace', ['title' => new Text('namespace')], str_replace('\\', ' | ', $c_item->namespace)), 'namespace');
                $c_return->child_insert($c_file_parts, 'file');
                $list->child_insert($c_return);
            }
        }
        return new Node([], [$targets, $list]);
    }

    ###########################
    ### structures: diagram ###
    ###########################

    static function format_defaults($value) {
        $result = preg_replace('%\\$([a-zA-Z0-9_]{1,})%S', '$1', $value); # replace "$name" to "name"
        $result = preg_replace('%\\n\\s{0,}%S',          ' ',  $result);  # replace line-breaks
        $result = preg_replace('%\\s{0,}(=>)\\s{0,}%S', ' = ', $result);  # replace "=>"  to " = "
        $result = preg_replace('%=\\s{2,}%S',            '= ', $result);  # replace "=  " to  "= "
        $result = preg_replace('%\\s{2,}=%S',            ' =', $result);  # replace "  =" to " ="
        $result = preg_replace('%^\\[\\s{1,}%S',         '[',  $result);  # replace "[ " to "["
        $result = preg_replace('%\\s{1,}\\]$%S',         ']',  $result);  # replace " ]" to "]"
        $result = preg_replace('%,\\]$%S',               ']',  $result);  # replace ",]" to "]"
        return trim($result);
    }

    static function block_markup__structures_diagram($page, $args = []) {
        $map = Core::structures_select();
        $diagram = new Markup('x-diagram-uml');

        # build diagram for each class
        foreach ($map as $c_item_full_name => $c_item_info) {
            if ($c_item_info->type === $page->args_get('type')) {
                try {

                    $c_file          = new File($c_item_info->file);
                    $c_reflection    = new ReflectionClass($c_item_full_name);
                    $x_class_wrapper = new Markup('x-class-wrapper');
                    $x_class         = new Markup('x-class',     ['title' => $c_item_info->file  ]);
                    $x_namespace     = new Markup('x-namespace', ['title' => new Text('namespace')], $c_item_info->namespace ? '&lt;'.$c_item_info->namespace.'&gt;' : '');
                    $x_name          = new Markup('x-name',      ['title' => new Text('name'     )], new Text_simple($c_item_info->name));
                    $x_name_wrapper  = new Markup('x-name-wrapper', [], [$x_namespace, $x_name]);
                    $x_attributes    = new Markup('x-attributes');
                    $x_operations    = new Markup('x-operations');
                    $x_children      = new Markup('x-children', [], [], -100);
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
                        if (strtolower($c_refl_property->getDeclaringClass()->name) === strtolower($c_item_full_name)) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                                        '(?<name>'.$c_refl_property->name.') = '.
                                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
                            $c_defaults = array_key_exists('value', $c_matches) ? static::format_defaults
                                               ($c_matches['value']) : null;
                            $c_name = ($c_defaults !== null) ?
                                new Text_simple($c_refl_property->name.' = '.$c_defaults) :
                                new Text_simple($c_refl_property->name);
                            if ($c_refl_property->isPublic   ()) $x_attributes->child_insert(new Markup('x-item', ['data-visibility' => 'public',    'title' => new Text('property public'   )] + ($c_refl_property->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_property->name);
                            if ($c_refl_property->isProtected()) $x_attributes->child_insert(new Markup('x-item', ['data-visibility' => 'protected', 'title' => new Text('property protected')] + ($c_refl_property->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_property->name);
                            if ($c_refl_property->isPrivate  ()) $x_attributes->child_insert(new Markup('x-item', ['data-visibility' => 'private',   'title' => new Text('property private'  )] + ($c_refl_property->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_property->name);
                        }
                    }

                    # find methods
                    foreach ($c_reflection->getMethods() as $c_refl_method) {
                        if (strtolower($c_refl_method->getDeclaringClass()->name) === strtolower($c_item_full_name)) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>final|abstract|public|protected|private|static|)\\s'.
                                        '(?:function)\\s'.
                                        '(?<name>'.$c_refl_method->name.')\\s*\\('.
                                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
                            $c_defaults = array_key_exists('params', $c_matches) ? static::format_defaults
                                               ($c_matches['params']) : null;
                            $c_name = ($c_defaults !== null) ?
                                new Text_simple($c_refl_method->name.' ('.$c_defaults.')') :
                                new Text_simple($c_refl_method->name.' ('.            ')');
                            if ($c_refl_method->isPublic   ()) $x_operations->child_insert(new Markup('x-item', ['data-visibility' => 'public',    'title' => new Text('method public'   )] + ($c_refl_method->isFinal() ? ['data-final' => true] : []) + ($c_refl_method->isAbstract() ? ['data-abstract' => true] : []) + ($c_refl_method->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_method->name);
                            if ($c_refl_method->isProtected()) $x_operations->child_insert(new Markup('x-item', ['data-visibility' => 'protected', 'title' => new Text('method protected')] + ($c_refl_method->isFinal() ? ['data-final' => true] : []) + ($c_refl_method->isAbstract() ? ['data-abstract' => true] : []) + ($c_refl_method->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_method->name);
                            if ($c_refl_method->isPrivate  ()) $x_operations->child_insert(new Markup('x-item', ['data-visibility' => 'private',   'title' => new Text('method private'  )] + ($c_refl_method->isFinal() ? ['data-final' => true] : []) + ($c_refl_method->isAbstract() ? ['data-abstract' => true] : []) + ($c_refl_method->isStatic() ? ['data-static' => true] : []), $c_name), $c_refl_method->name);
                        }
                    }

                } catch (Throwable $e) {
                    Message::insert(new Text_multiline([
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
            $c_item_parent_full_name =
                    !empty($map[$c_item_full_name]->extends) ?
                strtolower($map[$c_item_full_name]->extends) : null;
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

        $export_link = new Markup('a', ['href' => $page->args_get('base').'/'.$page->args_get('type').'/export'], $page->args_get('type').'.mdj');
        return new Node([], [
            new Markup('p', [], new Text('Export diagram to file "%%_file" for using with "StarUML" software.', ['file' => $export_link->render()])),
            new Markup_simple('input', ['type' => 'checkbox', 'data-type' => 'switcher', 'id' => 'expand', 'checked' => true]),
            new Markup('label', [], new Text('expand')),
            $diagram
        ]);
    }

    ##########################
    ### export UML diagram ###
    ##########################

    static function export($page, $args = []) {
        # build class diagram
        $map = Core::structures_select();
        $result = [];
        foreach ($map as $c_item_full_name => $c_item_info) {
            if ($c_item_info->type === $page->args_get('type')) {
                try {

                    $c_reflection = new ReflectionClass($c_item_full_name);
                    $c_file = new File($c_item_info->file);
                    $c_return = new stdClass;
                    $c_return->_type = 'UMLClass';
                    $c_return->_id = 'CLASS-'.Security::hash_get($c_item_full_name);
                    $c_return->name = $c_item_info->name;
                    $c_return->stereotype = $c_item_info->namespace;
                    $c_return->isAbstract            = !empty($c_item_info->modifier) && $c_item_info->modifier === 'abstract';
                    $c_return->isFinalSpecialization = !empty($c_item_info->modifier) && $c_item_info->modifier === 'final';
                    $c_return->attributes = [];
                    $c_return->operations = [];

                    # insert relation to parent class
                    $c_item_parent_full_name =
                            !empty($map[$c_item_full_name]->extends) ?
                        strtolower($map[$c_item_full_name]->extends) : null;
                    if ($c_item_parent_full_name) {
                        $c_relation = new stdClass;
                        $c_relation->_type = 'UMLGeneralization';
                        $c_relation->source = new stdClass;
                        $c_relation->target = new stdClass;
                        $c_relation->source->{'$ref'} = 'CLASS-'.Security::hash_get($c_item_full_name       );
                        $c_relation->target->{'$ref'} = 'CLASS-'.Security::hash_get($c_item_parent_full_name);
                        $c_return->ownedElements = [$c_relation];
                    }

                    # find properties
                    foreach ($c_reflection->getProperties() as $c_refl_property) {
                        if (strtolower($c_refl_property->getDeclaringClass()->name) === strtolower($c_item_full_name)) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>public|protected|private|static)\\s+\\$'.
                                        '(?<name>'.$c_refl_property->name.') = '.
                                        '(?<value>.+?);%s', $c_file->load(), $c_matches);
                            if ($c_refl_property->isPublic   ()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_refl_property->name, 'defaultValue' => array_key_exists('value', $c_matches) ? static::format_defaults($c_matches['value']) : '', 'visibility' => 'public',    'isStatic' => $c_refl_property->isStatic()];
                            if ($c_refl_property->isProtected()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_refl_property->name, 'defaultValue' => array_key_exists('value', $c_matches) ? static::format_defaults($c_matches['value']) : '', 'visibility' => 'protected', 'isStatic' => $c_refl_property->isStatic()];
                            if ($c_refl_property->isPrivate  ()) $c_return->attributes[] = (object)['_type' => 'UMLAttribute', 'name' => $c_refl_property->name, 'defaultValue' => array_key_exists('value', $c_matches) ? static::format_defaults($c_matches['value']) : '', 'visibility' => 'private',   'isStatic' => $c_refl_property->isStatic()];
                        }
                    }

                    # find methods
                    foreach ($c_reflection->getMethods() as $c_refl_method) {
                        if (strtolower($c_refl_method->getDeclaringClass()->name) === strtolower($c_item_full_name)) {
                            $c_matches = [];
                            preg_match('%(?<type>class|trait|interface)\\s+'.
                                        '(?<class_name>'.$c_item_info->name.').*?'.
                                        '(?<last_modifier>final|abstract|public|protected|private|static|)\\s'.
                                        '(?:function)\\s'.
                                        '(?<name>'.$c_refl_method->name.')\\s*\\('.
                                        '(?<params>.*?|)\\)%s', $c_file->load(), $c_matches);
                            if ($c_refl_method->isPublic   ()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_refl_method->name, 'visibility' => 'public',    'isFinal' => $c_refl_method->isFinal(), 'isAbstract' => $c_refl_method->isAbstract(), 'isStatic' => $c_refl_method->isStatic()];
                            if ($c_refl_method->isProtected()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_refl_method->name, 'visibility' => 'protected', 'isFinal' => $c_refl_method->isFinal(), 'isAbstract' => $c_refl_method->isAbstract(), 'isStatic' => $c_refl_method->isStatic()];
                            if ($c_refl_method->isPrivate  ()) $c_operation = (object)['_type' => 'UMLOperation', 'name' => $c_refl_method->name, 'visibility' => 'private',   'isFinal' => $c_refl_method->isFinal(), 'isAbstract' => $c_refl_method->isAbstract(), 'isStatic' => $c_refl_method->isStatic()];
                            if (!empty($c_matches['params'])) {
                                foreach (explode(',', $c_matches['params']) as $c_param) {
                                    $c_param_parts = explode('=', $c_param);
                                    $c_name = trim($c_param_parts[0], '$ ');
                                    $c_value = isset($c_param_parts[1]) ? static::format_defaults($c_param_parts[1]) : '';
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
        header('content-type: application/octet-stream');
        header('content-disposition: attachment; filename='.$page->args_get('type').'.mdj');
        header('cache-control: private, no-cache, no-store, must-revalidate');
        header('expires: 0');
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
