<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class markdown {

  static function _node_type_get($node) {
    $type = null;
    if ($node instanceof text         ) $type = '_text';
    if ($node instanceof node         ) $type = '_delimiter';
    if ($node instanceof markup_simple) $type = $node->tag_name;
    if ($node instanceof markup       ) $type = $node->tag_name;
    if ($type === 'pre') $type = '_code';
    if ($type === 'ul') $type = '_list';
    if ($type === 'ol') $type = '_list';
    if ($type === 'h1') $type = '_header';
    if ($type === 'h2') $type = '_header';
    if ($type === 'h3') $type = '_header';
    if ($type === 'h4') $type = '_header';
    if ($type === 'h5') $type = '_header';
    if ($type === 'h6') $type = '_header';
    return $type; # _text|_delimiter|_code|_list|_header|p|blockquote|hr|null
  }

  static function _text_process__insert_line($element, $new_text, $with_br = true, $encode = false) {
    $text_object = null;
    if (static::_node_type_get($element) === '_code'     ) $text_object = $element->child_select('code')->child_select('text');
    if (static::_node_type_get($element) === 'blockquote') $text_object = $element->child_select('text');
    if (static::_node_type_get($element) === 'p'         ) $text_object = $element->child_select('text');
    if (static::_node_type_get($element) === '_text'     ) $text_object = $element;
    if ($text_object) {
      $text = $text_object->text_select();
      if ($encode     ) $new_text = htmlspecialchars($new_text);
      if ($text === '') $text =          $new_text;
      if ($text !== '') $text = $text.nl.$new_text;
      if ($with_br    ) $text = preg_replace('%[ ]+'.nl.'%', static::_get_markup_br()->render().nl, $text);
      $text_object->text_update($text);
      return $text_object;
    }
  }

  static function _list_process__insert_data($list, $data, $type = null, $cur_depth = null, $p1 = null) {
    $max_depth = count($list->_pointers);
    if ($cur_depth !== null && !empty($list->_pointers[$cur_depth])) $container = $list->_pointers[$cur_depth];
    if ($cur_depth === null && !empty($list->_pointers[$max_depth])) $container = $list->_pointers[$max_depth];
    if (isset($container)) $last_list = $container->child_select_last();
    if (isset($last_list)) {
      $last_element = $last_list->child_select_last();
      if ($type === 'hr'                                                                  ) return $last_list->child_insert(static::_get_markup_hr());
      if ($type === '_delimiter'                                                          ) return $last_list->child_insert(static::_get_delimiter());
      if ($type === '_header'                                                             ) return $last_list->child_insert(static::_get_markup_header($data, $p1));
      if ($type === '_code'      && static::_node_type_get($last_element) !== '_code'     ) return $last_list->child_insert(static::_get_markup_code($data));
      if ($type === 'blockquote' && static::_node_type_get($last_element) !== 'blockquote') return $last_list->child_insert(static::_get_markup_blockquote($data));
      if ($type === 'p'          && static::_node_type_get($last_element) !== 'p'         ) return $last_list->child_insert(static::_get_markup_paragraph($data));
      if ($type === '_text'      && static::_node_type_get($last_element) !== '_text'     ) return $last_list->child_insert(new text($data));
      if (                          static::_node_type_get($last_element) === '_code'     ) return static::_text_process__insert_line($last_element, $data, false, true);
      if (                          static::_node_type_get($last_element) === 'blockquote') return static::_text_process__insert_line($last_element, $data);
      if (                          static::_node_type_get($last_element) === 'p'         ) return static::_text_process__insert_line($last_element, $data);
      if (                          static::_node_type_get($last_element) === '_text'     ) return static::_text_process__insert_line($last_element, $data);
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

  static function _get_markup_hr() {
    return new markup_simple('hr');
  }

  static function _get_markup_br() {
    return new markup_simple('br');
  }

  static function _get_markup_header($data, $size = 1) {
    return new markup('h'.$size, [], trim($data, ' #'));
  }

  static function _get_markup_list_container($is_numbered = false) {
    if ($is_numbered) return new markup('ol');
    else              return new markup('ul');
  }

  static function _get_markup_list() {
    return new markup('li');
  }

  static function _get_markup_code($data) {
    return new markup('pre', [], [
      'code' => new markup('code', [], [
        'text' => new text(htmlspecialchars($data))
      ])]
    );
  }

  static function _get_markup_blockquote($data) {
    return new markup('blockquote', [], [
      'text' => new text($data)
    ]);
  }

  static function _get_markup_paragraph($data) {
    return new markup('p', [], [
      'text' => new text($data)
    ]);
  }

  static function _get_delimiter() {
    return new node();
  }

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

  static function markdown_to_markup($markdown) {
    $pool = new node;
    $strings = explode(nl, $markdown);
    foreach ($strings as $c_number => $c_string) {
      $c_string    = str_replace(tb, '    ', $c_string);
      $c_indent    = strspn($c_string, ' ');
      $c_last_item = $pool->child_select_last();
      $c_last_type = static::_node_type_get($c_last_item);
      $c_matches = [];

    # ─────────────────────────────────────────────────────────────────────
    # markup
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<return>[<][/]{0,1}[a-z0-9\\-]{1,}[>].*)$%S', $c_string, $c_matches)) {

      # case: !markup|markup
        if ($c_last_type !== '_text') {
          $pool->child_insert(new text($c_string));
          continue;
        }

      # case: markup|markup
        if ($c_last_type === '_text') {
          static::_text_process__insert_line($c_last_item, $c_string);
          continue;
        }

        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # hr
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>(?:[*][ ]{0,}){3,}|'.
                                 '(?:[-][ ]{0,}){3,}|'.
                                 '(?:[_][ ]{0,}){3,})'.
                       '(?<spaces>[ ]{0,})$%S', $c_string, $c_matches)) {

      # case: p|'---'
        if ($c_last_type === 'p' && $c_matches['marker'][0] === '-') {
          goto element_header_setext;
        }

      # case: list|hr
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth < 1                                                                 ) {static::_list_process__insert_data($c_last_item,                                                     trim($c_string), 'hr', $c_cur_depth - 1); continue;}
          if ($c_cur_depth - $c_max_depth > 0 && $c_cur_depth - $c_max_depth < 3                              ) {static::_list_process__insert_data($c_last_item,                                                     trim($c_string), 'hr'  );                 continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::_node_type_get($c_last_list_element) === '_delimiter') {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).trim($c_string), '_code');                continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::_node_type_get($c_last_list_element) === '_code'     ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).trim($c_string), '_code');                continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                 ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).trim($c_string), '_text');                continue;}
        }

      # case: !list|hr
        if ($c_indent < 4) {
          $pool->child_insert(static::_get_markup_hr());
          continue;
        }

      # case: hr in code
        if ($c_indent > 3) {
          goto element_code;
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
          $c_text = $c_last_item->child_select('text')->text_select();
          $pool->child_delete($pool->child_select_last_id());
          $pool->child_insert(static::_get_markup_header($c_text, $c_size));
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
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth < 1                                                                 ) {static::_list_process__insert_data($c_last_item, $c_matches['return'], '_header', $c_cur_depth - 1, $c_size);                   continue;}
          if ($c_cur_depth - $c_max_depth > 0 && $c_cur_depth - $c_max_depth < 3                              ) {static::_list_process__insert_data($c_last_item, $c_matches['return'], '_header', null,             $c_size);                   continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::_node_type_get($c_last_list_element) === '_delimiter') {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).trim($c_string), '_code'); continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::_node_type_get($c_last_list_element) === '_code'     ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).trim($c_string), '_code'); continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                 ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).trim($c_string), '_text'); continue;}
        }

      # case: !list|header
        if ($c_indent < 4) {
          $pool->child_insert(static::_get_markup_header($c_matches['return'], $c_size));
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
        if ($c_last_type !== '_list' && $c_indent < 4) {
          $c_last_item = static::_get_markup_list_container($c_matches['dot']);
          $c_last_item->_pointers[1] = $c_last_item;
          $c_last_item->_indent = $c_indent;
          $c_last_type = '_list';
          $pool->child_insert($c_last_item);
        }

        if ($c_last_type === '_list') {

        # calculate depth
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth < 1                                                    ) $c_cur_depth = 1;
          if ($c_cur_depth > 1 && empty($c_last_item->_pointers[$c_cur_depth - 1])) $c_cur_depth = count($c_last_item->_pointers) + 1;

        # create new list sub container (ol|ul)
          if (empty($c_last_item->_pointers[$c_cur_depth - 0]) === true &&
              empty($c_last_item->_pointers[$c_cur_depth - 1]) !== true) {
            $new_container = static::_get_markup_list_container($c_matches['dot']);
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
            $c_last_item->_pointers[$c_cur_depth]->child_insert(static::_get_markup_list());
            static::_list_process__insert_data($c_last_item, $c_matches['return'], '_text', $c_cur_depth);
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
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth < 1                                                                 ) {static::_list_process__insert_data($c_last_item,                                                     $c_matches['return'],                      'blockquote', $c_cur_depth - 1); continue;}
          if ($c_cur_depth - $c_max_depth > 0 && $c_cur_depth - $c_max_depth < 3                              ) {static::_list_process__insert_data($c_last_item,                                                     $c_matches['return'],                      'blockquote');                   continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::_node_type_get($c_last_list_element) === '_delimiter') {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).$c_matches['marker'].$c_matches['return'], '_code'      );                  continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::_node_type_get($c_last_list_element) === '_code'     ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).$c_matches['marker'].$c_matches['return'], '_code'      );                  continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                 ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).$c_matches['marker'].$c_matches['return'], '_text'      );                  continue;}
        }

      # case: !blockquote|blockquote
        if ($c_last_type !== 'blockquote' && $c_indent < 4) {
          $pool->child_insert(static::_get_markup_blockquote($c_matches['return']));
          continue;
        }

      # case: blockquote|blockquote
        if ($c_last_type === 'blockquote') {
          static::_text_process__insert_line($c_last_item, $c_matches['return']);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # text
    # ─────────────────────────────────────────────────────────────────────

      if (trim($c_string) !== '') {

      # case: markup|text
        if ($c_last_type === '_text') {
          static::_text_process__insert_line($c_last_item, $c_string);
          continue;
        }

      # case: list|text
        if ($c_last_type === '_list') {
          $c_last_list_element = static::_list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if (                                                      static::_node_type_get($c_last_list_element) !== '_delimiter' && static::_node_type_get($c_last_list_element) === 'p'    ) {static::_list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), 'p'    ); continue;}
          if (                                                      static::_node_type_get($c_last_list_element) !== '_delimiter' && static::_node_type_get($c_last_list_element) === '_code') {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).ltrim($c_string, ' '), '_code'); continue;}
          if (                                                      static::_node_type_get($c_last_list_element) !== '_delimiter' && static::_node_type_get($c_last_list_element) !== 'p'    ) {static::_list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), '_text'); continue;}
          if ($c_indent > 1 && $c_cur_depth - $c_max_depth  <  2 && static::_node_type_get($c_last_list_element) === '_delimiter'                                                            ) {static::_list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), 'p', $c_cur_depth - 1); static::_list_process__delete_pointers($c_last_item, $c_cur_depth - 1); continue;}
          if ($c_indent > 1 && $c_cur_depth - $c_max_depth === 2 && static::_node_type_get($c_last_list_element) === '_delimiter'                                                            ) {static::_list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), 'p'    ); continue;}
          if ($c_indent > 1 && $c_cur_depth - $c_max_depth  >  2 && static::_node_type_get($c_last_list_element) === '_delimiter'                                                            ) {static::_list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).ltrim($c_string, ' '), '_code'); continue;}
          if ($c_indent < 2                                      && static::_node_type_get($c_last_list_element) === '_delimiter'                                                            ) {goto element_p_insert;}
        }

      # case: blockquote|text
        if ($c_last_type === 'blockquote') {
          static::_text_process__insert_line($c_last_item, $c_string);
          continue;
        }

      # case: p|text
        if ($c_last_type === 'p') {
          static::_text_process__insert_line($c_last_item, $c_string);
          continue;
        }

      # cases: header|text, pre|text, hr|text, null|text
        if ($c_indent < 4) {
          element_p_insert:
          $pool->child_insert(static::_get_markup_paragraph(ltrim($c_string, ' ')));
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # nl
    # ─────────────────────────────────────────────────────────────────────

      if (trim($c_string) === '') {

      # case: markup|nl
        if ($c_last_type === '_text') {
          static::_text_process__insert_line($c_last_item, '');
          continue;
        }

      # case: header|nl
        if ($c_last_type === '_header') {
          continue;
        }

      # case: hr|nl
        if ($c_last_type === 'hr') {
          continue;
        }

      # case: list|nl
        if ($c_last_type === '_list') {
          static::_list_process__insert_data($c_last_item, null, '_delimiter');
          continue;
        }

      # case: blockquote|nl
        if ($c_last_type === 'blockquote') {
          $pool->child_insert(static::_get_delimiter());
          continue;
        }

      # case: pre|nl
        if ($c_last_type === '_code') {
          static::_text_process__insert_line($c_last_item, '', false);
          continue;
        }

      # case: p|nl
        if ($c_last_type === 'p') {
          $pool->child_insert(static::_get_delimiter());
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # code
    # ─────────────────────────────────────────────────────────────────────

      element_code:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{4})'.
                       '(?<spaces>[ ]{0,})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # case: !pre|pre
        if ($c_last_type !== '_code') {
          $pool->child_insert(static::_get_markup_code($c_matches['spaces'].$c_matches['return']));
          continue;
        }

      # case: pre|pre
        if ($c_last_type === '_code') {
          static::_text_process__insert_line($c_last_item, $c_matches['spaces'].$c_matches['return'], false, true);
          continue;
        }

      }

    }

  # ─────────────────────────────────────────────────────────────────────
  # recursive post process
  # ─────────────────────────────────────────────────────────────────────

    foreach ($pool->children_select_recursive() as $c_item) {
      switch (static::_node_type_get($c_item)) {
        case '_code':
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

    return $pool;
  }

}}