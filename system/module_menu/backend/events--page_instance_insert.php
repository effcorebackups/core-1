<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\menu {
          use const \effcore\br;
          use \effcore\page;
          use \effcore\response;
          use \effcore\text_multiline;
          use \effcore\tree;
          abstract class events_page_instance_insert {

  static function on_check_existence($event, $page) {
    $entity_name = page::get_current()->args_get('entity_name');
    $category_id = page::get_current()->args_get('category_id');
    if ($entity_name === 'tree_item') {
      $trees = tree::select_all('sql');
      if (!$category_id || empty($trees[$category_id])) {
        response::send_header_and_exit('page_not_found', null, new text_multiline(['wrong category', 'go to <a href="/">front page</a>'], [], br.br));
      }
    }
  }

}}