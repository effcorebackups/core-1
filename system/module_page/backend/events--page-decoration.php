<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\color;
          use \effcore\core;
          use \effcore\page;
          use \effcore\tabs_item;
          use \effcore\url;
          abstract class events_page_decoration {

  static function on_tab_build_before($event, $tab) {
    $presets = color::preset_get_all();
    $type = page::get_current()->args_get('type');
    $id   = page::get_current()->args_get('id');
    core::array_sort_by_title($presets);
    if ($type == null)      url::go(page::get_current()->args_get('base').'/colors');
    if ($type == 'presets') url::go(page::get_current()->args_get('base').'/presets/'.reset($presets)->id);
    if (strpos($type, 'presets/') === 0 && !isset($presets[$id])) {
      url::go(page::get_current()->args_get('base').'/presets/'.reset($presets)->id);
    }
    foreach ($presets as $c_preset) {
      tabs_item::insert(      $c_preset->title,
        'decoration_presets_'.$c_preset->id,
        'decoration_presets', 'decoration', 'presets/'.$c_preset->id
      );
    }
  }

}}