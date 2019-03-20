<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\storage {
          use \effcore\page;
          abstract class events_token {

  static function on_replace($name, $args = []) {
    switch ($name) {
      case 'entity_name_page_context':
        return page::current_get()->args_get($args[0]);
    }
    return '';
  }

}}