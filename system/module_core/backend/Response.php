<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Response {

    const EXIT_STATE_OK      = 0b00;
    const EXIT_STATE_WARNING = 0b01;
    const EXIT_STATE_ERROR   = 0b10;

    const FORMAT_JSON      = 'json';
    const FORMAT_JSONP     = 'jsonp';
    const FORMAT_SERIALIZE = 'serialize';
    const FORMAT_DATA      = 'data';

    static function send_header_and_exit($type, $title = null, $message = null, $p = '') {
        Timer::tap('total');
        if (Module::is_enabled('test')) {
            header('x-web-server-name: '.Request::web_server_get_info()->name);
            header('x-time-total: '.Timer::period_get('total', 0, 1));
            header('x-php-memory-usage: '.memory_get_usage(true));
            header('x-return-level: system-exit');
        }
        switch ($type) {
            case 'redirect'              : header('location: '.$p                      );                                                                                                               break;
            case 'page_refresh'          : header('refresh: ' .$p                      );                                                                                                               break;
            case 'moved_permanently'     : header('http/1.1 301 moved permanently'     ); if (!$title) $title = 'Moved Permanently';                                                                    break;
            case 'bad_request'           : header('http/1.1 400 bad request'           ); if (!$title) $title = 'Bad Request';                                                                          break;
            case 'unsupported_media_type': header('http/1.1 415 unsupported media type'); if (!$title) $title = 'Unsupported Media Type';                                                               break;
            case 'page_access_forbidden' : header('http/1.1 403 forbidden'             ); if (!$title) $title = 'Page access forbidden'; $template_name = Template::pick_name('page_access_forbidden'); break;
            case 'file_access_forbidden' : header('http/1.1 403 forbidden'             ); if (!$title) $title = 'File access forbidden'; $template_name = Template::pick_name('file_access_forbidden'); break;
            case 'page_not_found'        : header('http/1.0 404 not found'             ); if (!$title) $title = 'Page not found';        $template_name = Template::pick_name('page_not_found');        break;
            case 'file_not_found'        : header('http/1.0 404 not found'             ); if (!$title) $title = 'File not found';        $template_name = Template::pick_name('file_not_found');        break;
            case 'no_content'            : header('http/1.0 204 no content'            ); if (!$title) $title = 'No Content';            $template_name = Template::pick_name('no_content');            break;
            case 'internal_server_error' : header('http/1.0 500 internal server error' ); if (!$title) $title = 'Internal Server Error'; $template_name = Template::pick_name('internal_server_error'); break;
        }
        if (!empty($template_name)) {
            if (!$message && Request::path_get() !== '/')
                 $message = 'go to <a href="/">front page</a>';
            $colors                = Color::get_all();
            $color_id__page        = Color_profile::get_color_info( Color_profile::get_current()->id, 'page'       )->color_id ?? '';
            $color_id__text        = Color_profile::get_color_info( Color_profile::get_current()->id, 'text'       )->color_id ?? '';
            $color_id__link        = Color_profile::get_color_info( Color_profile::get_current()->id, 'link'       )->color_id ?? '';
            $color_id__link_active = Color_profile::get_color_info( Color_profile::get_current()->id, 'link_active')->color_id ?? '';
            $content = (Template::make_new($template_name, [
                'attributes'        => Template_markup::attributes_render(['lang' => Language::code_get_current()]),
                'message'           => is_object($message) && method_exists($message, 'render') ? $message->render() : (new Text($message))->render(),
                'title'             => is_object($title  ) && method_exists($title  , 'render') ? $title  ->render() : (new Text($title  ))->render(),
                'color_page'        => isset($colors[$color_id__page       ]) ? $colors[$color_id__page       ]->value_hex : 'white',
                'color_text'        => isset($colors[$color_id__text       ]) ? $colors[$color_id__text       ]->value_hex : 'black',
                'color_link'        => isset($colors[$color_id__link       ]) ? $colors[$color_id__link       ]->value_hex : 'steelblue',
                'color_link_active' => isset($colors[$color_id__link_active]) ? $colors[$color_id__link_active]->value_hex : 'mediumvioletred',
                'console'           => Console::visible_mode_get() === Console::IS_VISIBLE_FOR_EVERYONE ? (new Markup('pre', [], Console::text_get()))->render() : ''
            ]))->render();
            header('content-length: '.strlen($content));
            print $content;
        } else {
            header('content-length: 0');
        }
        exit();
    }

    static function send_and_exit($data, $state = self::EXIT_STATE_OK, $format = self::FORMAT_JSON) {
        switch ($format) {
            case static::FORMAT_JSON:
                header('content-type: application/json');
                if ($state === static::EXIT_STATE_OK     ) print json_encode(['status' => 'ok',      'data' => $data]);
                if ($state === static::EXIT_STATE_WARNING) print json_encode(['status' => 'warning', 'data' => $data]);
                if ($state === static::EXIT_STATE_ERROR  ) print json_encode(['status' => 'error',   'data' => $data]);
                exit();
            case static::FORMAT_JSONP:
                header('content-type: application/javascript');
                if ($state === static::EXIT_STATE_OK     ) print 'export default '.json_encode(['status' => 'ok',      'data' => $data]);
                if ($state === static::EXIT_STATE_WARNING) print 'export default '.json_encode(['status' => 'warning', 'data' => $data]);
                if ($state === static::EXIT_STATE_ERROR  ) print 'export default '.json_encode(['status' => 'error',   'data' => $data]);
                exit();
            case static::FORMAT_SERIALIZE:
                header('content-type: text/plain');
                if ($state === static::EXIT_STATE_OK     ) print serialize(['status' => 'ok',      'data' => $data]);
                if ($state === static::EXIT_STATE_WARNING) print serialize(['status' => 'warning', 'data' => $data]);
                if ($state === static::EXIT_STATE_ERROR  ) print serialize(['status' => 'error',   'data' => $data]);
                exit();
            case static::FORMAT_DATA:
                header('content-type: text/plain');
                if ($state === static::EXIT_STATE_OK     ) print Storage_Data::data_to_text(['status' => 'ok',      'data' => $data], 'root');
                if ($state === static::EXIT_STATE_WARNING) print Storage_Data::data_to_text(['status' => 'warning', 'data' => $data], 'root');
                if ($state === static::EXIT_STATE_ERROR  ) print Storage_Data::data_to_text(['status' => 'error',   'data' => $data], 'root');
                exit();
            default:
                print 'UNKNOWN FORMAT';
                exit();
        }
    }

}
