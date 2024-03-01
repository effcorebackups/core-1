<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use const effcore\NL;
use effcore\Color_profile;
use effcore\Color;
use effcore\Core;
use effcore\Layout;
use effcore\Module;
use effcore\Page;
use effcore\Response;
use effcore\Security;
use effcore\Storage_Data;
use effcore\Tab_item;
use effcore\Text_multiline;
use effcore\URL;

abstract class Events_Page_View {

    static function on_redirect($event, $page) {
        $type = $page->args_get('type');
        if ($type === null    ) URL::go($page->args_get('base').'/colors/manage');
        if ($type === 'colors') URL::go($page->args_get('base').'/colors/manage');
        if ($type === 'colors/profiles' || str_starts_with($type, 'colors/profiles/')) {
            $profiles = Color_profile::get_all();
            Core::array_sort_by_number($profiles);
            $profile_id = null;
            if (preg_match('%^colors/profiles/[a-z0-9_]+/export/colors$%', $type)) $profile_id = $page->args_get('profile_color_export_id');
            if (preg_match('%^colors/profiles/[a-z0-9_]+/export$%',        $type)) $profile_id = $page->args_get('profile_export_id');
            if (preg_match('%^colors/profiles/[a-z0-9_]+$%',               $type)) $profile_id = $page->args_get('profile_id');
            if (!$profile_id || empty($profiles[$profile_id])) {
                URL::go($page->args_get('base').'/colors/profiles/'.reset($profiles)->id);
            }
        }
        if ($type === 'layouts' || str_starts_with($type, 'layouts/')) {
            $layout_id = $page->args_get('layout_id');
            $layouts   = Layout::select_all();
            Core::array_sort_by_string($layouts);
            if (empty($layouts[$layout_id])) {
                URL::go($page->args_get('base').'/layouts/'.reset($layouts)->id);
            }
        }
    }

    static function on_tab_build_before($event, $tab) {
        $base = Page::get_current()->args_get('base');
        $type = Page::get_current()->args_get('type');
        if ($base === '/manage/view' && str_starts_with($type, 'colors/')) {
            $profiles = Color_profile::get_all();
            Core::array_sort_by_string($profiles);
            foreach ($profiles as $c_profile) {
                $c_profile_title = (new Text_multiline([
                    $c_profile->title, ' ('.
                    $c_profile->module_id.')'
                ], [], ''))->render();
                Tab_item::insert(                                      $c_profile_title,
                    'view_colors_profiles_'                           .$c_profile->id,
                    'view_colors_profiles', 'view', 'colors/profiles/'.$c_profile->id, null, [], [], false, $c_profile->weight, 'page'
                );
            }
        }
        if (str_starts_with($type, 'layouts/')) {
            $layouts = Layout::select_all();
            Core::array_sort_by_string($layouts);
            foreach ($layouts as $c_layout) {
                $c_layout_title = (new Text_multiline([
                    $c_layout->title, ' ('.
                    $c_layout->module_id.')'
                ], [], ''))->render();
                Tab_item::insert(                      $c_layout_title,
                    'view_layouts_'                   .$c_layout->id,
                    'view_layouts', 'view', 'layouts/'.$c_layout->id, null, [], [], false, 0, 'page'
                );
            }
        }
    }

    static function export_custom_colors($page, $args = []) {
        $colors = Core::deep_clone(Color::get_all('nosql-dynamic'));
        if (count($colors)) {
            foreach ($colors as $c_id => $c_color)
                foreach ($c_color as $c_property => $c_value)
                    if (!($c_property === 'id'        ||
                          $c_property === 'value_hex' ||
                          $c_property === 'group')) unset($colors[$c_id]->{$c_property});
            header('content-type: application/octet-stream');
            header('content-disposition: attachment; filename=colors.data');
            header('cache-control: private, no-cache, no-store, must-revalidate');
            header('expires: 0');
            print '############################################################################################################'.NL;
            print '### put this file to the "/data" directory of your profile, which is located in the "/modules" directory ###'.NL;
            print '### example: "/modules/examples/profile_classic/data/colors.data"                                        ###'.NL;
            print '############################################################################################################'.NL;
            print Storage_Data::data_to_text($colors, 'colors');
            exit();
        } else {
            Response::send_header_and_exit('file_not_found');
        }
    }

    static function export_profile_custom_colors($page, $args = []) {
        $profile_id = $page->args_get('profile_color_export_id');
        if ($profile_id) {
            $profile = Color_profile::get($profile_id);
            if ($profile) {
                $colors_statistics = Color_profile::get_colors_statistics(Color_profile::STATISTICS_MODE_PROFILE_BY_COLOR)[$profile_id];
                $colors_custom = Core::deep_clone(Color::get_all('nosql-dynamic'));
                $colors = array_intersect_key($colors_custom, $colors_statistics);
                if (count($colors)) {
                    foreach ($colors as $c_id => $c_color)
                        foreach ($c_color as $c_property => $c_value)
                            if (!($c_property === 'id'        ||
                                  $c_property === 'value_hex' ||
                                  $c_property === 'group')) unset($colors[$c_id]->{$c_property});
                    header('content-type: application/octet-stream');
                    header('content-disposition: attachment; filename=colors-for-'.$profile_id.'.data');
                    header('cache-control: private, no-cache, no-store, must-revalidate');
                    header('expires: 0');
                    print '############################################################################################################'.NL;
                    print '### put this file to the "/data" directory of your profile, which is located in the "/modules" directory ###'.NL;
                    print '### example: "/modules/examples/profile_classic/data/colors.data"                                        ###'.NL;
                    print '############################################################################################################'.NL;
                    print Storage_Data::data_to_text($colors, 'colors');
                    exit();
                } else {
                    Response::send_header_and_exit('file_not_found');
                }
            }
        }
    }

    static function export_profile($page, $args = []) {
        $profile_id = $page->args_get('profile_export_id');
        if ($profile_id) {
            $profile = Color_profile::get($profile_id);
            if ($profile) {
                $profile = Core::deep_clone($profile);
                if (Module::is_in_system_path($profile->module_id) === true) {
                    $name_suffix = Security::hash_get_mini(serialize($profile));
                    $profile->id   .=  '_variation_' .$name_suffix;
                    $profile->title.= ' (variation: '.$name_suffix.')'; }
                foreach ($profile as $c_property => $c_value)
                    if (!($c_property === 'id'                 ||
                          $c_property === 'title'              ||
                          $c_property === 'is_dark'            ||
                          $c_property === 'is_user_selectable' ||
                          $c_property === 'groups')) unset($profile->{$c_property});
                header('content-type: application/octet-stream');
                header('content-disposition: attachment; filename=color_profiles-'.$profile->id.'.data');
                header('cache-control: private, no-cache, no-store, must-revalidate');
                header('expires: 0');
                print '############################################################################################################'.NL;
                print '### put this file to the "/data" directory of your profile, which is located in the "/modules" directory ###'.NL;
                print '### example: "/modules/examples/profile_classic/data/color_profiles.data"                                ###'.NL;
                print '############################################################################################################'.NL;
                print Storage_Data::data_to_text([$profile_id => $profile], 'color_profiles');
                exit();
            } else Response::send_header_and_exit('file_not_found');
        }     else Response::send_header_and_exit('file_not_found');
    }

}
