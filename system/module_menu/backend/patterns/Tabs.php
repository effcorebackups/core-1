<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Tabs extends Node {

    public $id;
    public $template = 'tabs';
    public $template_top_items = 'tab_top_items';
    public $template_sub_items = 'tab_sub_items';
    public $attributes = ['role' => 'tablist'];
    public $origin = 'nosql'; # nosql | dynamic

    function __construct($id = null, $attributes = [], $weight = 0) {
        if ($id) $this->id = $id;
        parent::__construct($attributes, [], $weight);
    }

    function build() {
        if (!$this->is_builded) {
            Event::start('on_tab_build_before', $this->id, ['tab' => &$this]);
            $this->attribute_insert('data-id', $this->id);
            foreach (Tab_item::select_all() as $c_item) {
                if ($c_item->id_tab    === $this->id &&
                    $c_item->id_parent === null) {
                    $this->child_insert($c_item, $c_item->id);
                    $c_item->build(); }}
            Event::start('on_tab_build_after', $this->id, ['tab' => &$this]);
            $this->is_builded = true;
        }
    }

    function render() {
        static::init();
        $this->build();
        return (Template::make_new($this->template, [
            'attributes' => $this->render_attributes(),
            'top_items'  => $this->render_top_items(),
            'sub_items'  => $this->render_sub_items()
        ]))->render();
    }

    function render_top_items() {
        $rendered = '';
        foreach ($this->children_select(true) as $c_child) {
            $c_clone = clone $c_child;
            $c_clone->children = [];
            $rendered.= $c_clone->render();
        }
        return $rendered ? (Template::make_new($this->template_top_items, [
            'children' => $rendered
        ]))->render() : '';
    }

    function render_sub_items() {
        $rendered = '';
        foreach ($this->children_select(true) as $c_item) {
            $c_url = rtrim(Page::get_current()->args_get('base').'/'.$c_item->action_name, '/');
            if (Url::is_active_trail($c_url)) {
                foreach ($c_item->children_select(true) as $c_child) {
                    $rendered.= $c_child->render();
                }
                break;
            }
        }
        return $rendered ? (Template::make_new($this->template_sub_items, [
            'children' => $rendered
        ]))->render() : '';
    }

    function get_first_branch($with_access = true) {
        $result = [];
        static::init();
        $this->build();
        $c_children = $this->children;
        while (true) {
            if (count($c_children)) {
                Core::array_sort_by_number($c_children);
                $c_found = false;
                foreach ($c_children as $c_child) {
                    if ( ($with_access !== true) ||
                         ($with_access === true && Access::check($c_child->access)) ) {
                        $result[] = $c_child;
                        $c_children = $c_child->children;
                        $c_found = true;
                        break 1; }}
                if (!$c_found) return $result;
            } else             return $result;
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('tabs') as $c_module_id => $c_tabs) {
                foreach ($c_tabs as $c_row_id => $c_tab) {
                    if (isset(static::$cache[$c_tab->id])) Console::report_about_duplicate('tabs', $c_tab->id, $c_module_id, static::$cache[$c_tab->id]);
                              static::$cache[$c_tab->id] = $c_tab;
                              static::$cache[$c_tab->id]->origin = 'nosql';
                              static::$cache[$c_tab->id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function select_all() {
        static::init();
        return static::$cache ?? [];
    }

    static function select($id) {
        static::init();
        return static::$cache[$id] ?? null;
    }

    static function insert($id, $attributes = [], $weight = 0, $module_id = null) {
        static::init();
        $new_tab = new static($id, $attributes, $weight);
               static::$cache[$id] = $new_tab;
               static::$cache[$id]->origin = 'dynamic';
               static::$cache[$id]->module_id = $module_id;
        return static::$cache[$id];
    }

    static function delete($id) {
        static::init();
        unset(static::$cache[$id]);
    }

}
