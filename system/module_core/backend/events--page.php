<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\block;
use effcore\page;
use effcore\storage;
use effcore\tab_item;
use effcore\tree_item;

abstract class events_page {

    static function on_breadcrumbs_build_before($event, $breadcrumbs) {

        # ─────────────────────────────────────────────────────────────────────
        # find all active menu items
        # ─────────────────────────────────────────────────────────────────────

        $branches = [];
        foreach (tree_item::select_all_by_id_tree($breadcrumbs->id) as $c_item) {
            if ($c_item->is_active      () ||
                $c_item->is_active_trail()) {
                $branches[][$c_item->id] = $c_item;
            }
        }
        # find all parents (resolve all branches)
        foreach ($branches as $c_id => $c_branch) {
            $counter = 0;
            while (true) {
                if ($counter++ >= 15) break;
                $c_parent_id = end($c_branch)->id_parent;
                if ($c_parent_id) {
                    $c_parent = tree_item::select($c_parent_id, $breadcrumbs->id);
                    $branches[$c_id][$c_parent->id] = $c_parent; }
                else break;
            }
        }
        # find the longest branch
        $longest = [];
        foreach ($branches as $c_branch) {
            if (count($c_branch) > count($longest)) {
                $longest = $c_branch;
            }
        }
        # insert new links to breadcrumbs
        foreach (array_reverse($longest) as $c_item) {
            $breadcrumbs->link_insert(
                $c_item->id,
                $c_item->title,
                $c_item->href_get() ?: null
            );
        }

        # ─────────────────────────────────────────────────────────────────────
        # find all active tabs items
        # ─────────────────────────────────────────────────────────────────────

        $active_tab = null;
        $blocks = page::get_current()->blocks;
        if (is_array($blocks)) {
            foreach ($blocks as $c_id_area => $c_blocks_by_area) {
                foreach ($c_blocks_by_area as $c_block) {
                    if ($c_block instanceof block             &&
                        $c_block->type             === 'link' && strpos(
                        $c_block->source, 'tabs/') === 0) {
                        $active_tab = storage::get('data')->select($c_block->source, true);
                    }
                }
            }
        }
        $branches = [];
        if ($active_tab) {
            $active_tab->build();
            foreach (tab_item::select_all($active_tab->id) as $c_item) {
                if ($c_item->is_active      () ||
                    $c_item->is_active_trail()) {
                    $branches[][$c_item->id] = $c_item;
                }
            }
        }
        # find all parents (resolve all branches)
        foreach ($branches as $c_id => $c_branch) {
            $counter = 0;
            while (true) {
                if ($counter++ >= 15) break;
                $c_parent_id = end($c_branch)->id_parent;
                if ($c_parent_id) {
                    $c_parent = tab_item::select($c_parent_id);
                    $branches[$c_id][$c_parent->id] = $c_parent; }
                else break;
            }
        }
        # find the longest branch
        $longest = [];
        foreach ($branches as $c_branch) {
            if (count($c_branch) > count($longest)) {
                $longest = $c_branch;
            }
        }
        # insert new links to breadcrumbs
        foreach (array_reverse($longest) as $c_item) {
            $breadcrumbs->link_insert(
                $c_item->id,
                $c_item->title,
                $c_item->href_default_get() ?: false
            );
        }

    }

}
