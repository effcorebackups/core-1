<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Core;
use effcore\Request;
use effcore\Token;

abstract class Events_Token {

    static function on_apply($name, $args = []) {
        if ($name === 'request_scheme'        && count($args) === 0                             ) return            Request::scheme_get();
        if ($name === 'request_host'          && count($args) === 0                             ) return            Request::host_get();
        if ($name === 'request_host_decode'   && count($args) === 0                             ) return            Request::host_get(true);
        if ($name === 'request_addr'          && count($args) === 0                             ) return            Request::addr_get();
        if ($name === 'request_addr_remote'   && count($args) === 0                             ) return            Request::addr_remote_get();
        if ($name === 'request_uri'           && count($args) === 0                             ) return            Request::uri_get();
        if ($name === 'request_path'          && count($args) === 0                             ) return            Request::path_get();
        if ($name === 'request_query'         && count($args) === 0                             ) return            Request::query_get();
        if ($name === 'request_scheme'        && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::scheme_get());
        if ($name === 'request_host'          && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::host_get());
        if ($name === 'request_host_decode'   && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::host_get(true));
        if ($name === 'request_addr'          && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::addr_get());
        if ($name === 'request_addr_remote'   && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::addr_remote_get());
        if ($name === 'request_uri'           && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::uri_get());
        if ($name === 'request_path'          && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::path_get());
        if ($name === 'request_query'         && count($args) === 1 && $args[0] === 'preg_quote') return preg_quote(Request::query_get());
        switch ($name) {
            case 'return_if_token':
                if (count($args) > 2) {
                    if (strpos($args[0], '%%') === false) {
                        if (strpos($args[0], 'return_if') === 0) return '!!! "return_if…" inside "return_if_token" is meaningless !!!';
                        if (count($args) === 3) return Token::apply('%%_'.$args[0]) === $args[1] ? $args[2] : '';
                        if (count($args) === 4) return Token::apply('%%_'.$args[0]) === $args[1] ? $args[2] : $args[3];
                    }
                }
                break;
            case 'return_if_url_arg':
                if (count($args) === 3) return Request::value_get($args[0], 0, '_GET') === $args[1] ? $args[2] : '';
                if (count($args) === 4) return Request::value_get($args[0], 0, '_GET') === $args[1] ? $args[2] : $args[3];
                break;
            case 'return_url_arg':
                if (count($args) === 3) {
                    $color_arg_name = $args[0];
                    $color_default  = $args[1];
                    $filter         = $args[2];
                    if ($filter === 'css_units') {
                        $color_arg = Core::sanitize_css_units(Request::value_get($color_arg_name, 0, '_GET'));
                        return $color_arg ?: $color_default;
                    }
                }
                break;
        }
    }

}
