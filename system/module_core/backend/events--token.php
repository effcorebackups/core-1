<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\core;
use effcore\request;
use effcore\token;

abstract class events_token {

    static function on_apply($name, $args = []) {
        if ($name === 'request_scheme'        && count($args) === 0                             ) return            request::scheme_get();
        if ($name === 'request_host'          && count($args) === 0                             ) return            request::host_get();
        if ($name === 'request_host_decode'   && count($args) === 0                             ) return            request::host_get(true);
        if ($name === 'request_addr'          && count($args) === 0                             ) return            request::addr_get();
        if ($name === 'request_addr_remote'   && count($args) === 0                             ) return            request::addr_remote_get();
        if ($name === 'request_uri'           && count($args) === 0                             ) return            request::uri_get();
        if ($name === 'request_path'          && count($args) === 0                             ) return            request::path_get();
        if ($name === 'request_query'         && count($args) === 0                             ) return            request::query_get();
        if ($name === 'request_scheme'        && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::scheme_get());
        if ($name === 'request_host'          && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::host_get());
        if ($name === 'request_host_decode'   && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::host_get(true));
        if ($name === 'request_addr'          && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::addr_get());
        if ($name === 'request_addr_remote'   && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::addr_remote_get());
        if ($name === 'request_uri'           && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::uri_get());
        if ($name === 'request_path'          && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::path_get());
        if ($name === 'request_query'         && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(request::query_get());
        switch ($name) {
            case 'return_if_token':
                if (count($args) > 2) {
                    if (strpos($args[0], '%%') === false) {
                        if (strpos($args[0], 'return_if') === 0) return '!!! "return_if…" inside "return_if_token" is meaningless !!!';
                        if (count($args) === 3) return token::apply('%%_'.$args[0]) === $args[1] ? $args[2] : '';
                        if (count($args) === 4) return token::apply('%%_'.$args[0]) === $args[1] ? $args[2] : $args[3];
                    }
                }
                break;
            case 'return_if_url_arg':
                if (count($args) === 3) return request::value_get($args[0], 0, '_GET') === $args[1] ? $args[2] : '';
                if (count($args) === 4) return request::value_get($args[0], 0, '_GET') === $args[1] ? $args[2] : $args[3];
                break;
            case 'return_url_arg':
                if (count($args) === 3) {
                    $color_arg_name = $args[0];
                    $color_default  = $args[1];
                    $filter         = $args[2];
                    if ($filter === 'css_units') {
                        $color_arg = core::sanitize_css_units(request::value_get($color_arg_name, 0, '_GET'));
                        return $color_arg ?: $color_default;
                    }
                }
                break;
        }
    }

}
