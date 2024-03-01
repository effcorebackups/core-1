<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use const effcore\BR;
use const effcore\NL;
use effcore\Core;
use effcore\Request;
use effcore\Security;
use effcore\Test_message;
use effcore\Test;
use effcore\Text_simple;
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
            $c_depth = 0;
            Timer::tap('test_total');
            print 'TEST: '.$test->id.NL;
            foreach ($test->run() as $c_tick) {
                if ($c_tick === Test::SUCCESSFUL) break;
                if ($c_tick === Test::FAILED    ) break;
                if ($c_tick instanceof Test_message && $c_tick->type === 'dpath') {
                    $c_depth = Core::path_get_depth($c_tick->value);
                    print str_repeat('  ', $c_depth);
                    print '### '.$c_tick->value;
                    print NL;
                }
                if ($c_tick instanceof Text_simple) {
                    print str_repeat('  ', $c_depth);
                    print str_replace(BR, NL, $c_tick->render());
                    print NL;
                }
            }
            Timer::tap('test_total');
            $timer_value = Core::format_number(
                Timer::period_get('test_total', -1, -2), Core::FPART_MAX_LEN
            );
            # show message
            print 'Total run time: '.$timer_value.' sec.'.NL;
            if     ($c_tick === Test::SUCCESSFUL) print 'THE TEST WAS SUCCESSFUL.'.NL;
            elseif ($c_tick === Test::FAILED    ) print 'THE TEST WAS FAILED!'    .NL;
            else                                  print 'THE TEST WAS COMPLETED!' .NL;
        }
    }

    static function test_all($args = []) {
        $global_report = [];
        static::_environment_prepare($args);
        Timer::tap('test_total');
        foreach (Test::get_all() as $c_test) {
            if ($c_test->type === 'php') {
                $c_depth = 0;
                Timer::tap('test_total_'.$c_test->id);
                print 'TEST: '.$c_test->id.NL;
                foreach ($c_test->run() as $c_tick) {
                    if ($c_tick === Test::SUCCESSFUL) break;
                    if ($c_tick === Test::FAILED    ) break;
                    if ($c_tick instanceof Test_message && $c_tick->type === 'dpath') {
                        $c_depth = Core::path_get_depth($c_tick->value);
                        print str_repeat('  ', $c_depth);
                        print '### '.$c_tick->value;
                        print NL;
                    }
                    if ($c_tick instanceof Text_simple) {
                        print str_repeat('  ', $c_depth);
                        print str_replace(BR, NL, $c_tick->render());
                        print NL;
                    }
                }
                Timer::tap('test_total_'.$c_test->id);
                $c_timer_value = Core::format_number(
                    Timer::period_get('test_total_'.$c_test->id, -1, -2), Core::FPART_MAX_LEN
                );
                # show message
                print 'Total run time: '.$c_timer_value.' sec.'.NL;
                if     ($c_tick === Test::SUCCESSFUL) print 'THE TEST WAS SUCCESSFUL.'.NL;
                elseif ($c_tick === Test::FAILED    ) print 'THE TEST WAS FAILED!'    .NL;
                else                                  print 'THE TEST WAS COMPLETED!' .NL;
                # fill the $global_report
                if     ($c_tick === Test::SUCCESSFUL) $global_report[]= str_pad($c_test->id, 30).' | '.str_pad($c_timer_value, 11).' | THE TEST WAS SUCCESSFUL.';
                elseif ($c_tick === Test::FAILED    ) $global_report[]= str_pad($c_test->id, 30).' | '.str_pad($c_timer_value, 11).' | THE TEST WAS FAILED!';
                else                                  $global_report[]= str_pad($c_test->id, 30).' | '.str_pad($c_timer_value, 11).' | THE TEST WAS COMPLETED!';
                # break on error if '--force' param is not presented
                if ($c_tick === Test::FAILED) {
                    if (empty($args['force'])) {
                        break;
                    }
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
            if ($c_test->type === 'php') {
                print '  - '.$c_test->id.': '.Core::to_rendered($c_test->title).NL;
            }
        }
    }

}
