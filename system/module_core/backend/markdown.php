<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class markdown {

  ###################
  ### separations ###
  ###################

  # ┌────────────╥────────┬────┬─────────┬────────────┬───────────┬─────────┐
  # │ separators ║ header │ hr │ list    │ blockquote │ paragraph │ code    │
  # ╞════════════╬════════╪════╪═════════╪════════════╪═══════════╪═════════╡
  # │     header ║ -      │ -  │ -       │ -          │ -         │ -       │
  # │         hr ║ -      │ -  │ -       │ -          │ -         │ -       │
  # │       list ║ -      │ -  │ element │ -          │ nl        │ element │
  # │ blockquote ║ -      │ -  │ -       │ nl         │ nl        │ nl      │
  # │  paragraph ║ -      │ nl │ -       │ -          │ nl        │ nl      │
  # │       code ║ -      │ -  │ -       │ -          │ -         │ element │
  # └────────────╨────────┴────┴─────────┴────────────┴───────────┴─────────┘

  static function _node_universal_type_get($node) {
    $type = $node instanceof markup ||
            $node instanceof markup_simple ? $node->tag_name : null;
    if ($type === 'ul') return 'list';
    if ($type === 'ol') return 'list';
    if ($type === 'h1') return 'header';
    if ($type === 'h2') return 'header';
    if ($type === 'h3') return 'header';
    if ($type === 'h4') return 'header';
    if ($type === 'h5') return 'header';
    if ($type === 'h6') return 'header';
    return $type; # header|p|list|pre|blockquote|hr|null
  }

  static function _text_process__insert_line__ws_br($text_object, $new_text) {
    $text = $text_object->text_select();
    if (substr($text, -2) === '  ')
               $text = rtrim($text, ' ').' '.((new markup_simple('br'))->render());
    $text_object->text_update($text.$new_text);
    return $text_object;
  }

  static function _list_process__insert_data($list, $data, $cur_depth = null) {
    $max_depth = count($list->_pointers);
    if ($cur_depth !== null && !empty($list->_pointers[$cur_depth])) $container = $list->_pointers[$cur_depth];
    if ($cur_depth === null && !empty($list->_pointers[$max_depth])) $container = $list->_pointers[$max_depth];
    if (isset($container)) $last_list = $container->child_select_last();
    if (isset($last_list)) {
      $last_element = $last_list->child_select_last();
      if (is_string($data) === true && $last_element instanceof text === true && $last_element->text_select() === '') $last_element->text_insert(   $data);
      if (is_string($data) === true && $last_element instanceof text === true && $last_element->text_select() !== '') $last_element->text_append(nl.$data);
      if (is_string($data) === true && $last_element instanceof text !== true) $last_list->child_insert(new text($data));
      if (is_string($data) !== true) $last_list->child_insert($data);
    }
  }

  static function _list_process__select_last_element($list) {
    $last_container = $list->_pointers[count($list->_pointers)];
    $last_list      = $last_container->child_select_last();
    $last_element   = $last_list     ->child_select_last();
    return $last_element;
  }

  static function _list_process__delete_pointers($list, $from) {
    foreach ($list->_pointers as $c_depth => $c_pointer) {
      if ($c_depth > $from) {
        unset($list->_pointers[$c_depth]);
      }
    }
  }

  static function markdown_to_markup($markdown) {
    $pool = new node;
    $strings = explode(nl, $markdown);
    foreach ($strings as $c_number => $c_string) {
      $c_string    = str_replace(tb, '    ', $c_string);
      $c_indent    = strspn($c_string, ' ');
      $c_last_item = $pool->child_select_last();
      $c_last_type = static::_node_universal_type_get($c_last_item);
      $c_matches = [];

    # ─────────────────────────────────────────────────────────────────────
    # hr
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>([*][ ]{0,}){3,}|'.
                                 '([-][ ]{0,}){3,}|'.
                                 '([_][ ]{0,}){3,})'.
                       '(?<spaces>[ ]{0,})$%S', $c_string, $c_matches)) {

      # case: p|'---'
        if ($c_last_type === 'p' && $c_matches['marker'][0] === '-') {
          goto element_header_setext;
        }

      # case: list|hr
        if ($c_last_type === 'list' && $c_indent > 1) {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth  <  1                                                       ) static::_list_process__insert_data($c_last_item, new markup_simple('hr'), $c_cur_depth - 1);
          if ($c_cur_depth - $c_max_depth === 1                                                       ) static::_list_process__insert_data($c_last_item, new markup_simple('hr'));
          if ($c_cur_depth - $c_max_depth === 2                                                       ) static::_list_process__insert_data($c_last_item, new markup_simple('hr'));
          if ($c_cur_depth - $c_max_depth  >  2 && get_class($c_last_list_element) !== 'effcore\\node') static::_list_process__insert_data($c_last_item,                                                                                                                                                  trim($c_string));
          if ($c_cur_depth - $c_max_depth  >  2 && get_class($c_last_list_element) === 'effcore\\node') static::_list_process__insert_data($c_last_item, new markup('pre', [], ['code' => new markup('code', [], ['text' => new text(str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).htmlspecialchars(trim($c_string)))])]));
          continue;
        }

      # case: !list|hr
        if ($c_indent < 4) {
          $c_last_item = new markup_simple('hr');
          $c_last_type = 'hr';
          $pool->child_insert($c_last_item);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # header (setext-style)
    # ─────────────────────────────────────────────────────────────────────

      element_header_setext:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,3})'.
                       '(?<marker>[=]{1,}|[-]{1,})'.
                       '(?<spaces>[ ]{0,})$%S', $c_string, $c_matches)) {

        if ($c_matches['marker'][0] === '=') $c_size = 1;
        if ($c_matches['marker'][0] === '-') $c_size = 2;

      # case: p|header
        if ($c_last_type === 'p') {
          $pool->child_delete($pool->child_select_last_id());
          $c_text = $c_last_item->child_select('text')->text_select();
          $c_last_item = new markup('h'.$c_size, [], trim($c_text));
          $c_last_type = 'header';
          $pool->child_insert($c_last_item);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # header (atx-style)
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[#]{1,6})'.
                       '(?<spaces>[ ]{1,})'.
                       '(?<return>.{1,})$%S', $c_string, $c_matches)) {

        $c_size = strlen($c_matches['marker']);

      # case: list|header
        if ($c_last_type === 'list' && $c_indent > 1) {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth  <  1                                                       ) static::_list_process__insert_data($c_last_item, new markup('h'.$c_size, [], trim($c_matches['return'], ' #')), $c_cur_depth - 1);
          if ($c_cur_depth - $c_max_depth === 1                                                       ) static::_list_process__insert_data($c_last_item, new markup('h'.$c_size, [], trim($c_matches['return'], ' #')));
          if ($c_cur_depth - $c_max_depth === 2                                                       ) static::_list_process__insert_data($c_last_item, new markup('h'.$c_size, [], trim($c_matches['return'], ' #')));
          if ($c_cur_depth - $c_max_depth  >  2 && get_class($c_last_list_element) !== 'effcore\\node') static::_list_process__insert_data($c_last_item,                                                                                                                                                  trim($c_string));
          if ($c_cur_depth - $c_max_depth  >  2 && get_class($c_last_list_element) === 'effcore\\node') static::_list_process__insert_data($c_last_item, new markup('pre', [], ['code' => new markup('code', [], ['text' => new text(str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).htmlspecialchars(trim($c_string)))])]));
          continue;
        }

      # case: !list|header
        if ($c_indent < 4) {
          $c_last_item = new markup('h'.$c_size, [], trim($c_matches['return'], ' #'));
          $c_last_type = 'header';
          $pool->child_insert($c_last_item);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # list
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[*+-]|[0-9]+(?<dot>[.]))'.
                       '(?<spaces>[ ]{1,})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # create new list container (ol|ul)
        if ($c_last_type !== 'list' && $c_indent < 4) {
          $c_last_item = new markup($c_matches['dot'] ? 'ol' : 'ul');
          $c_last_item->_pointers[1] = $c_last_item;
          $c_last_item->_indent = $c_indent;
          $c_last_type = 'list';
          $pool->child_insert($c_last_item);
        }

        if ($c_last_type === 'list') {

        # calculate depth
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth < 1                                                    ) $c_cur_depth = 1;
          if ($c_cur_depth > 1 && empty($c_last_item->_pointers[$c_cur_depth - 1])) $c_cur_depth = count($c_last_item->_pointers) + 1;

        # create new list sub container (ol|ul)
          if (empty($c_last_item->_pointers[$c_cur_depth - 0]) === true &&
              empty($c_last_item->_pointers[$c_cur_depth - 1]) !== true) {
            $new_container = new markup($c_matches['dot'] ? 'ol' : 'ul');
            $prn_container = $c_last_item->_pointers[$c_cur_depth - 1];
            $prn_last_list = $prn_container->child_select_last();
            if ($prn_last_list) {
              $prn_last_list->child_insert($new_container);
              $c_last_item->_pointers[$c_cur_depth] = $new_container;
            }
          }

        # delete old pointers to list containers (ol|ul)
          static::_list_process__delete_pointers($c_last_item, $c_cur_depth);

        # insert new list item (li)
          if (!empty($c_last_item->_pointers[$c_cur_depth])) {
            $c_last_item->_pointers[$c_cur_depth]->child_insert(new markup('li'));
            static::_list_process__insert_data($c_last_item, $c_matches['return'], $c_cur_depth);
          }

        }
        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # blockquote
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[>][ ]{0,1})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # case: list|blockquote
        if ($c_last_type === 'list' && $c_indent > 1) {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth  <  1                                                       ) static::_list_process__insert_data($c_last_item, new markup('blockquote', [], ['text' => new text($c_matches['return'])]), $c_cur_depth - 1);
          if ($c_cur_depth - $c_max_depth === 1                                                       ) static::_list_process__insert_data($c_last_item, new markup('blockquote', [], ['text' => new text($c_matches['return'])]));
          if ($c_cur_depth - $c_max_depth === 2                                                       ) static::_list_process__insert_data($c_last_item, new markup('blockquote', [], ['text' => new text($c_matches['return'])]));
          if ($c_cur_depth - $c_max_depth  >  2 && get_class($c_last_list_element) !== 'effcore\\node') static::_list_process__insert_data($c_last_item,                                                  $c_matches['return']);
          if ($c_cur_depth - $c_max_depth  >  2 && get_class($c_last_list_element) === 'effcore\\node') static::_list_process__insert_data($c_last_item, new markup('pre', [], ['code' => new markup('code', [], ['text' => new text(str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).htmlspecialchars($c_matches['return']))])]));
          continue;
        }

      # case: !blockquote|blockquote
        if ($c_last_type !== 'blockquote' && $c_indent < 4) {
          $c_last_item = new markup('blockquote', [], ['text' => new text($c_matches['return'])]);
          $c_last_type = 'blockquote';
          $pool->child_insert($c_last_item);
          continue;
        }

      # case: blockquote|blockquote
        if ($c_last_type === 'blockquote') {
          static::_text_process__insert_line__ws_br($c_last_item->child_select('text'), nl.$c_matches['return']);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # text
    # ─────────────────────────────────────────────────────────────────────

      if (trim($c_string) !== '') {

      # case: list|text
        if ($c_last_type === 'list') {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if (get_class($c_last_list_element) !== 'effcore\\node') static::_list_process__insert_data($c_last_item, trim($c_string));
          if (get_class($c_last_list_element) === 'effcore\\node' && $c_cur_depth < 2) goto element_p_insert;
          if (get_class($c_last_list_element) === 'effcore\\node' && $c_cur_depth > 1 && $c_cur_depth - $c_max_depth  <  2) {static::_list_process__insert_data($c_last_item, new markup('p',   [],                                   ['text' => new text(                                                                     ltrim($c_string, ' '))]), $c_cur_depth - 1); static::_list_process__delete_pointers($c_last_item, $c_cur_depth - 1);}
          if (get_class($c_last_list_element) === 'effcore\\node' && $c_cur_depth > 1 && $c_cur_depth - $c_max_depth === 2) {static::_list_process__insert_data($c_last_item, new markup('p',   [],                                   ['text' => new text(                                                                     ltrim($c_string, ' '))]));}
          if (get_class($c_last_list_element) === 'effcore\\node' && $c_cur_depth > 1 && $c_cur_depth - $c_max_depth  >  2) {static::_list_process__insert_data($c_last_item, new markup('pre', [], ['code' => new markup('code', [], ['text' => new text(str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).htmlspecialchars(ltrim($c_string, ' ')))])]));}
          continue;
        }

      # case: blockquote|text
        if ($c_last_type === 'blockquote') {
          $c_last_item->child_select('text')->text_append(nl.$c_string);
          continue;
        }

      # case: p|text
        if ($c_last_type === 'p') {
          static::_text_process__insert_line__ws_br($c_last_item->child_select('text'), nl.$c_string);
          continue;
        }

      # cases: header|text, pre|text, hr|text, null|text
        if ($c_indent < 4) {
          element_p_insert:
          $c_last_item = new markup('p', [], ['text' => new text(ltrim($c_string, ' '))]);
          $c_last_type = 'p';
          $pool->child_insert($c_last_item);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # nl
    # ─────────────────────────────────────────────────────────────────────

      if (trim($c_string) === '') {

      # case: header|nl
        if ($c_last_type === 'header') {
          continue;
        }

      # case: hr|nl
        if ($c_last_type === 'hr') {
          continue;
        }

      # case: list|nl
        if ($c_last_type === 'list') {
          static::_list_process__insert_data($c_last_item, new node());
          continue;
        }

      # case: blockquote|nl
        if ($c_last_type === 'blockquote') {
          $c_last_item = new node();
          $c_last_type = null;
          $pool->child_insert($c_last_item);
          continue;
        }

      # case: pre|nl
        if ($c_last_type === 'pre') {
          $c_last_item->child_select('code')->child_select('text')->text_append(nl);
          continue;
        }

      # case: p|nl
        if ($c_last_type === 'p') {
          $c_last_item = new node();
          $c_last_type = null;
          $pool->child_insert($c_last_item);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # code
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{4})'.
                       '(?<spaces>[ ]{0,})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # case: !pre|pre
        if ($c_last_type !== 'pre') {
          $c_last_item = new markup('pre', [], ['code' => new markup('code', [], ['text' => new text($c_matches['spaces'].htmlspecialchars($c_matches['return']))])]);
          $c_last_type = 'pre';
          $pool->child_insert($c_last_item);
          continue;
        }

      # case: pre|pre
        if ($c_last_type === 'pre') {
          $c_last_item->child_select('code')->child_select('text')->text_append(nl.$c_matches['spaces'].htmlspecialchars($c_matches['return']));
          continue;
        }

      }

    }

  # ─────────────────────────────────────────────────────────────────────
  # recursive post process
  # ─────────────────────────────────────────────────────────────────────

    foreach ($pool->children_select_recursive() as $c_item) {
      if ($c_item instanceof markup) {
        switch ($c_item->tag_name) {
          case 'pre':
            $c_text_object = $c_item->child_select('code')->child_select('text');
            if ($c_text_object) {
              $c_text = $c_text_object->text_select();
              $c_text = trim($c_text, nl);
              $c_text_object->text_update(
                $c_text
              );
            }
            break;
          case 'blockquote':
            $c_text_object = $c_item->child_select('text');
            if ($c_text_object) {
              $c_text = $c_text_object->text_select();
              $c_text = rtrim(trim($c_text, nl), ' ');
              if ($c_text) {
                $c_item->child_delete('text');
                foreach (static::markdown_to_markup($c_text)->children_select() as $c_new_child) {
                  $c_item->child_insert($c_new_child);
                }
              }
            }
            break;
        } 
      }
    }

    return $pool;
  }

}}