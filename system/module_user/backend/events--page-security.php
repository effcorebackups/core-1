<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\text;
          use \effcore\url;
          abstract class events_page_security {

  static function on_redirect($event, $page) {
    $type = $page->args_get('type');
    if ($type === null) {url::go($page->args_get('base').'/settings');}
  }

  static function block_security($page) {
    return new block('Security', ['data-id' => 'security', 'data-title-is-hidden' => true], [
      new text('UNDER CONSTRUCTION')
    ]);
  }

}}