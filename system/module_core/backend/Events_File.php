<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use const effcore\NL;
use effcore\Console;
use effcore\Core;
use effcore\Module;
use effcore\Request;
use effcore\Response;
use effcore\Timer;
use effcore\Token;

abstract class Events_File {

    static function on_load_not_found($event, &$type_info, &$file, $real_path, $phase) {
        Response::send_header_and_exit('file_not_found');
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_load_dynamic($event, &$type_info, &$file) {
        $data = Token::apply($file->load());
        $etag = Core::hash_get($data);

        # send header '304 Not Modified' if the data has no changes
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
                  $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }

        # send result data
        $result = $data;
        Timer::tap('total');
        if (Module::is_enabled('test')) {
            header('X-PHP-Memory-usage: '.memory_get_usage(true));
            header('X-Time-total: '.Timer::period_get('total', 0, 1));
        }
        if ($file->type === 'cssd' ||
            $file->type === 'jsd') {
            if (Console::visible_mode_get() === Console::IS_VISIBLE_FOR_EVERYONE) {
                $result.= NL.'/*'.NL.Console::text_get().NL.'*/'.NL;
            }
        }
        header('Content-Length: '.strlen($result));
        header('Cache-Control: private, no-cache');
        header('Accept-Ranges: none');
        header('Etag: '.$etag);
        if (!empty($type_info->headers)) {
            foreach ($type_info->headers as $c_key => $c_value) {
                header($c_key.': '.$c_value);
            }
        }
        print $result;
        exit();
    }

    # ┌────────────────────────────────────────╥─────────┐
    # │ headers                                ║ support │
    # ╞════════════════════════════════════════╬═════════╡
    # │ Range: bytes=int-                      ║    +    │
    # │ Range: bytes=int-int                   ║    +    │
    # │ Range: bytes=int-int, int-int          ║    -    │
    # │ Range: bytes=int-int, int-int, int-int ║    -    │
    # │ Range: bytes=-<-length>                ║    -    │
    # └────────────────────────────────────────╨─────────┘

    # ─────────────────────────────────────────────────────────────────────
    # http ranges limits:
    # ═════════════════════════════════════════════════════════════════════
    #
    #    ┌┬┬┬┬┬┬┬┬┐
    #    ┝┷┷┷┷┷┷┷┷┿━━━━━━━━━━━━━━━━━━━━━┥
    #   0│min     │max                  │length
    #
    #
    #               ┌┬┬┬┬┬┬┬┬┐
    #    ┝━━━━━━━━━━┿┷┷┷┷┷┷┷┷┿━━━━━━━━━━┥
    #   0│       min│        │max       │length
    #
    #
    #                         ┌┬┬┬┬┬┬┬┬┐
    #    ┝━━━━━━━━━━━━━━━━━━━━┿┷┷┷┷┷┷┷┷┿┥
    #   0│                 min│     max││length
    #
    #
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    #
    #    0 ≤ min ≤ max < length
    #
    # ─────────────────────────────────────────────────────────────────────

    const READ_BLOCK_SIZE = 1024;

    static function on_load_static($event, &$type_info, &$file) {

        $last_modified = gmdate('D, d M Y H:i:s', filemtime($file->path_get())).' GMT';

        # send header '304 Not Modified' if the data has not changed
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
                  $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $last_modified) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }

        # send headers
        header('Accept-Ranges: bytes');
        header('Cache-Control: private, no-cache');
        header('Last-Modified: '.$last_modified);
        if (!empty($type_info->headers)) {
            foreach ($type_info->headers as $c_key => $c_value) {
                header($c_key.': '.$c_value);
            }
        }

        # if the file is empty
        $length = filesize($file->path_get());
        if ($length === 0) {
            header('Content-Length: 0');
            exit();
        }

        # if no ranges are specified
        $ranges = Request::http_range_get();
        if ($ranges->has_range !== true) {
            header('Content-Length: '.$length);
            if ($handle = fopen($file->path_get(), 'rb')) {
                fseek($handle, 0, SEEK_SET);
                fpassthru($handle);
                fclose($handle);
            }
            exit();
        }

        # if ranges are specified
        if ($ranges->has_range === true) {
            $min = $ranges->min;
            $max = $ranges->max;
            if ($min === null) {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
            if ($max === null || $max >= $length) $max = $length - 1;
            if (!(0 <= $min &&
                       $min <= $max &&
                               $max < $length)) {header('HTTP/1.1 416 Requested Range Not Satisfiable'); exit();}
            header('HTTP/1.1 206 Partial Content');
            header('Content-Range: bytes '.$min.'-'.$max.'/'.$length);
            header('Content-Length: '.($max + 1 - $min));
            $cur = $min;
            if ($handle = fopen($file->path_get(), 'rb')) {
                fseek($handle, $min, SEEK_SET);
                while (strlen($c_data = fread($handle, static::READ_BLOCK_SIZE))) {
                    $cur += strlen($c_data);
                    if ($cur  <  $max + 1) {print        $c_data;                            }
                    if ($cur === $max + 1) {print        $c_data;                      break;}
                    if ($cur  >  $max + 1) {print substr($c_data, 0, $max + 1 - $cur); break;}
                }
                fclose($handle);
            }
            exit();
        }

    }

}
