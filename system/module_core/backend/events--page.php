<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\markup;
          use \effcore\page_part;
          use \effcore\page;
          use \effcore\storage;
          use \effcore\tabs_item;
          use \effcore\text;
          use \effcore\tree_item;
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
    foreach ($branches as &$c_branch) {
      $counter = 0;
      while (true) {
        if ($counter++ >= 15) break;
        $c_parent_id = end($c_branch)->id_parent;
        if ($c_parent_id) {
            $c_parent = tree_item::select($c_parent_id, $breadcrumbs->id);
            $c_branch[$c_parent->id] = $c_parent;}
        else break;
      }
    }
  # find the longest branch
    $longest = [];
    foreach ($branches as &$c_branch) {
      if (count($c_branch) > count($longest)) {
        $longest = $c_branch;
      }
    }
  # insert new links to breadcrumbs
    foreach (array_reverse($longest) as $c_item) {
      $breadcrumbs->link_insert(
        $c_item->id,
        $c_item->title,
        $c_item->href_get() ?: false
      );
    }

  # ─────────────────────────────────────────────────────────────────────
  # find all active tabs items
  # ─────────────────────────────────────────────────────────────────────
    $active_tab = null;
    $page_parts = page::get_current()->parts;
    if (is_array($page_parts)) {
      foreach ($page_parts as $c_parts) {
        foreach ( $c_parts as $c_part ) {
          if ($c_part instanceof page_part &&
              $c_part->type == 'link'      && strpos(
              $c_part->source, 'tabs/') === 0) {
            $active_tab = storage::get('files')->select($c_part->source, true);
          }
        }
      }
    }
    $branches = [];
    if ($active_tab) {
      $active_tab->build();
      foreach (tabs_item::select_all($active_tab->id) as $c_item) {
        if ($c_item->is_active      () ||
            $c_item->is_active_trail()) {
          $branches[][$c_item->id] = $c_item;
        }
      }
    }
  # find all parents (resolve all branches)
    foreach ($branches as &$c_branch) {
      $counter = 0;
      while (true) {
        if ($counter++ >= 15) break;
        $c_parent_id = end($c_branch)->id_parent;
        if ($c_parent_id) {
            $c_parent = tabs_item::select($c_parent_id);
            $c_branch[$c_parent->id] = $c_parent;}
        else break;
      }
    }
  # find the longest branch
    $longest = [];
    foreach ($branches as &$c_branch) {
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

}}