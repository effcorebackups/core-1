<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use const \effcore\br;
          use \effcore\block;
          use \effcore\decorator;
          use \effcore\file;
          use \effcore\text_simple;
          abstract class events_page_file_types {

  static function on_show_block_file_types($page) {
    $decorator = new decorator('table');
    $decorator->id = 'file_types_registered';
    $file_types = file::types_get();
    ksort($file_types);
    foreach ($file_types as $c_type) {
      $decorator->data[] = [
        'type'      => ['value' => new text_simple(      $c_type->type                                         ), 'title' => 'Type'     ],
        'kind'      => ['value' => new text_simple(      $c_type->kind ?? ''                                   ), 'title' => 'Kind'     ],
        'module_id' => ['value' => new text_simple(      $c_type->module_id                                    ), 'title' => 'Module ID'],
        'headers'   => ['value' => new text_simple(isset($c_type->headers) ? implode(br, $c_type->headers) : ''), 'title' => 'Headers'  ]
      ];
    }
    return new block('', ['data-id' => 'file_types_registered'], [
      $decorator
    ]);
  }

}}
