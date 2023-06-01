<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

class breadcrumbs extends markup {

    public $tag_name = 'nav';
    public $attributes = ['aria-label' => 'breadcrumb'];
    public $id;
    public $links = [];
    public $is_remove_last_link = true;

    function build() {
        if (!$this->is_builded) {
            event::start('on_breadcrumbs_build_before', $this->id, ['breadcrumbs' => &$this]);
            $this->children_delete();
            foreach ($this->links as $c_row_id => $c_link) {
                if ($this->is_remove_last_link && $c_link === end($this->links)) break;
                $c_link_markup = new markup('a', ['href' => $c_link->url], new text($c_link->title, [], true, true), $c_link->weight ?? 0);
                if (url::is_active      ($c_link->url)) $c_link_markup->attribute_insert('aria-current',        'true');
                if (url::is_active_trail($c_link->url)) $c_link_markup->attribute_insert('data-selected-trail', 'true');
                $this->child_insert($c_link_markup); }
            event::start('on_breadcrumbs_build_after', $this->id, ['breadcrumbs' => &$this]);
            $this->is_builded = true;
        }
    }

    function link_select_all() {
        return $this->links;
    }

    function link_select($row_id) {
        return $this->links[$row_id];
    }

    function link_insert($row_id, $title, $url, $weight = null) {
        $this->links[$row_id] = new stdClass;
        $this->links[$row_id]->title = $title;
        $this->links[$row_id]->url = $url;
        $this->links[$row_id]->weight = $weight === null ? 1 - count($this->links) : $weight;
    }

    function link_update($row_id, $title = null, $url = null, $weight = null) {
        if ($title  !== null) $this->links[$row_id]->title  = $title;
        if ($url    !== null) $this->links[$row_id]->url    = $url;
        if ($weight !== null) $this->links[$row_id]->weight = $weight;
    }

    function link_delete($row_id) {
        unset($this->links[$row_id]);
    }

    function render() {
        $this->build();
        return parent::render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static protected $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (storage::get('data')->select_array('breadcrumbs') as $c_module_id => $c_breadcrumbs_by_module) {
                foreach ($c_breadcrumbs_by_module as $c_breadcrumbs) {
                    if (isset(static::$cache[$c_breadcrumbs->id])) console::report_about_duplicate('breadcrumbs', $c_breadcrumbs->id, $c_module_id, static::$cache[$c_breadcrumbs->id]);
                              static::$cache[$c_breadcrumbs->id] = $c_breadcrumbs;
                              static::$cache[$c_breadcrumbs->id]->module_id = $c_module_id;
                              static::$cache[$c_breadcrumbs->id]->origin = 'nosql';
                }
            }
        }
    }

    static function select($id) {
        static::init();
        return static::$cache[$id] ?? null;
    }

}
