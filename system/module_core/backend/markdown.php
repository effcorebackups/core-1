<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class markdown {

  ###################
  ### separations ###
  ###################

  # ┌────────────╥────────┬───────────┬────────────┬──────────────┬──────────────┐
  # │ min breaks ║ header │ paragraph │ blockquote │ list         │ code         │
  # ╞════════════╬════════╪═══════════╪════════════╪══════════════╪══════════════╡
  # │     header ║ ''     │ ''        │ ''         │ ''           │ ''           │
  # │  paragraph ║ ''     │ nl        │ ''         │ nl           │ nl           │
  # │ blockquote ║ nl     │ nl        │ nl.'text'  │ nl           │ nl           │
  # │       list ║ nl     │ nl        │ nl         │ nl.'text'.nl │ nl.'text'.nl │
  # │       code ║ ''     │ ''        │ ''         │ nl           │    'text'.nl │
  # └────────────╨────────┴───────────┴────────────┴──────────────┴──────────────┘

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

  static function _text_process__insert_br($text_object, $new_text) {
    $text = $text_object->text_select();
    if (substr($text, -2) === '  ')
               $text = rtrim($text, ' ').' '.((new markup_simple('br'))->render());
    $text_object->text_update($text.$new_text);
    return $text_object;
  }

  static function _text_process__delete_last_line($text_object) {
    $text = $text_object->text_select();
    $last_nl_pos = strrpos($text, nl);
    $text = substr($text, 0, $last_nl_pos !== false ? $last_nl_pos : null);
    $text_object->text_update($text);
    return $text_object;
  }

  static function _list_process__insert_data($list, $data, $depth = null) {
    $max_depth = count($list->_ul_ol_pointers);
    $container = empty($list->_ul_ol_pointers[$max_depth]) ? null :
                       $list->_ul_ol_pointers[$max_depth];
    if ($container) $last_list = $container->child_select_last();
    if ($last_list) {
        $last_list->child_insert(
          is_string($data) ? new text($data) : $data
        );
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
    # headers
    # ─────────────────────────────────────────────────────────────────────

    # setext-style
      $c_matches = [];
      if (preg_match('%^(?<marker>[-]+[ ]*|[=]+[ ]*)$%S', $c_string, $c_matches)) {
      # delete previous insertion
        if ($c_last_type === 'header') $pool->child_delete($pool->child_select_last_id());
        if ($c_last_type === 'hr'    ) $pool->child_delete($pool->child_select_last_id());
        if ($c_last_type === 'p'     ) {
          static::_text_process__delete_last_line($c_last_item->child_select('text'));
          if ($c_last_item->child_select('text')->text_select() === '') {
            $pool->child_delete($pool->child_select_last_id());
          }
        }

      # make new header
        if ($c_matches['marker'][0] === '=') $c_size = 1;
        if ($c_matches['marker'][0] === '-') $c_size = 2;
        $c_last_item = new markup('h'.$c_size, [], trim($strings[$c_number - 1]));
        $c_last_type = 'header';
        $pool->child_insert($c_last_item);
        continue;
      }

    # atx-style
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[#]{1,6})'.
                       '(?<spaces>[ ]{1,})'.
                       '(?<return>.+)$%S', $c_string, $c_matches)) {
        $c_size = strlen($c_matches['marker']);

      # case: list|header
        if ($c_indent > 1) {
          if ($c_last_type === 'list') {
            $c_list_depth = (int)(floor($c_indent - $c_last_item->_ul_ol_start_indent) / 2) + 1;
            static::_list_process__insert_data($c_last_item, new markup('h'.$c_size, [], trim($c_matches['return'], ' #')), $c_list_depth);
            continue;
          }
        }

      # make new header
        if ($c_indent < 4) {
          $c_last_item = new markup('h'.$c_size, [], trim($c_matches['return'], ' #'));
          $c_last_type = 'header';
          $pool->child_insert($c_last_item);
          continue;
        }
      }

    # ─────────────────────────────────────────────────────────────────────
    # horizontal rules
    # ─────────────────────────────────────────────────────────────────────

      if (preg_match('%^(?<indent>[ ]{0,3})'.
                       '(?<marker>([*][ ]{0,2}){3,}|'.
                                 '([-][ ]{0,2}){3,}|'.
                                 '([_][ ]{0,2}){3,})'.
                       '(?<spaces>[ ]{0,})$%S', $c_string)) {
        $c_last_item = new markup_simple('hr');
        $c_last_type = 'hr';
        $pool->child_insert($c_last_item);
        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # lists
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[*+-]|[0-9]+(?<dot>[.]))'.
                       '(?<spaces>[ ]{1,})'.
                       '(?<return>.+)$%S', $c_string, $c_matches)) {
      # case: p|list
        if ($c_last_type === 'p') {
          $c_last_item->child_select('text')->text_append(nl.$c_string);
          continue;
        }

      # case: blockquote|list
        if ($c_last_type === 'blockquote') {
          $c_last_item->child_select('text')->text_append(nl.$c_string);
          continue;
        }

      # case: pre|list
        if ($c_last_type === 'pre') {
          $c_last_item = new markup('p', [], ['text' => new text(ltrim($c_string, ' '))]);
          $c_last_type = 'p';
          $pool->child_insert($c_last_item);
          continue;
        }

      # create new list container (ol|ul)
        if ($c_last_type !== 'list' && $c_indent < 4) {
          $c_last_item = new markup($c_matches['dot'] ? 'ol' : 'ul');
          $c_last_item->_ul_ol_pointers[1] = $c_last_item;
          $c_last_item->_ul_ol_start_indent = $c_indent;
          $c_last_type = 'list';
          $pool->child_insert($c_last_item);
        }

        if ($c_last_type === 'list') {
        # calculate depth
          $c_list_depth = (int)(floor($c_indent - $c_last_item->_ul_ol_start_indent) / 2) + 1;
          if ($c_list_depth < 1) $c_list_depth = 1;
          while ($c_list_depth > 1 && empty($c_last_item->_ul_ol_pointers[$c_list_depth - 1]))
                 $c_list_depth--;
        # create new list sub container (ol|ul)
          if (empty($c_last_item->_ul_ol_pointers[$c_list_depth]) &&
             !empty($c_last_item->_ul_ol_pointers[$c_list_depth - 1])) {
            $new_container = new markup($c_matches['dot'] ? 'ol' : 'ul');
                         $c_last_item->_ul_ol_pointers[$c_list_depth] = $new_container;
            $parent_li = $c_last_item->_ul_ol_pointers[$c_list_depth - 1]->child_select_last();
            if ($parent_li) $parent_li->child_insert($new_container);
          }
        # delete old pointers to list containers (ol|ul)
          foreach ($c_last_item->_ul_ol_pointers as $c_depth => $c_pointer) {
            if ($c_depth > $c_list_depth) {
              unset($c_last_item->_ul_ol_pointers[$c_depth]);
            }
          }
        # insert new list item (li)
          if (!empty($c_last_item->_ul_ol_pointers[$c_list_depth])) {
            $new_li = new markup('li');
            $c_last_item->_ul_ol_pointers[$c_list_depth]->child_insert($new_li);
            static::_list_process__insert_data($c_last_item, $c_matches['return']);
          }
        }
        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # blockquote
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,3})'.
                       '(?<marker>[>][ ]{0,1})'.
                       '(?<return>.+)$%S', $c_string, $c_matches)) {

      # case: !blockquote|blockquote
        if ($c_last_type !== 'blockquote') {
          $c_last_item = new markup('blockquote', [], ['text' => new text($c_matches['return'])]);
          $c_last_type = 'blockquote';
          $pool->child_insert($c_last_item);
          continue;
        }

      # case: blockquote|blockquote
        if ($c_last_type === 'blockquote') {
          static::_text_process__insert_br($c_last_item->child_select('text'), nl.$c_matches['return']);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # nl
    # ─────────────────────────────────────────────────────────────────────

      if (trim($c_string) === '') {

      # cases: header|nl, hr|hl
        if ($c_last_type === 'header') continue;
        if ($c_last_type === 'hr'    ) continue;

      # case: list|nl
        if ($c_last_type === 'list') {
          $c_list_depth = (int)(floor($c_indent - $c_last_item->_ul_ol_start_indent) / 2) + 1;
          if (static::_list_process__insert_data($c_last_item, nl, $c_list_depth)) {
            continue;
          }
        }

      # case: blockquote|nl
        if ($c_last_type === 'blockquote') {
          $c_last_item->child_select('text')->text_append(nl);
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
    # text
    # ─────────────────────────────────────────────────────────────────────

      if (trim($c_string) !== '') {

      # case: list|text
        if ($c_last_type === 'list') {
          $c_list_depth = (int)(floor($c_indent - $c_last_item->_ul_ol_start_indent) / 2) + 1;
          if (static::_list_process__insert_data($c_last_item, $c_string, $c_list_depth)) {
            continue;
          }
        }

      # case: blockquote|text
        if ($c_last_type === 'blockquote') {
          $c_last_item->child_select('text')->text_append(nl.$c_string);
          continue;
        }

      # case: p|text
        if ($c_last_type === 'p') {
          static::_text_process__insert_br($c_last_item->child_select('text'), nl.$c_string);
          continue;
        }

      # cases: header|text, pre|text, hr|text, null|text
        if ($c_indent < 4) {
          $c_last_item = new markup('p', [], ['text' => new text(ltrim($c_string, ' '))]);
          $c_last_type = 'p';
          $pool->child_insert($c_last_item);
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # code (last prioruty)
    # ─────────────────────────────────────────────────────────────────────

      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{4})'.
                       '(?<spaces>[ ]{0,})'.
                       '(?<return>.*)$%S', $c_string, $c_matches)) {

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
  # recursive post process for blockquote
  # ─────────────────────────────────────────────────────────────────────

    foreach ($pool->children_select_recursive() as $c_item) {
      if ($c_item instanceof markup &&
          $c_item->tag_name === 'blockquote') {
        $c_child = $c_item->child_select('text');
        if ($c_child) {
          $c_markup = ltrim($c_child->text_select(), nl);
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