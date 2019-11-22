<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\access;
          use \effcore\markup;
          use \effcore\text;
          use \effcore\url;
          use \effcore\user;
          abstract class events_page {

  static function block_title($page) {
    return new markup('h1', ['id' => 'title'],
      new text($page->title, [], true, true)
    );
  }

  static function block_page_actions($page) {
    if ($page->origin == 'sql' && access::check((object)['roles' => ['admins' => 'admins']])) {
      return new markup('x-page-actions', [],
        new markup('a', ['data-id' => 'update', 'href' => '/manage/data/content/page/'.$page->id.'/update?'.url::back_part_make()], 'update this page')
      );
    }
  }

}}