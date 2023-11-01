<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Frontend {

    public $display;
    public $favicons = [];
    public $styles   = [];
    public $scripts  = [];

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache;

    static function cache_cleaning() {
        static::$cache = null;
    }

    static function init() {
        if (static::$cache === null) {
            foreach (Storage::get('data')->select_array('frontend') as $c_module_id => $c_frontends) {
                foreach ($c_frontends as $c_row_id => $c_frontend) {
                    if (isset(static::$cache[$c_row_id])) Console::report_about_duplicate('frontend', $c_row_id, $c_module_id, static::$cache[$c_row_id]);
                              static::$cache[$c_row_id] = $c_frontend;
                              static::$cache[$c_row_id]->module_id = $c_module_id;
                }
            }
        }
    }

    static function select_all() {
        static::init();
        return static::$cache;
    }

    static function select($row_id) {
        static::init();
        return static::$cache[$row_id] ?? null;
    }

    static function insert($row_id, $display = null, $type = 'styles', $element = [], $element_row_id = null, $mudule_id = null) {
        static::init();
        if (!isset(static::$cache[$row_id]))
                   static::$cache[$row_id] = new static;
        static::$cache[$row_id]->display   = $display;
        static::$cache[$row_id]->module_id = $mudule_id;
        if ($element_row_id) static::$cache[$row_id]->{$type}[$element_row_id] = (object)$element;
        else                 static::$cache[$row_id]->{$type}[               ] = (object)$element;
    }

    static function markup_get($used_blocks_dpath, $used_blocks_cssid) {
        $result          = new stdClass;
        $result->icons   = new Node;
        $result->styles  = new Node;
        $result->scripts = new Node;
        foreach (static::select_all() as $c_row_id => $c_items) {
            if ($c_items->display === null                                               ||
                static::is_visible_by_url        ($c_items->display)                     ||
                static::is_visible_by_block_dpath($c_items->display, $used_blocks_dpath) ||
                static::is_visible_by_block_cssid($c_items->display, $used_blocks_cssid) ) {

                # collect favicons
                foreach ($c_items->favicons as $c_item) {
                    $c_url        = new Url($c_item->path[0] === '/' ? $c_item->path : '/'.Module::get($c_items->module_id)->path.$c_item->path);
                    $c_attributes = $c_item->attributes ?? [];
                    $c_weight     = $c_item->weight     ?? 0;
                    $result->icons->child_insert(new Markup_simple('link', [
                        'href' => Token::apply($c_url->relative_get())
                    ] + $c_attributes, $c_weight));
                }

                # collect styles
                foreach ($c_items->styles as $c_item) {
                    $c_url        = new Url($c_item->path[0] === '/' ? $c_item->path : '/'.Module::get($c_items->module_id)->path.$c_item->path);
                    $c_attributes = $c_item->attributes ?? [];
                    $c_weight     = $c_item->weight     ?? 0;
                    $result->styles->child_insert(new Markup_simple('link', [
                        'href' => Token::apply($c_url->relative_get())
                    ] + $c_attributes, $c_weight));
                }

                # collect scripts
                foreach ($c_items->scripts as $c_item) {
                    $c_url        = new Url($c_item->path[0] === '/' ? $c_item->path : '/'.Module::get($c_items->module_id)->path.$c_item->path);
                    $c_attributes = $c_item->attributes ?? [];
                    $c_weight     = $c_item->weight     ?? 0;
                    $result->scripts->child_insert(new Markup('script', [
                        'src' => Token::apply($c_url->relative_get())
                    ] + $c_attributes, [], $c_weight));
                }

            }
        }
        return $result;
    }

    static function is_visible_by_block_dpath($display, $used_blocks_dpath) {
        return ($display->check === 'block' &&
                $display->where === 'dpath' && preg_match(
                $display->match.'m', implode(NL, $used_blocks_dpath)));
    }

    static function is_visible_by_block_cssid($display, $used_blocks_cssid) {
        return ($display->check === 'block' &&
                $display->where === 'cssid' && preg_match(
                $display->match.'m', implode(NL, $used_blocks_cssid)));
    }

    static function is_visible_by_url($display) {
        return ($display->check === 'url' && $display->where === 'protocol'  && preg_match($display->match, Url::get_current()->protocol       )) ||
               ($display->check === 'url' && $display->where === 'domain'    && preg_match($display->match, Url::get_current()->domain         )) ||
               ($display->check === 'url' && $display->where === 'path'      && preg_match($display->match, Url::get_current()->path           )) ||
               ($display->check === 'url' && $display->where === 'query'     && preg_match($display->match, Url::get_current()->query          )) ||
               ($display->check === 'url' && $display->where === 'anchor'    && preg_match($display->match, Url::get_current()->anchor         )) ||
               ($display->check === 'url' && $display->where === 'file_type' && preg_match($display->match, Url::get_current()->file_type_get())) ||
               ($display->check === 'url' && $display->where === 'relative'  && preg_match($display->match, Url::get_current()-> relative_get())) ||
               ($display->check === 'url' && $display->where === 'absolute'  && preg_match($display->match, Url::get_current()-> absolute_get()));
    }

}
