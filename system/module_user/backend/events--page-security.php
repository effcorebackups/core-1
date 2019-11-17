<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\text;
          abstract class events_page_security {

  static function block_security($page) {
    return new block('', ['data-id' => 'security'], [
      new text('UNDER CONSTRUCTION')
    ]);
  }

}}