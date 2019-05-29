<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\develop {
          use \effcore\block;
          use \effcore\decorator;
          use \effcore\template;
          use \effcore\text_simple;
          abstract class events_page_templates {

  static function on_show_block_templates($page) {
    $decorator = new decorator('table');
    $decorator->id = 'templates_registered';
    $templates = template::get_all();
    ksort($templates);
    foreach ($templates as $c_template) {
      $decorator->data[] = [
        'name'      => ['value' => new text_simple($c_template->name     ), 'title' => 'Name'     ],
        'type'      => ['value' => new text_simple($c_template->type     ), 'title' => 'Type'     ],
        'module_id' => ['value' => new text_simple($c_template->module_id), 'title' => 'Module ID'],
      ];
    }
    return new block('', ['data-id' => 'templates_registered'], [
      $decorator
    ]);
  }

}}
