<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Color_preset;
use effcore\Core;
use effcore\Layout;
use effcore\Page;
use effcore\Tab_item;
use effcore\Url;

abstract class Events_Page_View {

    static function on_redirect($event, $page) {
        $type = $page->args_get('type');
        $id   = $page->args_get('id');
        if ($type === null            ) {                                                                                                    Url::go($page->args_get('base').'/colors/current'                     );}
        if ($type === 'colors'        ) {                                                                                                    Url::go($page->args_get('base').'/colors/current'                     );}
        if ($type === 'colors/presets') {$presets = Color_preset::get_all(); Core::array_sort_by_string($presets); if (empty($presets[$id])) Url::go($page->args_get('base').'/colors/presets/'.reset($presets)->id);}
        if ($type === 'layouts'       ) {$layouts = Layout::select_all   (); Core::array_sort_by_string($layouts); if (empty($layouts[$id])) Url::go($page->args_get('base').'/layouts/'       .reset($layouts)->id);}
    }

    static function on_tab_build_before($event, $tab) {
        $type = Page::get_current()->args_get('type');
        if ($type === 'colors/presets') {
            $presets = Color_preset::get_all();
            Core::array_sort_by_string($presets);
            foreach ($presets as $c_preset) {
                Tab_item::insert(                                    $c_preset->title,
                    'view_colors_presets_'                          .$c_preset->id,
                    'view_colors_presets', 'view', 'colors/presets/'.$c_preset->id, null, [], [], false, 0, 'page'
                );
            }
        }
        if ($type === 'layouts') {
            $layouts = Layout::select_all();
            Core::array_sort_by_string($layouts);
            foreach ($layouts as $c_layout) {
                Tab_item::insert(                      $c_layout->title,
                    'view_layouts_'                   .$c_layout->id,
                    'view_layouts', 'view', 'layouts/'.$c_layout->id, null, [], [], false, 0, 'page'
                );
            }
        }
    }

}
