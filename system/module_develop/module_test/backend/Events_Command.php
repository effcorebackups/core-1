<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\NL;
use effcore\Core;
use effcore\Request;
use effcore\Security;
use effcore\Test;
use effcore\Text_multiline;
use effcore\Timer;

abstract class Events_Command {

    static function test($args = []) {
        if (empty($args)) {
            static::_show_error('TEST NAME IS REQUIRED!');
            exit();
        }
        if (!Test::get($args[0], false)) {
            static::_show_error('INVALID TEST NAME!');
            exit();
        }
        static::_environment_prepare($args);
        $test = Test::get($args[0]);
        if ($test) {
            Timer::tap('test_total');
            $test_result = $test->run();
            Timer::tap('test_total');
            $timer_value = Core::format_number(
                Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN
            );
            # make report
            print 'TEST: '.$test->id.NL;
            if (!empty($test_result['reports'])) {
                foreach ($test_result['reports'] as $c_dpath => $c_part) {
                    $c_depth = Core::path_get_depth($c_dpath);
                    if (is_array($c_part))
                        foreach ($c_part as $c_key => $c_line) {
                            if ($c_line instanceof Text_multiline)
                                $c_line->delimiter = NL;
                            $c_part[$c_key] = Core::to_rendered($c_line);
                        }
                    if (is_array($c_part))
                         print (new Text_multiline($c_part, [], NL))->render().NL;
                    else print                     $c_part                    .NL;
                }
            }
            # show message
            if (!empty($test_result['return']))
                 print 'Total run time: '.$timer_value.' sec.'.NL.'THE TEST WAS SUCCESSFUL.'.NL;
            else print 'Total run time: '.$timer_value.' sec.'.NL.'THE TEST WAS FAILED!'    .NL;
        }
    }

    static function test_all($args = []) {
        $global_report = [];
        static::_environment_prepare($args);
        Timer::tap('test_total');
        foreach (Test::get_all() as $c_test) {
            if ($c_test->type === 'php') {
                Timer::tap('test_total_'.$c_test->id);
                $c_test_result = $c_test->run();
                Timer::tap('test_total_'.$c_test->id);
                $c_timer_value = Core::format_number(
                    Timer::period_get('test_total_'.$c_test->id, -1, -2), Core::FPART_MAX_LEN
                );
                # make report
                print 'TEST: '.$c_test->id.NL;
                if (!empty($c_test_result['reports'])) {
                    foreach ($c_test_result['reports'] as $c_dpath => $c_part) {
                        $c_depth = Core::path_get_depth($c_dpath);
                        if (is_array($c_part))
                            foreach ($c_part as $c_key => $c_line) {
                                if ($c_line instanceof Text_multiline)
                                    $c_line->delimiter = NL;
                                $c_part[$c_key] = Core::to_rendered($c_line);
                            }
                        if (is_array($c_part))
                             print (new Text_multiline($c_part, [], NL))->render().NL;
                        else print                     $c_part                    .NL;
                    }
                }
                # show message
                if (!empty($c_test_result['return']))
                     print 'Total run time: '.$c_timer_value.' sec.'.NL.'THE TEST WAS SUCCESSFUL.'.NL;
                else print 'Total run time: '.$c_timer_value.' sec.'.NL.'THE TEST WAS FAILED!'    .NL;
                # fill the $global_report
                if (!empty($c_test_result['return']))
                     $global_report[]= str_pad($c_test->id, 30).' | '.str_pad($c_timer_value, 11).' | THE TEST WAS SUCCESSFUL.';
                else $global_report[]= str_pad($c_test->id, 30).' | '.str_pad($c_timer_value, 11).' | THE TEST WAS FAILED!';
                # break on error if '--force' param is not presented
                if (empty($c_test_result['return']) && empty($args['force'])) {
                    break;
                }
                # test delimiter
                print NL.NL.NL;
            }
        }
        Timer::tap('test_total');
        $timer_value = Core::format_number(
            Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN
        );

        print NL.NL.NL.'GLOBAL REPORT'.NL;
        print str_repeat('=', 80).NL;
        print str_pad('Test ID'    , 30).' | '.
              str_pad('Time (sec.)', 11).' | '.
              str_pad('Result'     , 27).NL;
        print str_repeat('-', 80).NL;
              foreach ($global_report as $c_line) print $c_line.NL;
        print str_repeat('=', 80).NL;
        print 'TOTAL TIME: '.$timer_value.NL;
    }

    static function _environment_prepare($args = []) {
        $_SERVER['HTTP_HOST'    ] = $args['host'       ] ?? Request::DEFAULT_HOST;
        $_SERVER['SERVER_NAME'  ] = $args['server_name'] ?? Request::DEFAULT_HOST;
        $_SERVER['LOCAL_ADDR'   ] = $args['local_addr' ] ?? Request::DEFAULT_ADDR;
        $_SERVER['SERVER_ADDR'  ] = $args['server_addr'] ?? Request::DEFAULT_ADDR;
        $_SERVER['SERVER_PORT'  ] = $args['server_port'] ?? Request::DEFAULT_PORT;
        $_SERVER['REMOTE_ADDR'  ] = $args['remote_addr'] ?? Request::DEFAULT_ADDR;
        $_SERVER['REMOTE_PORT'  ] = $args['remote_port'] ?? Request::DEFAULT_PORT;
        $_SERVER['UNENCODED_URL'] = '';
        $_SERVER['REQUEST_URI'  ] = '';
        if (!empty($args['server_software'])) {
            $_SERVER['SERVER_SOFTWARE'] = $args['server_software'];
        }
        if (!empty($args['quantity']) && Security::validate_int($args['quantity']) && $args['quantity'] >= 1 && $args['quantity'] <= 100) {
            $_POST['quantity'] = $args['quantity'];
        }
        if (!empty($args['proxy'])) {
            $_POST['proxy'] = $args['proxy'];
        }
        if (!empty($args['https'])) {
            $_POST['is_https'] = '1';
        }
    }

    static function _show_error($title) {
        print $title.NL.NL;
        print 'FORMAT: ./command test <name>'  .NL.NL;
        print 'THE FOLLOWING NAMES ARE AVAILABLE:'.NL;
        foreach (Test::get_all(false) as $c_test) {
            print '  - '.$c_test->id.': '.Core::to_rendered($c_test->title).NL;
        }
    }

}
