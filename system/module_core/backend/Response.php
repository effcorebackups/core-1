<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Response {

    static function send_header_and_exit($type, $title = null, $message = null, $p = '') {
        Timer::tap('total');
        if (Module::is_enabled('test')) {
            header('X-PHP-Memory-usage: '.memory_get_usage(true));
            header('X-Time-total: '.Timer::period_get('total', 0, 1));
            header('X-Return-level: system-exit');
        }
        switch ($type) {
            case 'redirect'              : header('Location: '.$p                      );                                                                                                          break;
            case 'page_refresh'          : header('Refresh: ' .$p                      );                                                                                                          break;
            case 'moved_permanently'     : header('HTTP/1.1 301 Moved Permanently'     ); if (!$title) $title = 'Moved Permanently';                                                               break;
            case 'bad_request'           : header('HTTP/1.1 400 Bad Request'           ); if (!$title) $title = 'Bad Request';                                                                     break;
            case 'unsupported_media_type': header('HTTP/1.1 415 Unsupported Media Type'); if (!$title) $title = 'Unsupported Media Type';                                                          break;
            case 'access_forbidden'      : header('HTTP/1.1 403 Forbidden'             ); if (!$title) $title = 'Access forbidden'; $template_name = Template::pick_name('page_access_forbidden'); break;
            case 'page_not_found'        : header('HTTP/1.0 404 Not Found'             ); if (!$title) $title = 'Page not found';   $template_name = Template::pick_name('page_not_found');        break;
            case 'file_not_found'        : header('HTTP/1.0 404 Not Found'             ); if (!$title) $title = 'File not found';   $template_name = Template::pick_name('page_not_found');        break;
        }
        if (!empty($template_name)) {
            if (!$message && Request::path_get() !== '/')
                 $message = 'go to <a href="/">front page</a>';
            $settings = Module::settings_get('page');
            $colors   = Color::get_all();
            $content  = (Template::make_new($template_name, ['attributes' => Core::data_to_attributes([
                'lang'               => Language::code_get_current()]),
                'message'            => is_object($message) && method_exists($message, 'render') ? $message->render() : (new Text($message))->render(),
                'title'              => is_object($title  ) && method_exists($title,   'render') ? $title  ->render() : (new Text($title  ))->render(),
                'color__page'        => isset($colors[$settings->color__page_id       ]) ? $colors[$settings->color__page_id       ]->value_hex : '',
                'color__text'        => isset($colors[$settings->color__text_id       ]) ? $colors[$settings->color__text_id       ]->value_hex : '',
                'color__link'        => isset($colors[$settings->color__link_id       ]) ? $colors[$settings->color__link_id       ]->value_hex : '',
                'color__link_active' => isset($colors[$settings->color__link_active_id]) ? $colors[$settings->color__link_active_id]->value_hex : '',
                'console'            => Console::visible_mode_get() === Console::IS_VISIBLE_FOR_EVERYONE ? (new Markup('pre', [], Console::text_get()))->render() : ''
            ]))->render();
            header('Content-Length: '.strlen($content));
            print $content;
        } else {
            header('Content-Length: 0');
        }
        exit();
    }

}
