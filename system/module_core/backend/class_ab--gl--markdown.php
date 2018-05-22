<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class markdown {

  static function _node_universal_type_get($node) {
    $type = $node instanceof markup ||
            $node instanceof markup_simple ? $node->tag_name : (
            $node instanceof text ? 'text' : null);
    if ($type == 'ul') return 'list';
    if ($type == 'ol') return 'list';
    if ($type == 'h1') return 'header';
    if ($type == 'h2') return 'header';
    if ($type == 'h3') return 'header';
    if ($type == 'h4') return 'header';
    if ($type == 'h5') return 'header';
    if ($type == 'h6') return 'header';
    return $type;
  }

  static function _list_data_insert($list, $data, $c_indent, $level = null) {
    if (empty($list->_wr_name))                $list->_wr_name = 'wr_data0';
    if (is_string($data) && trim($data) == '') $list->_wr_name = 'wr_data1';
    switch ($list->_wr_name) {
      case 'wr_data0':
      # add data to the list
        $wr_data0_level = count($list->_p_list);
        $acceptor = empty($list->_p_list[$wr_data0_level]) ? null :
                          $list->_p_list[$wr_data0_level];         # get list container
        if ($acceptor) $acceptor = $acceptor->child_select_last(); # get last li
        if ($acceptor) $acceptor = $acceptor->child_select('wr_data0');
        if ($acceptor) {
          $acceptor->child_insert(
            is_string($data) ? new text(nl.$data) : $data
          );
          return true;
        }
        break;
      case 'wr_data1':
      # delete old pointer to the current paragraph
        if (is_string($data) && trim($data) == '') {
          $list->_c_paragraph = null;
          return true;
        }
      # add new paragraph to the list
        if (empty($list->_c_paragraph) && $c_indent > 0) {
          $wr_data1_level = min($level, count($list->_p_list));
          $acceptor = empty($list->_p_list[$wr_data1_level]) ? null :
                            $list->_p_list[$wr_data1_level];         # get list container
          if ($acceptor) $acceptor = $acceptor->child_select_last(); # get last li
          if ($acceptor) $acceptor = $acceptor->child_select('wr_data1');
          if ($acceptor) {
            $list->_c_paragraph = new markup('p');
            $acceptor->child_insert(
              $list->_c_paragraph
            );
          }
        # convert text in previous lists to paragraphs
          foreach ($list->_p_list as $c_level => $c_pointer) {
            if ($c_level <= $level) {
              $acceptor = $list->_p_list[$c_level];
              if ($acceptor) $acceptor = $acceptor->child_select_last();
              if ($acceptor) $acceptor = $acceptor->child_select('wr_data0');
              if ($acceptor) {
                $new_p = new markup('p');
                foreach ($acceptor->children_select() as $id => $c_child) {
                  if ($c_child instanceof text) {
                    $new_p->child_insert($c_child);
                    $acceptor->child_delete($id);
                  }
                }
                if ($new_p->children_count()) {
                  $acceptor->child_insert($new_p);
                }
              }
            }
          }
        }
      # add data to current paragraph
        if (empty($list->_c_paragraph) == false) {
          $list->_c_paragraph->child_insert(
            is_string($data) ? new text(nl.$data) : $data
          );
          return true;
        }
        break;
    }
  }

  static function markdown_to_markup($markdown) {
    $pool = new node();
    $strings = explode(nl, $markdown);
    foreach ($strings as $c_num => $c_string) {
      $c_string = str_replace(tb, '    ', $c_string);
      $c_indent = strspn($c_string, ' ');
      $l_level = (int)floor((($c_indent - 0) / 4) + 1) ?: 1;
      $p_level = (int)floor((($c_indent - 1) / 4) + 1) ?: 1;
      $last_item = $pool->child_select_last();
      $last_type = static::_node_universal_type_get($last_item);
      $c_matches = [];

    # headers
    # ─────────────────────────────────────────────────────────────────────
      $n_header = null;
      if (preg_match('%^(?<marker>[-=]+)[ ]*$%S', $c_string, $c_matches)) {
        if ($c_matches['marker'][0] == '=') $n_header = new markup('h1', [], $strings[$c_num-1]);
        if ($c_matches['marker'][0] == '-') $n_header = new markup('h2', [], $strings[$c_num-1]);
      # delete previous insertion
        if ($last_type == 'p' && $last_item->child_select_first() instanceof text) $pool->child_delete($pool->child_select_last_id());
        if ($last_type == 'header')   $pool->child_delete($pool->child_select_last_id());
        if ($last_type == 'hr')       $pool->child_delete($pool->child_select_last_id());
      }
      if (preg_match('%^(?<marker>[#]{1,6})(?<return>.*)$%S', $c_string, $c_matches)) {
        $n_header = new markup('h'.strlen($c_matches['marker']), [], $c_matches['return']);
      }
      if ($n_header) {
      # special case: list|header
        if ($last_type == 'list') {
          static::_list_data_insert($last_item, $n_header, $c_indent);
          continue;
        }
      # default case
        $pool->child_insert($n_header);
        continue;
      }

    # horizontal rules
    # ─────────────────────────────────────────────────────────────────────
      if (preg_match('%^(?<indent>[ ]{0,3})'.
                       '(?<marker>([*][ ]{0,2}){3,}|'.
                                 '([-][ ]{0,2}){3,}|'.
                                 '([_][ ]{0,2}){3,})'.
                       '(?<noises>[ ]{0,})$%S', $c_string)) {
        $pool->child_insert(new markup_simple('hr'));
        continue;
      }

    # lists
    # ─────────────────────────────────────────────────────────────────────
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[*+-]|[0-9]+(?<dot>[.]))'.
                       '(?<noises>[ ]{1,})'.
                       '(?<return>[^ ].+)$%S', $c_string, $c_matches)) {
      # special cases: p|list, blockquote|list, code|list
        if ($last_type == 'p')          {$last_item->child_insert(new text(nl.$c_string)); continue;}
        if ($last_type == 'blockquote') {$last_item->child_select('text')->text_append(nl.$c_string); continue;}
        if ($last_type == 'pre')        {$pool->child_insert(new text(htmlspecialchars($c_string))); continue;}
      # create new list container
        if ($last_type != 'list' && $c_indent < 4) {
          $last_item = new markup($c_matches['dot'] ? 'ol' : 'ul');
          $last_item->_p_list[1] = $last_item;
          $last_type = 'list';
          $pool->child_insert($last_item);
        }
        if ($last_type == 'list') {
        # create new list sub container (ol/ul)
          if (empty($last_item->_p_list[$l_level-0]) &&
              empty($last_item->_p_list[$l_level-1]) == false) {
            $new_container = new markup($c_matches['dot'] ? 'ol' : 'ul');
                         $last_item->_p_list[$l_level-0] = $new_container;
            $parent_li = $last_item->_p_list[$l_level-1]->child_select_last();
            if ($parent_li) $parent_li->child_select('wr_container')
                                      ->child_insert($new_container);
          }
        # delete old pointers to list containers (ol/ul)
          foreach ($last_item->_p_list as $c_level => $c_pointer) {
            if ($c_level > $l_level) {
              unset($last_item->_p_list[$c_level]);
            }
          }
        # insert new list item (li)
          unset($last_item->_wr_name);
          $new_li = new markup('li');
          $new_li->child_insert(new node(), 'wr_data0');
          $new_li->child_insert(new node(), 'wr_container');
          $new_li->child_insert(new node(), 'wr_data1');
          $last_item->_p_list[$l_level]->child_insert($new_li);
          static::_list_data_insert($last_item, $c_matches['return'], $c_indent);
          continue;
        }
      }

    # blockquotes
    # ─────────────────────────────────────────────────────────────────────
      if (preg_match('%^(?<indent>[ ]{0,3})'.
                       '(?<marker>[>][ ]{0,1})'.
                       '(?<return>.+)$%S', $c_string, $c_matches)) {
      # create new blockquote container
        if ($last_type != 'blockquote') {
          $last_item = new markup('blockquote');
          $last_item->child_insert(new text(''), 'text');
          $pool->child_insert($last_item);
        }
      # insert new blockquote string
        $last_item->child_select('text')->text_append(
          nl.$c_matches['return']
        );
        continue;
      }

    # paragraphs
    # ─────────────────────────────────────────────────────────────────────
    # special cases: list|text, list|nl
      if ($last_type == 'list') {
        if (static::_list_data_insert($last_item, $c_string, $c_indent, $p_level)) {
          continue;
        }
      }
      if (trim($c_string) == '') {
        if ($last_type == 'text') {$last_item->text_append(nl); continue;}
        if ($last_type != 'text') {$pool->child_insert(new text(nl)); continue;}
      } else {
      # special cases: blockquote|text, p|text
        if ($last_type == 'blockquote') {$last_item->child_select('text')->text_append(nl.$c_string); continue;}
        if ($last_type == 'p')          {$last_item->child_insert(new text(nl.$c_string)); continue;}
      # special cases: |text, header|text, hr|text
        if ($c_indent < 4) {
          $pool->child_insert(new markup('p', [], $c_string));
          continue;
        }
      }

    # code (last prioruty)
    # ─────────────────────────────────────────────────────────────────────
      if (preg_match('%^(?<indent>[ ]{4})'.
                       '(?<noises>[ ]{0,})'.
                       '(?<return>[^ ].*)$%S', $c_string, $c_matches)) {
      # create new code container
        if ($last_type != 'pre') {
          $last_item = new markup('pre');
          $last_item->child_insert(new markup('code'), 'code');
          $pool->child_insert($last_item);
        }
      # insert new code string
        $last_item->child_select('code')->child_insert(
          new text(nl.htmlspecialchars($c_matches['return']))
        );
        continue;
      }

    }

  # postprocess for blockquote
  # ─────────────────────────────────────────────────────────────────────

    foreach ($pool->children_select_recursive() as $c_item) {
      if ($c_item instanceof markup &&
          $c_item->tag_name == 'blockquote') {
        $c_child = $c_item->child_select('text');
        if ($c_child) {
          $c_markup = trim($c_child->text_select());
          if ($c_markup) {
            $c_item->child_delete('text');
            foreach (static::markdown_to_markup($c_markup)->children_select() as $c_new_child) {
              $c_item->child_insert($c_new_child);
            }
          }
        }
      }
    }

    return $pool;
  }

}}