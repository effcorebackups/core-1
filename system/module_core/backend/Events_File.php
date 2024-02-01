<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use const effcore\NL;
use effcore\Console;
use effcore\File;
use effcore\Module;
use effcore\Request;
use effcore\Response;
use effcore\Security;
use effcore\Timer;
use effcore\Token;
use Exception;

abstract class Events_File {

    static function on_load_not_found($event, &$type_info, &$file, $real_path, $phase) {
        Response::send_header_and_exit('file_not_found');
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_load_dynamic($event, &$type_info, &$file) {
        $data = Token::apply($file->load());
        $etag = Security::hash_get($data);

        # send header '304 Not Modified' if the data has no changes
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) &&
                  $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
            header('http/1.1 304 not modified');
            exit();
        }

        # send result data
        $result = $data;
        Timer::tap('total');
        if (Module::is_enabled('test')) {
            header('x-web-server-name: '.Request::web_server_get_info()->name);
            header('x-time-total: '.Timer::period_get('total', 0, 1));
            header('x-php-memory-usage: '.memory_get_usage(true));
        }
        if ($file->type === 'cssd' ||
            $file->type === 'jsd') {
            if (Console::visible_mode_get() === Console::IS_VISIBLE_FOR_EVERYONE) {
                $result.= NL.'/*'.NL.Console::text_get().NL.'*/'.NL;
            }
        }
        header('content-length: '.strlen($result));
        header('cache-control: private, no-cache');
        header('accept-ranges: none');
        header('etag: '.$etag);
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

    static function on_load_static($event, &$type_info, &$file) {

        $last_modified = gmdate('D, d M Y H:i:s', filemtime($file->path_get())).' GMT';

        ##################################################################
        ### send header '304 Not Modified' if the data has not changed ###
        ##################################################################

        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
                  $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $last_modified) {
            header('http/1.1 304 not modified');
            exit();
        }

        ####################
        ### send headers ###
        ####################

        header('accept-ranges: bytes');
        header('cache-control: private, no-cache');
        header('last-modified: '.$last_modified);

        if (!empty($type_info->headers)) {
            foreach ($type_info->headers as $c_key => $c_value) {
                header($c_key.': '.$c_value);
            }
        }

        ############################
        ### if the file is empty ###
        ############################

        $length = $file->size_get();

        if ($length === 0) {
            header('content-length: 0');
            exit();
        }

        ##################################
        ### if no ranges are specified ###
        ##################################

        $ranges = Request::http_range_get();

        if ($ranges->has_range !== true) {
            header('content-length: '.$length);
            try {
                $handle = @fopen($file->path_get(), 'rb');
                if ($handle) {
                    fseek($handle, 0, SEEK_SET);
                    while ($c_data = fread($handle, File::READ_BLOCK_SIZE))
                        print $c_data;
                    fclose($handle);
                } else             { header('content-length: 0'); Response::send_header_and_exit('no_content'); }
            } catch (Exception $e) { header('content-length: 0'); Response::send_header_and_exit('no_content'); }
            exit();
        }

        ###############################
        ### if ranges are specified ###
        ###############################

        if ($ranges->has_range === true) {
            $min = $ranges->min;
            $max = $ranges->max;

            if ($min === null) {
                header('http/1.1 416 requested range not satisfiable');
                exit();
            }

            if ($max === null || $max >= $length) {
                $max = $length - 1;
            }

            if (!(0 <= $min &&
                       $min <= $max &&
                               $max < $length)) {
                header('http/1.1 416 requested range not satisfiable');
                exit();
            }

            header('http/1.1 206 partial content');
            header('content-range: bytes '.$min.'-'.$max.'/'.$length);
            header('content-length: '.($max + 1 - $min));

            try {
                $handle = @fopen($file->path_get(), 'rb');
                if ($handle) {
                    fseek($handle, $min, SEEK_SET);
                    $cur = $min;
                    while ($c_data = fread($handle, File::READ_BLOCK_SIZE)) {
                        $cur += strlen($c_data);
                        if ($cur  <  $max + 1) {print        $c_data;                            }
                        if ($cur === $max + 1) {print        $c_data;                      break;}
                        if ($cur  >  $max + 1) {print substr($c_data, 0, $max + 1 - $cur); break;}
                    }
                    fclose($handle);
                } else             { header('content-length: 0'); Response::send_header_and_exit('no_content'); }
            } catch (Exception $e) { header('content-length: 0'); Response::send_header_and_exit('no_content'); }

            exit();
        }

    }

}
