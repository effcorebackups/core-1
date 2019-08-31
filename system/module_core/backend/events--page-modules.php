<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\url;
          abstract class events_page_modules {

  static function on_build_before($event, $page) {
    $action = $page->args_get('action');
    if ($action == null) {
      url::go($page->args_get('base').'/install');
    }
  }

}}