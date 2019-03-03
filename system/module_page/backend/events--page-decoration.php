<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\url;
          abstract class events_page_decoration {

  static function on_page_init($page) {
    $type = $page->args_get('type');
    if ($type == null) {
      url::go($page->args_get('base').'/colors');
    }
  }

}}