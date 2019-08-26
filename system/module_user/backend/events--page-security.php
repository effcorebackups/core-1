<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\block;
          use \effcore\text;
          abstract class events_page_security {

  static function on_show_block_security($page) {
    return new block('', ['data-id' => 'security'], [
      new text('UNDER CONSTRUCTION')
    ]);
  }

}}