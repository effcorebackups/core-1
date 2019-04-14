<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class node extends node_simple {

  public $children = [];

  function __construct($attributes = [], $children = [], $weight = 0) {
    parent::__construct($attributes, $weight);
  # ─────────────────────────────────────────────────────────────────────
  # allowed scalar   types: '', '…', '0', 0, 0.1
  # allowed compound types: [], […], obj{}, obj{…}
  # ─────────────────────────────────────────────────────────────────────
    if ($children !== null &&
        $children !== true &&
        $children !== false) {
      foreach (is_array($children) ? $children : [$children] as $id => $c_child) {
        $this->child_insert($c_child, $id);
      }
    }
  }

  ################
  ### children ###
  ################

  function children_count() {
    return count($this->children);
  }

  function children_select() {
    return $this->children;
  }

  function children_select_recursive($children = null, $npath = '', $is_parent_at_last = false) {
    $result = [];
    foreach ($children ?: $this->children as $c_id => $c_child) {
      $c_npath = $npath ? $npath.'/'.$c_id : $c_id;
      if ($is_parent_at_last == false) $result[$c_npath] = $c_child;
      if (!empty($c_child->children))  $result += $this->children_select_recursive($c_child->children, $c_npath, $is_parent_at_last);
      if ($is_parent_at_last != false) $result[$c_npath] = $c_child;
    }
    return $result;
  }

  function child_select($id) {
    return $this->children[$id] ?? null;
  }

  function child_select_first()         {return reset($this->children);}
  function child_select_last()          {return   end($this->children);}
  function child_select_first_id()      {$keys = array_keys($this->children); return reset($keys);}
  function child_select_last_id()       {$keys = array_keys($this->children); return   end($keys);}
  function child_select_prev($child)    {reset($this->children); do if (current($this->children) === $child) return prev($this->children) ?                      : null; while (next($this->children));}
  function child_select_next($child)    {reset($this->children); do if (current($this->children) === $child) return next($this->children) ?                      : null; while (next($this->children));}
  function child_select_prev_id($child) {reset($this->children); do if (current($this->children) === $child) return prev($this->children) ? key($this->children) : null; while (next($this->children));}
  function child_select_next_id($child) {reset($this->children); do if (current($this->children) === $child) return next($this->children) ? key($this->children) : null; while (next($this->children));}

  function child_insert_first($child, $new_id = null) {
    $id = ($new_id !== null ?
           $new_id : count($this->children));
    $this->children = [$id => $child] + $this->children;
    return $id;
  }

  function child_insert($child, $new_id = null) {
    $id = ($new_id !== null ?
           $new_id : count($this->children));
    $this->children[$id] = $child;
    return $id;
  }

  function child_insert_before($child, $before_id, $new_id = null) {
    $id = ($new_id !== null ?
           $new_id : count($this->children));
    $children = [];
    foreach ($this->children as $c_id => $c_child) {
      if ($c_id === $before_id) $children[$id] = $child;
      $children[$c_id] = &$c_child;
    }
    $this->children = $children;
    return $id;
  }

  function child_insert_after($child, $after_id, $new_id = null) {
    $id = ($new_id !== null ?
           $new_id : count($this->children));
    $children = [];
    foreach ($this->children as $c_id => $c_child) {
      $children[$c_id] = &$c_child;
      if ($c_id === $after_id) $children[$id] = $child;
    }
    $this->children = $children;
    return $id;
  }

  function child_update($id, $new_child) {
    $this->children[$id] = $new_child;
  }

  function child_delete($id) {
    unset($this->children[$id]);
  }

  function children_delete_all() {
    $this->children = [];
  }

  ##############
  ### render ###
  ##############

  function render() {
    if ($this->template) {
      return (template::make_new($this->template, [
        'attributes' => $this->render_attributes(),
        'self'       => $this->render_self(),
        'children'   => $this->render_children($this->children)
      ]))->render();
    } else {
      return $this->render_self().
             $this->render_children($this->children);
    }
  }

  function render_children($children, $join = true) {
    $rendered = [];
    foreach (core::array_sort_by_weight($children) as $c_child) {
      $rendered[] = $this->render_child($c_child);
    }
    return $join ? implode('', $rendered) :
                               $rendered;
  }

  function render_child($child) {
    return is_object($child) &&
       method_exists($child, 'render') ? $child->render() :
                                         $child;
  }

}}