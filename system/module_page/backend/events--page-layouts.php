<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\core;
          use \effcore\layout;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\url;
          abstract class events_page_layouts {

  static function on_tab_build_before($event, $tab) {
    $layouts = layout::select_all();
    $id = page::get_current()->args_get('id');
    core::array_sort_by_text_property($layouts);
    if (!isset($layouts[$id])) url::go(page::get_current()->args_get('base').'/'.reset($layouts)->id);
    foreach ($layouts as $c_layout) {
      tabs_item::insert($c_layout->title,
         'layouts_all_'.$c_layout->id,
         'layouts_all', 'layouts', $c_layout->id
      );
    }
  }

}}