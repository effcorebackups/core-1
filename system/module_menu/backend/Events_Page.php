<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\menu;

use effcore\Access;
use effcore\Block_preset;
use effcore\Entity;
use effcore\Frontend;
use effcore\Markup;
use effcore\Page;
use effcore\Request;
use effcore\Text;
use effcore\Tree_item;
use effcore\Tree;
use effcore\Url;

abstract class Events_Page {

    static function on_breadcrumbs_build_before($event, $breadcrumbs) {
        if (Page::get_current()->id === 'instance_select' ||
            Page::get_current()->id === 'instance_insert' ||
            Page::get_current()->id === 'instance_update' ||
            Page::get_current()->id === 'instance_delete') {
            $entity_name = Page::get_current()->args_get('entity_name');
            $instance_id = Page::get_current()->args_get('instance_id');
            $category_id = Page::get_current()->args_get('category_id');
            if ($entity_name === 'tree_item') {
                if ($category_id) {                                                                    $tree = Tree::select($category_id       );}
                if ($instance_id) {$tree_item = Tree_item::select($instance_id, null); if ($tree_item) $tree = Tree::select($tree_item->id_tree);}
                if (isset($tree)) { # note: $tree is undefined on 'insert instance' page
                    $breadcrumbs->link_insert('category', $tree->title, '/manage/data/menu/tree_item///'.$tree->id);
                }
            }
        }
    }

    static function on_block_presets_dynamic_build($event, $id = null) {
        if ($id === null                                          ) {foreach (Tree::select_all('sql') as $c_item)            Block_preset::insert('block__tree_sql__'.$c_item->id, 'Menus', $c_item->title ?: 'NO TITLE', [ /* all areas */ ], ['title' => 'Menu', 'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\menu\\Events_Page::block_markup__tree_get', 'args' => ['instance_id' => $c_item->id, 'entity_name' => 'tree'], 'has_admin_tree_menu' => true, 'attributes' => ['data-id' => 'block__tree_sql__'.$c_item->id]], 0, 'menu');}
        if ($id !== null && strpos($id, 'block__tree_sql__') === 0) {$c_item__id = substr($id, strlen('block__tree_sql__')); Block_preset::insert('block__tree_sql__'.$c_item__id, 'Menus',                   'NO TITLE', [ /* all areas */ ], ['title' => 'Menu', 'title_is_visible' => false, 'type' => 'code', 'source' => '\\effcore\\modules\\menu\\Events_Page::block_markup__tree_get', 'args' => ['instance_id' => $c_item__id, 'entity_name' => 'tree'], 'has_admin_tree_menu' => true, 'attributes' => ['data-id' => 'block__tree_sql__'.$c_item__id]], 0, 'menu');}
    }

    static function on_block_build_after($event, $block) {
        if (Request::value_get('manage_layout', 0, '_GET') === 'true') {
            if (Access::check((object)['roles' => ['registered' => 'registered']])) {
                if (!empty($block->has_admin_tree_menu)) {
                    $instance_id = $block->args['instance_id'];
                    $entity_name = $block->args['entity_name'];
                    if ($entity_name === 'tree'                                &&
                        Access::check(Entity::get('tree_item')->access_select) &&
                        Access::check(Entity::get('tree_item')->access_update)) {
                        $block->extra_t = new Markup('x-admin-actions', ['data-entity_name' => $entity_name],
                            new Markup('a', ['data-id' => 'update', 'title' => new Text('update'), 'href' => '/manage/data/menu/tree_item///'.$instance_id.'?'.Url::back_part_make()],
                                new Markup('x-action-title', ['data-action-title' => true], 'update')
                            )
                        );
                    }
                }
            }
        }
    }

    static function block_markup__tree_get($page, $args = []) {
        if (!empty($args['instance_id'])) {
            return Tree::select($args['instance_id']);
        }
    }

    static function on_tree_build_after($event, $tree) {
        if (!Frontend::select('tree_all__menu'))
             Frontend::insert('tree_all__menu', null, 'styles', ['path' => 'frontend/tree.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => +400], 'tree_style', 'menu');
        if ($tree->visualization_mode === 'decorated-rearrangeable') {
            if (!Frontend::select('tree_rearrangeable__menu')) {
                 Frontend::insert('tree_rearrangeable__menu', null, 'scripts', ['path'  => 'frontend/tree-rearrangeable.js',   'attributes' => [            'defer' => true            ], 'weight' => +300], 'tree_script', 'menu');
                 Frontend::insert('tree_rearrangeable__menu', null, 'styles',  ['path'  => 'frontend/tree-rearrangeable.cssd', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => +300], 'tree_style',  'menu');
            }
        }
    }

}
