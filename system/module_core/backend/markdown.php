<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
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
    if (empty($list->_wrapper_name))           $list->_wrapper_name = 'wrapper_data0';
    if (is_string($data) && trim($data) == '') $list->_wrapper_name = 'wrapper_data1';
    switch ($list->_wrapper_name) {
      case 'wrapper_data0':
      # insert data to the list
        $wrapper_data0_level = count($list->_ul_ol_pointers);
        $container = empty($list->_ul_ol_pointers[$wrapper_data0_level]) ? null :
                           $list->_ul_ol_pointers[$wrapper_data0_level];
        if ($container) $container = $container->child_select_last(); # get last li
        if ($container) $container = $container->child_select('wrapper_data0');
        if ($container) {
          $container->child_insert(
            is_string($data) ? new text($data) : $data
          );
          return true;
        }
        break;
      case 'wrapper_data1':
      # delete old pointer to the current paragraph
        if (is_string($data) && trim($data) == '') {
          $list->_c_paragraph = null;
          return true;
        }
      # insert new paragraph to the list
        if (empty($list->_c_paragraph) && $c_indent > 0) {
          $wrapper_data1_level = min($level, count($list->_ul_ol_pointers));
          $container = empty($list->_ul_ol_pointers[$wrapper_data1_level]) ? null :
                             $list->_ul_ol_pointers[$wrapper_data1_level];
          if ($container) $container = $container->child_select_last(); # get last li
          if ($container) $container = $container->child_select('wrapper_data1');
          if ($container) {
            $list->_c_paragraph = new markup('p');
            $container->child_insert(
              $list->_c_paragraph
            );
          }
        # convert text in previous lists to paragraphs
          foreach ($list->_ul_ol_pointers as $c_level => $c_pointer) {
            if ($c_level <= $level) {
              $container = $list->_ul_ol_pointers[$c_level];
              if ($container) $container = $container->child_select_last();
              if ($container) $container = $container->child_select('wrapper_data0');
              if ($container) {
                $new_p = new markup('p');
                foreach ($container->children_select() as $id => $c_child) {
                  if ($c_child instanceof text) {
                    $new_p->child_insert($c_child);
                    $container->child_delete($id);
                  }
                }
                if ($new_p->children_select_count()) {
                  $container->child_insert($new_p);
                }
              }
            }
          }
        }
      # insert data to current paragraph
        if (empty($list->_c_paragraph) == false) {
          $list->_c_paragraph->child_insert(
            is_string($data) ? new text($data) : $data
          );
          return true;
        }
        break;
    }
  }

  # level | min list indent          | max list indent
  # ─────────────────────────────────────────────────────────────────────
  #     1 | 4 * (level - 2) + 1 = -3 | min + 6 =  3
  #     2 | 4 * (level - 2) + 1 =  1 | min + 6 =  7
  #     3 | 4 * (level - 2) + 1 =  5 | min + 6 = 11
  #     4 | 4 * (level - 2) + 1 =  9 | min + 6 = 15
  #     5 | 4 * (level - 2) + 1 = 13 | min + 6 = 19
  #     6 | 4 * (level - 2) + 1 = 17 | min + 6 = 23
  #     7 | 4 * (level - 2) + 1 = 21 | min + 6 = 27
  #     8 | 4 * (level - 2) + 1 = 25 | min + 6 = 31
  #     9 | 4 * (level - 2) + 1 = 29 | min + 6 = 35
  # ─────────────────────────────────────────────────────────────────────

  # level | min paragraph indent     | max paragraph indent
  # ─────────────────────────────────────────────────────────────────────
  #     1 | 4 * (level - 1) + 1 =  1 | min + 3 =  4
  #     2 | 4 * (level - 1) + 1 =  5 | min + 3 =  8
  #     3 | 4 * (level - 1) + 1 =  9 | min + 3 = 12
  #     4 | 4 * (level - 1) + 1 = 13 | min + 3 = 16
  #     5 | 4 * (level - 1) + 1 = 17 | min + 3 = 20
  #     6 | 4 * (level - 1) + 1 = 21 | min + 3 = 24
  #     7 | 4 * (level - 1) + 1 = 25 | min + 3 = 28
  #     8 | 4 * (level - 1) + 1 = 29 | min + 3 = 32
  #     9 | 4 * (level - 1) + 1 = 33 | min + 3 = 36
  # ─────────────────────────────────────────────────────────────────────

  static function markdown_to_markup($markdown) {
    $pool = new node;
    $strings = explode(nl, $markdown);
    foreach ($strings as $c_number => $c_string) {
      $c_string          = str_replace(tb, '    ', $c_string);
      $c_indent          = strspn($c_string, ' ');
      $c_list_level      = (int)floor(($c_indent - 1) / 4) + 2 ?: 1;
      $c_paragraph_level = (int)floor(($c_indent - 1) / 4) + 1 ?: 1;
      $c_last_item = $pool->child_select_last();
      $c_last_type = static::_node_universal_type_get($c_last_item);
      $c_matches = [];

    # ─────────────────────────────────────────────────────────────────────
    # headers
    # ─────────────────────────────────────────────────────────────────────
      $n_header = null;
      $c_matches = [];
      if (preg_match('%^(?<marker>[-=]+)[ ]*$%S', $c_string, $c_matches)) {
        if ($c_matches['marker'][0] == '=') $n_header = new markup('h1', [], $strings[$c_number - 1]);
        if ($c_matches['marker'][0] == '-') $n_header = new markup('h2', [], $strings[$c_number - 1]);
      # delete previous insertion
        if ($c_last_type == 'p' && $c_last_item->child_select_first() instanceof text) $pool->child_delete($pool->child_select_last_id());
        if ($c_last_type == 'header'                                                 ) $pool->child_delete($pool->child_select_last_id());
        if ($c_last_type == 'hr'                                                     ) $pool->child_delete($pool->child_select_last_id());
      }
      $c_matches = [];
      if (preg_match('%^(?<marker>[#]{1,6})(?<return>.+)$%S', $c_string, $c_matches)) {
        $n_header = new markup('h'.strlen($c_matches['marker']), [], trim($c_matches['return']));
      }
      if ($n_header) {
      # special case: list|header
        if ($c_last_type == 'list') {
          static::_list_data_insert($c_last_item, $n_header, $c_indent);
          continue;
        }
      # default case
        $pool->child_insert($n_header);
        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
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

    # ─────────────────────────────────────────────────────────────────────
    # lists
    # ─────────────────────────────────────────────────────────────────────
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[*+-]|[0-9]+(?<dot>[.]))'.
                       '(?<noises>[ ]{1,})'.
                       '(?<return>.+)$%S', $c_string, $c_matches)) {
      # special cases: p|list, blockquote|list, code|list
        if ($c_last_type == 'p')          {$c_last_item->child_insert(            new text(nl.$c_string));  continue;}
        if ($c_last_type == 'blockquote') {$c_last_item->child_select('text')->text_append(nl.$c_string);   continue;}
        if ($c_last_type == 'pre')        {$pool->child_insert(     new text(htmlspecialchars($c_string))); continue;}
      # create new list container (ol|ul)
        if ($c_last_type != 'list' && $c_indent < 4) {
          if ($c_list_level > 1)
              $c_list_level = 1;
          $c_last_item = new markup($c_matches['dot'] ? 'ol' : 'ul');
          $c_last_item->_ul_ol_pointers[1] = $c_last_item;
          $c_last_type = 'list';
          $pool->child_insert($c_last_item);
        }
      # create new list sub container (ol|ul)
        if (empty($c_last_item->_ul_ol_pointers[$c_list_level - 0]) &&
           !empty($c_last_item->_ul_ol_pointers[$c_list_level - 1])) {
          $new_container = new markup($c_matches['dot'] ? 'ol' : 'ul');
                       $c_last_item->_ul_ol_pointers[$c_list_level - 0] = $new_container;
          $parent_li = $c_last_item->_ul_ol_pointers[$c_list_level - 1]->child_select_last();
          if ($parent_li) $parent_li->child_select('wrapper_container')->child_insert($new_container);
        }
       # delete old pointers to list containers (ol|ul)
         foreach ($c_last_item->_ul_ol_pointers as $c_level => $c_pointer) {
           if ($c_level > $c_list_level) {
             unset($c_last_item->_ul_ol_pointers[$c_level]);
           }
         }
       # insert new list item (li)
         unset($c_last_item->_wrapper_name);
         $new_li = new markup('li');
         $new_li->child_insert(new node, 'wrapper_data0');
         $new_li->child_insert(new node, 'wrapper_container');
         $new_li->child_insert(new node, 'wrapper_data1');
         $c_last_item->_ul_ol_pointers[$c_list_level]->child_insert($new_li);
         static::_list_data_insert($c_last_item, $c_matches['return'], $c_indent);
         continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # blockquotes
    # ─────────────────────────────────────────────────────────────────────
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,3})'.
                       '(?<marker>[>][ ]{0,1})'.
                       '(?<return>.+)$%S', $c_string, $c_matches)) {
      # create new blockquote container
        if ($c_last_type != 'blockquote') {
          $c_last_item = new markup('blockquote');
          $c_last_item->child_insert(new text(''), 'text');
          $pool->child_insert($c_last_item);
        }
      # insert new blockquote string
        $c_last_item->child_select('text')->text_append(
          nl.$c_matches['return']
        );
        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # paragraphs
    # ─────────────────────────────────────────────────────────────────────
    # special cases: list|text, list|nl
      if ($c_last_type == 'list') {
        if (static::_list_data_insert($c_last_item, $c_string, $c_indent, $c_paragraph_level)) {
          continue;
        }
      }
      if (trim($c_string) == '') {
        if ($c_last_type == 'text') {$c_last_item->text_append(nl);     continue;}
        if ($c_last_type != 'text') {$pool->child_insert(new text(nl)); continue;}
      } else {
      # special cases: blockquote|text, p|text
        if ($c_last_type == 'blockquote') {$c_last_item->child_select('text')->text_append(nl.$c_string);  continue;}
        if ($c_last_type == 'p'         ) {$c_last_item->child_insert(            new text(nl.$c_string)); continue;}
      # special cases: |text, header|text, hr|text
        if ($c_indent < 4) {
          $pool->child_insert(new markup('p', [], $c_string));
          continue;
        }
      }

    # ─────────────────────────────────────────────────────────────────────
    # code (last prioruty)
    # ─────────────────────────────────────────────────────────────────────
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{4})'.
                       '(?<noises>[ ]{0,})'.
                       '(?<return>.*)$%S', $c_string, $c_matches)) {
      # create new code container
        if ($c_last_type != 'pre') {
          $c_last_item = new markup('pre');
          $c_last_item->child_insert(new markup('code'), 'code');
          $pool->child_insert($c_last_item);
        }
      # insert new code string
        if ( $c_last_item->child_select('code')->children_select_count() )
             $c_last_item->child_select('code')->child_insert(new text(nl.htmlspecialchars($c_matches['return'])));
        else $c_last_item->child_select('code')->child_insert(new text(   htmlspecialchars($c_matches['return'])));
        continue;
      }

    }

  # ─────────────────────────────────────────────────────────────────────
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