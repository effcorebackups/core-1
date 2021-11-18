<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class markdown {

  const blockquote_max_depth = 15;
  static protected $blockquote_cur_depth = 0;
  static protected $references = [];

  static function node_type_get($element) {
    $type = null;
    if ($element instanceof text                                  ) $type = '_text';
    if ($element instanceof text && isset($element->markdown_type)) $type = $element->markdown_type;
    if ($element instanceof node && isset($element->markdown_type)) $type = $element->markdown_type;
    if ($element instanceof markup_simple                         ) $type = $element->tag_name;
    if ($element instanceof markup                                ) $type = $element->tag_name;
    if ($type === 'pre') $type = '_code';
    if ($type === 'ul' ) $type = '_list';
    if ($type === 'ol' ) $type = '_list';
    if ($type === 'li' ) $type = '_list_item';
    if ($type === 'h1' ) $type = '_header';
    if ($type === 'h2' ) $type = '_header';
    if ($type === 'h3' ) $type = '_header';
    if ($type === 'h4' ) $type = '_header';
    if ($type === 'h5' ) $type = '_header';
    if ($type === 'h6' ) $type = '_header';
    return $type; # blockquote, p, hr, _header, _list, _code, _markup, _text, _delimiter, null …
  }

  static function text_process__insert_line($element, $new_text, $with_br = true, $encode = false) {
    $text_object = null;
    if (static::node_type_get($element) === '_code'     ) $text_object = $element->child_select('code')->child_select('text');
    if (static::node_type_get($element) === 'blockquote') $text_object = $element->child_select('text');
    if (static::node_type_get($element) === 'p'         ) $text_object = $element->child_select('text');
    if (static::node_type_get($element) === '_text'     ) $text_object = $element;
    if (static::node_type_get($element) === '_markup'   ) $text_object = $element;
    if ($text_object) {
      $text = $text_object->text_select();
      if ($encode     ) $new_text = htmlspecialchars($new_text);
      if ($text === '') $text =          $new_text;
      if ($text !== '') $text = $text.nl.$new_text;
      if ($with_br    ) $text = preg_replace('%[ ]+'.nl.'%S', static::markup_br_get()->render().nl, $text);
      $text_object->text_update($text);
      return $text_object;
    }
  }

  static function list_process__insert_data($list, $data, $type = null, $cur_depth = null, $p1 = null) {
    $max_depth = count($list->_pointers);
    if ($cur_depth !== null && !empty($list->_pointers[$cur_depth])) $container = $list->_pointers[$cur_depth];
    if ($cur_depth === null && !empty($list->_pointers[$max_depth])) $container = $list->_pointers[$max_depth];
    if (isset($container)) $last_list = $container->child_select_last();
    if (isset($last_list)) {
      $last_element = $last_list->child_select_last();
      if ($type === 'hr'                                                                 ) return $last_list->child_insert(static::markup_hr_get());
      if ($type === '_delimiter'                                                         ) return $last_list->child_insert(static::delimiter_get());
      if ($type === '_header'                                                            ) return $last_list->child_insert(static::markup_header_get($data, $p1));
      if ($type === '_code'      && static::node_type_get($last_element) !== '_code'     ) return $last_list->child_insert(static::markup_code_get($data));
      if ($type === 'blockquote' && static::node_type_get($last_element) !== 'blockquote') return $last_list->child_insert(static::markup_blockquote_get($data));
      if ($type === 'p'          && static::node_type_get($last_element) !== 'p'         ) return $last_list->child_insert(static::markup_paragraph_get($data));
      if ($type === '_text'      && static::node_type_get($last_element) !== '_text'     ) return $last_list->child_insert(new text($data));
      if (                          static::node_type_get($last_element) === '_code'     ) return static::text_process__insert_line($last_element, $data, false, true);
      if (                          static::node_type_get($last_element) === 'blockquote') return static::text_process__insert_line($last_element, $data);
      if (                          static::node_type_get($last_element) === 'p'         ) return static::text_process__insert_line($last_element, $data);
      if (                          static::node_type_get($last_element) === '_text'     ) return static::text_process__insert_line($last_element, $data);
    }
  }

  static function list_process__select_last_element($list) {
    $last_container = $list->_pointers[count($list->_pointers)];
    $last_list      = $last_container->child_select_last();
    $last_element   = $last_list     ->child_select_last();
    return $last_element;
  }

  static function list_process__delete_pointers($list, $from) {
    foreach ($list->_pointers as $c_depth => $c_pointer) {
      if ($c_depth > $from) {
        unset($list->_pointers[$c_depth]);
      }
    }
  }

  static function delimiter_get() {
    $node = new node;
    $node->markdown_type = '_delimiter';
    return $node;
  }

  static function markup_hr_get() {
    return new markup_simple('hr');
  }

  static function markup_br_get() {
    return new markup_simple('br');
  }

  static function markup_list_container_get($is_numbered = false) {
    if ($is_numbered) return new markup('ol');
    else              return new markup('ul');
  }

  static function markup_list_get() {
    return new markup('li');
  }

  static function markup_header_get($data, $size = 1) {
    return new markup('h'.$size, [], [
      'text' => new text(trim($data, ' #'))
    ]);
  }

  static function markup_code_get($data) {
    return new markup('pre', [], [
      'code' => new markup('code', [], [
        'text' => new text(htmlspecialchars($data))
      ])]
    );
  }

  static function markup_blockquote_get($data) {
    return new markup('blockquote', [], [
      'text' => new text($data)
    ]);
  }

  static function markup_paragraph_get($data) {
    return new markup('p', [], [
      'text' => new text($data)
    ]);
  }

  static function markup_markup_get($data) {
    $text = new text($data);
    $text->markdown_type = '_markup';
    return $text;
  }

  static function markup_inline_tags_get() {
    return [
      'a', 'br', 'bdi', 'abbr', 'audio', 'applet', 'acronym', 'noscript',
      'b', 'em', 'bdo', 'area', 'embed', 'button', 'command', 'progress',
      'i', 'rp', 'big', 'cite', 'input', 'canvas', 'marquee', 'textarea',
      'q', 'rt', 'del', 'code', 'label', 'keygen', 'noembed',
      's', 'tt', 'dfn', 'font', 'meter', 'object',
      'u',       'img', 'list', 'param', 'option',
                 'ins', 'mark', 'small', 'output',
                 'kbd', 'nobr', 'track', 'select',
                 'map', 'ruby', 'video', 'source',
                 'sub', 'samp',          'strike',
                 'sup', 'span',          'strong',
                 'var', 'time',
                 'wbr',
    ];
  }

  static function meta_encode($data) {
    return str_replace(['#', '*', '-', '=', '_', '>'], ['&#35;', '&#42;', '&#45;', '&#61', '&#95;', '&gt;'], $data);
  }

  static function string_prepare($data) {
    return str_replace([tb, '\\\\', '\\`', '\\*', '\\_', '\\{', '\\}', '\\[', '\\]', '\\(', '\\)', '\\#', '\\+', '\\-', '\\.', '\\!'], ['    ', '&#92;', '&#96;', '&#42;', '&#95', '&#123;', '&#125;', '&#91;', '&#93;', '&#40;', '&#41;', '&#35;', '&#43;', '&#45;', '&#46;', '&#33;'], $data);
  }

  # ┌────────────╥────────┬────┬─────────┬────────────┬───────────┬─────────┬────────┐
  # │ separators ║ header │ hr │ list    │ blockquote │ paragraph │ code    │ markup │
  # ╞════════════╬════════╪════╪═════════╪════════════╪═══════════╪═════════╪════════╡
  # │     header ║ -      │ -  │ -       │ -          │ -         │ -       │ -      │
  # │         hr ║ -      │ -  │ -       │ -          │ -         │ -       │ -      │
  # │       list ║ -      │ -  │ element │ -          │ nl        │ element │ -      │
  # │ blockquote ║ -      │ -  │ -       │ nl         │ nl        │ nl      │ -      │
  # │  paragraph ║ -      │ nl │ -       │ -          │ nl        │ nl      │ -      │
  # │       code ║ -      │ -  │ -       │ -          │ -         │ element │ -      │
  # │     markup ║ nl     │ nl │ nl      │ nl         │ nl        │ nl      │ -      │
  # └────────────╨────────┴────┴─────────┴────────────┴───────────┴─────────┴────────┘

  static function markdown_to_markup($data) {
    $pool = new node;
    $strings = explode(nl, $data);
    $inline_tags = core::array_keys_map(static::markup_inline_tags_get());
    foreach ($strings as $c_number => $c_string) {
      $c_string    = static::string_prepare($c_string);
      $c_indent    = strspn($c_string, ' ');
      $c_last_item = $pool->child_select_last();
      $c_last_type = static::node_type_get($c_last_item);
      $c_matches = [];

    # ─────────────────────────────────────────────────────────────────────
    # markup
    # ─────────────────────────────────────────────────────────────────────

      element_markup:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<return>[<][/]{0,1}(?<tag>[a-z0-9\\-]{1,})[^>]{0,}[>].*)$%S', $c_string, $c_matches)) {

        if (strpos($c_matches['tag'], 'x-') === 0)
             $is_inline_tag = true;
        else $is_inline_tag = isset($inline_tags[$c_matches['tag']]);

        if ($c_last_type === '_code' && $c_indent > 3) {
          goto element_code;
        }

      # *|markup
        if ($is_inline_tag) {
          goto element_text;
        }

      # default:
        if ($c_last_type === '_markup') {goto element_text;}
        if ($c_last_type !== '_markup') {
          $pool->child_insert(static::markup_markup_get($c_string));
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # hr
    # ─────────────────────────────────────────────────────────────────────

      element_hr:
      $c_matches = [];
      if (preg_match('%^(?<indent>'.'[ ]{0,})'.
                       '(?<marker>(?:[*][ ]{0,}){3,}|'.
                                 '(?:[-][ ]{0,}){3,}|'.
                                 '(?:[_][ ]{0,}){3,})'.
                       '(?<spaces>'.'[ ]{0,})$%S', $c_string, $c_matches)) {

      # case: p|'---'
        if ($c_last_type === 'p') {
          if ($c_matches['marker'][0] === '-' && strpbrk($c_matches['marker'], ' ') === false) {
            if ($c_indent > 3) goto element_text;
            if ($c_indent < 4) goto element_header_setext;
          }
        }

      # case: p|hr, blockquote|hr, header|hr, hr|hr, code|hr, markup|hr
        if ($c_last_type === 'p'          && $c_indent > 3) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'blockquote' && $c_indent > 3) {goto element_text;}
        if ($c_last_type === '_header'    && $c_indent > 3) {goto element_code;}
        if ($c_last_type === 'hr'         && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_code'      && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_markup'                    ) {goto element_text;}

      # case: list|hr
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth < 1                                                                ) {static::list_process__insert_data($c_last_item,                                                                         trim($c_string),  'hr', $c_cur_depth - 1); continue;}
          if ($c_cur_depth - $c_max_depth > 0 && $c_cur_depth - $c_max_depth < 3                             ) {static::list_process__insert_data($c_last_item,                                                                         trim($c_string),  'hr'  );                 continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_delimiter') {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    trim($c_string),  '_code');                continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_code'     ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    trim($c_string),  '_code');                continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).static::meta_encode(trim($c_string)), '_text');                continue;}
        }

      # default:
        if ($c_indent > 3) {goto element_code;}
        if ($c_indent < 4) {
          $pool->child_insert(static::markup_hr_get());
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # header (setext-style)
    # ─────────────────────────────────────────────────────────────────────

      element_header_setext:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[=]{1,}|'.
                                 '[-]{1,})'.
                       '(?<spaces>[ ]{0,})$%S', $c_string, $c_matches)) {

        if ($c_matches['marker'][0] === '=') $c_size = 1;
        if ($c_matches['marker'][0] === '-') $c_size = 2;

      # case: blockquote|header, p|header, code|header, header|header, hr|header, markup|header
        if ($c_last_type === 'blockquote'              ) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'p'       && $c_indent > 3) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === '_code'   && $c_indent < 4) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === '_header' && $c_indent < 4) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'hr'      && $c_indent < 4) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === '_code'   && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_header' && $c_indent > 3) {goto element_code;}
        if ($c_last_type === 'hr'      && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_markup'                 ) {goto element_text;}

      # default:
        if ($c_indent > 3) {goto element_code;}
        if ($c_indent < 4 && $c_last_type === 'p') {
          $c_text = $c_last_item->child_select('text')->text_select();
          $pool->child_delete($pool->child_select_last_id());
          $pool->child_insert(static::markup_header_get($c_text, $c_size));
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # header (atx-style)
    # ─────────────────────────────────────────────────────────────────────

      element_header_atx:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[#]{1,6})'.
                       '(?<spaces>[ ]{1,})'.
                       '(?<return>.{1,})$%S', $c_string, $c_matches)) {

        $c_size = strlen($c_matches['marker']);

      # case: p|header, blockquote|header, hr|header, header|header, code|header, markup|header
        if ($c_last_type === 'p'          && $c_indent > 3) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'blockquote' && $c_indent > 3) {goto element_text;}
        if ($c_last_type === 'hr'         && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_header'    && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_code'      && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_markup'                    ) {goto element_text;}

      # case: list|header
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth < 1                                                                ) {static::list_process__insert_data($c_last_item, $c_matches['return'], '_header', $c_cur_depth - 1, $c_size);                                        continue;}
          if ($c_cur_depth - $c_max_depth > 0 && $c_cur_depth - $c_max_depth < 3                             ) {static::list_process__insert_data($c_last_item, $c_matches['return'], '_header', null,             $c_size);                                        continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_delimiter') {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    trim($c_string),  '_code'); continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_code'     ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    trim($c_string), ' _code'); continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).static::meta_encode(trim($c_string)), '_text'); continue;}
        }

      # default:
        if ($c_indent > 3) {goto element_code;}
        if ($c_indent < 4) {
          $pool->child_insert(static::markup_header_get($c_matches['return'], $c_size));
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # list
    # ─────────────────────────────────────────────────────────────────────

      element_list:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[*+-]|[0-9]+(?<dot>[.]))'.
                       '(?<spaces>[ ]{1,})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # case: blockquote|list, p|list, hr|list, header|list, code|list, markup|list, list|list
        if ($c_last_type === 'blockquote' && $c_indent > 3) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'p'          && $c_indent > 3) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'hr'         && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_header'    && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_code'      && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_markup'                    ) {goto element_text;}
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_delimiter') {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    trim($c_string),  '_code'); continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_code'     ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    trim($c_string),  '_code'); continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).static::meta_encode(trim($c_string)), '_text'); continue;}
        }

      # default:
        if ($c_last_type !== '_list' && $c_indent > 3) {goto element_code;}
        if ($c_last_type !== '_list' && $c_indent < 4) {
          $c_last_item = static::markup_list_container_get($c_matches['dot']);
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
            $new_container = static::markup_list_container_get($c_matches['dot']);
            $prn_container = $c_last_item->_pointers[$c_cur_depth - 1];
            $prn_last_list = $prn_container->child_select_last();
            if ($prn_last_list) {
              $prn_last_list->child_insert($new_container);
              $c_last_item->_pointers[$c_cur_depth] = $new_container;
            }
          }
        # delete old pointers to list containers (ol|ul)
          static::list_process__delete_pointers($c_last_item, $c_cur_depth);
        # insert new list item (li)
          if (!empty($c_last_item->_pointers[$c_cur_depth])) {
            $c_last_item->_pointers[$c_cur_depth]->child_insert(static::markup_list_get());
            static::list_process__insert_data($c_last_item, $c_matches['return'], '_text', $c_cur_depth);
          }
        }
        continue;
      }

    # ─────────────────────────────────────────────────────────────────────
    # blockquote
    # ─────────────────────────────────────────────────────────────────────

      element_blockquote:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{0,})'.
                       '(?<marker>[>][ ]{0,1})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # case: p|blockquote, hr|blockquote, header|blockquote, code|blockquote, markup|blockquote
        if ($c_last_type === 'p'       && $c_indent > 3) {$c_string = static::meta_encode($c_string); goto element_text;}
        if ($c_last_type === 'hr'      && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_header' && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_code'   && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_markup'                 ) {goto element_text;}

      # case: list|blockquote
        if ($c_last_type === '_list' && $c_indent > 1) {
          $c_last_list_element = static::list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if ($c_cur_depth - $c_max_depth < 1                                                                ) {static::list_process__insert_data($c_last_item,                                                                         $c_matches['return'],                       'blockquote', $c_cur_depth - 1); continue;}
          if ($c_cur_depth - $c_max_depth > 0 && $c_cur_depth - $c_max_depth < 3                             ) {static::list_process__insert_data($c_last_item,                                                                         $c_matches['return'],                       'blockquote');                   continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_delimiter') {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    $c_matches['marker'] .$c_matches['return'], '_code'     );                   continue;}
          if ($c_cur_depth - $c_max_depth > 2 && static::node_type_get($c_last_list_element) === '_code'     ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).                    $c_matches['marker'] .$c_matches['return'], '_code'     );                   continue;}
          if ($c_cur_depth - $c_max_depth > 2                                                                ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).static::meta_encode($c_matches['marker']).$c_matches['return'], '_text'     );                   continue;}
        }

      # default:
        if ($c_indent > 3) {goto element_code;}
        if ($c_indent < 4 && $c_last_type === 'blockquote' && trim($c_matches['return']) === '') {$c_last_item->child_select('text')->text_append(nl); continue;}
        if ($c_indent < 4 && $c_last_type === 'blockquote' && trim($c_matches['return']) !== '') {$c_string = $c_matches['return']; goto element_text;}
        if ($c_indent < 4 && $c_last_type !== 'blockquote') {
          $pool->child_insert(static::markup_blockquote_get($c_matches['return']));
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # text
    # ─────────────────────────────────────────────────────────────────────

      element_text:
      if (trim($c_string) !== '') {

      # case: markup|text, blockquote|text, p|text, hr|text, header|text, code|text
        if ($c_last_type === '_markup'                 ) {static::text_process__insert_line($c_last_item, $c_string); continue;}
        if ($c_last_type === 'blockquote'              ) {static::text_process__insert_line($c_last_item, $c_string); continue;}
        if ($c_last_type === 'p'                       ) {static::text_process__insert_line($c_last_item, $c_string); continue;}
        if ($c_last_type === 'hr'      && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_header' && $c_indent > 3) {goto element_code;}
        if ($c_last_type === '_code'   && $c_indent > 3) {goto element_code;}

      # case: list|text
        if ($c_last_type === '_list') {
          $c_last_list_element = static::list_process__select_last_element($c_last_item);
          $c_max_depth = count($c_last_item->_pointers);
          $c_cur_depth = (int)(floor($c_indent - $c_last_item->_indent) / 2) + 1;
          if (                                                      static::node_type_get($c_last_list_element) !== '_delimiter' && static::node_type_get($c_last_list_element) === 'p'    ) {static::list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), 'p'    ); continue;}
          if (                                                      static::node_type_get($c_last_list_element) !== '_delimiter' && static::node_type_get($c_last_list_element) === '_code') {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).ltrim($c_string, ' '), '_code'); continue;}
          if (                                                      static::node_type_get($c_last_list_element) !== '_delimiter' && static::node_type_get($c_last_list_element) !== 'p'    ) {static::list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), '_text'); continue;}
          if ($c_indent > 1 && $c_cur_depth - $c_max_depth  <  2 && static::node_type_get($c_last_list_element) === '_delimiter'                                                           ) {static::list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), 'p', $c_cur_depth - 1); static::list_process__delete_pointers($c_last_item, $c_cur_depth - 1); continue;}
          if ($c_indent > 1 && $c_cur_depth - $c_max_depth === 2 && static::node_type_get($c_last_list_element) === '_delimiter'                                                           ) {static::list_process__insert_data($c_last_item,                                                     ltrim($c_string, ' '), 'p'    ); continue;}
          if ($c_indent > 1 && $c_cur_depth - $c_max_depth  >  2 && static::node_type_get($c_last_list_element) === '_delimiter'                                                           ) {static::list_process__insert_data($c_last_item, str_repeat(' ', $c_indent - 4 - ($c_max_depth * 2)).ltrim($c_string, ' '), '_code'); continue;}
          if ($c_indent < 2                                      && static::node_type_get($c_last_list_element) === '_delimiter'                                                           ) {$pool->child_insert(static::markup_paragraph_get(ltrim($c_string, ' '))); continue;}
        }

      # default:
        if ($c_indent > 3) {goto element_code;}
        if ($c_indent < 4) {
          $pool->child_insert(static::markup_paragraph_get(ltrim($c_string, ' ')));
          continue;
        }

      }

    # ─────────────────────────────────────────────────────────────────────
    # nl
    # ─────────────────────────────────────────────────────────────────────

      element_nl:
      if (trim($c_string) === '') {

      # case: markup|nl, blockquote|nl, p|nl, header|nl, hr|nl, list|nl, code|nl
        if ($c_last_type === '_markup'   ) {$pool->child_insert(static::delimiter_get()); continue;}
        if ($c_last_type === 'blockquote') {$pool->child_insert(static::delimiter_get()); continue;}
        if ($c_last_type === 'p'         ) {$pool->child_insert(static::delimiter_get()); continue;}
        if ($c_last_type === '_header'   ) {$pool->child_insert(static::delimiter_get()); continue;}
        if ($c_last_type === 'hr'        ) {$pool->child_insert(static::delimiter_get()); continue;}
        if ($c_last_type === '_list'     ) {static::list_process__insert_data($c_last_item, '', '_delimiter'); continue;}
        if ($c_last_type === '_code'     ) {static::text_process__insert_line($c_last_item, '', false);        continue;}

      }

    # ─────────────────────────────────────────────────────────────────────
    # code
    # ─────────────────────────────────────────────────────────────────────

      element_code:
      $c_matches = [];
      if (preg_match('%^(?<indent>[ ]{4})'.
                       '(?<spaces>[ ]{0,})'.
                       '(?<return>.{0,})$%S', $c_string, $c_matches)) {

      # case: code|code
        if ($c_last_type === '_code') {
          static::text_process__insert_line($c_last_item, $c_matches['spaces'].$c_matches['return'], false, true);
          continue;
        }

      # default:
        if ($c_last_type !== '_code') {
          $pool->child_insert(static::markup_code_get($c_matches['spaces'].$c_matches['return']));
          continue;
        }

      }

    }

  # ─────────────────────────────────────────────────────────────────────
  # recursive post-process
  # ─────────────────────────────────────────────────────────────────────

  # post-process for blockquotes
    foreach ($pool->children_select_recursive() as $c_item) {
      switch (static::node_type_get($c_item)) {
        case 'blockquote':
          $c_text_object = $c_item->child_select('text');
          if ($c_text_object) {
            $c_text = $c_text_object->text_select();
            $c_text = rtrim(trim($c_text, nl), ' ');
            if ($c_text) {
              static::$blockquote_cur_depth++;
              if (static::$blockquote_cur_depth < static::blockquote_max_depth) {
                $c_item->child_delete('text');
                foreach (static::markdown_to_markup($c_text)->children_select() as $c_new_child) {
                  $c_item->child_insert($c_new_child); }}
              static::$blockquote_cur_depth--;
            }
          }
          break;
      }
    }

  # post-process for references
    foreach ($pool->children_select_recursive() as $c_item) {
      $c_item_type = static::node_type_get($c_item);
      switch ($c_item_type) {
        case '_text':
          if ($c_prev_item_type === 'p'          ||
              $c_prev_item_type === '_header'    ||
              $c_prev_item_type === '_list_item' ||
              $c_prev_item_type === 'blockquote') {
            $text = $c_item->text_select();
            $text = preg_replace_callback('%\\['.'(?<id>[^\\]\\n]{1,127})'.'\\]'.'\\:'.
                            '(?:[ ]{0,64}'.      '(?<url>[^ "\\n]{1,1024})'.   '|)'.
                            '(?:[ ]{0,64}'.'["]'.'(?<title>[^"\\n]{1,512})'.'["]|)%S', function ($c_match) {
              static::$references[core::hash_get(strtolower($c_match['id']))] = (object)[
                'url'   => array_key_exists('url',   $c_match) ? trim($c_match['url'  ]) : '',
                'title' => array_key_exists('title', $c_match) ? trim($c_match['title']) : '',
              ];
              return '';
            }, $text);
            $c_item->text_update($text);
          }
          break;
      }
      $c_prev_item      = $c_item;
      $c_prev_item_type = $c_item_type;
    }

  # post-process for code|text
    foreach ($pool->children_select_recursive() as $c_item) {
      $c_item_type = static::node_type_get($c_item);
      switch ($c_item_type) {
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
        case '_text':
          if ($c_prev_item_type === 'p'          ||
              $c_prev_item_type === '_header'    ||
              $c_prev_item_type === '_list_item' ||
              $c_prev_item_type === 'blockquote') {
            $text = $c_item->text_select();
          # image|link|email
            $text = preg_replace('%\\!\\['.'(?<text>[^\\]\\n]{1,1024}|)'.'\\]'.'\\('.'(?:[ ]{0,64}'.'(?<url>[^ \\)"\\n]{1,1024})'.'|)'.'(?:[ ]{0,64}["]'.'(?<title>[^"\\n]{1,512})'.'["]|)'.'[ ]{0,64}\\)%S', (new markup_simple('img', ['title' => '$3', 'src'  => '$2',  'alt' => '$1']))->render(), $text);
            $text = preg_replace('%'.'\\['.'(?<text>[^\\]\\n]{1,1024}|)'.'\\]'.'\\('.'(?:[ ]{0,64}'.'(?<url>[^ \\)"\\n]{1,1024})'.'|)'.'(?:[ ]{0,64}["]'.'(?<title>[^"\\n]{1,512})'.'["]|)'.'[ ]{0,64}\\)%S', (new markup       ('a',   ['title' => '$3', 'href' => '$2'], new text('$1')))->render(), $text);
            $text = preg_replace_callback('%\\!\\['.'(?<text>[^\\]\\n]{1,1024}|)'.'\\]'.'\\['.'(?<id>[^\\]\\n]{1,127})'.'\\]%S', function ($c_match) {$c_id = core::hash_get(strtolower($c_match['id'  ])); if (isset(static::$references[$c_id])) return (new markup_simple('img', ['title' => static::$references[$c_id]->title, 'src'  => static::$references[$c_id]->url,  'alt' => $c_match['text']]))->render(); else return $c_match[0];}, $text);
            $text = preg_replace_callback('%'.'\\['.'(?<text>[^\\]\\n]{1,1024})'. '\\]'.'\\['.                          '\\]%S', function ($c_match) {$c_id = core::hash_get(strtolower($c_match['text'])); if (isset(static::$references[$c_id])) return (new markup       ('a',   ['title' => static::$references[$c_id]->title, 'href' => static::$references[$c_id]->url], new text($c_match['text'])))->render(); else return $c_match[0];}, $text);
            $text = preg_replace_callback('%'.'\\['.'(?<text>[^\\]\\n]{1,1024}|)'.'\\]'.'\\['.'(?<id>[^\\]\\n]{1,127})'.'\\]%S', function ($c_match) {$c_id = core::hash_get(strtolower($c_match['id'  ])); if (isset(static::$references[$c_id])) return (new markup       ('a',   ['title' => static::$references[$c_id]->title, 'href' => static::$references[$c_id]->url], new text($c_match['text'])))->render(); else return $c_match[0];}, $text);
            $text = preg_replace_callback('%'.'\\<'.'(?<text>[^\\>\\n]{5,512})'.'\\>'.'%S', function ($c_match) {
              if (core::validate_email($c_match['text'])) return (new markup('a', ['href' => 'mailto:'.$c_match['text']], new text($c_match['text'])))->render();
              if (core::validate_url  ($c_match['text'])) return (new markup('a', ['href' =>           $c_match['text']], new text($c_match['text'])))->render();
              return $c_match[0];
            }, $text);
          # strong|em|code
            $text = preg_replace('%'.'([*_])\\1'.'(?<phrase>(?:(?!\\1).){1,2048})'.'\\1\\1'.'%sS', (new markup('strong', [], '$2'))->render(), $text);
            $text = preg_replace('%'.'([*_])'   .'(?<phrase>(?:(?!\\1).){1,2048})'.'\\1'   .'%sS', (new markup('em',     [], '$2'))->render(), $text);
            $text = preg_replace('%'.'(`)'      .'(?<phrase>[^`]'.     '{1,2048})'.'`'     .'%sS', (new markup('code',   [], '$2'))->render(), $text);
            $c_item->text_update($text);
          }
          break;
      }
      $c_prev_item      = $c_item;
      $c_prev_item_type = $c_item_type;
    }

    return $pool;
  }

}}