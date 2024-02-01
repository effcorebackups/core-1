<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use const effcore\BR;
use effcore\Cron;
use effcore\Response;
use effcore\Text_multiline;
use effcore\Timer;
use effcore\User;

abstract class Events_Page_Cron {

    static function block_markup__cron($page, $args = []) {
        if ($page->args_get('key') === User::key_get('cron')) {
            Timer::tap('cron');
            $result = Cron::run();
            Timer::tap('cron');
            foreach ($result as $c_handler => $c_result)
                print 'Run: '.$c_handler.BR;
                print 'Cron execution time: '.Timer::period_get('cron', -1, -2).' sec.';
            exit();
        } else {
            Response::send_header_and_exit('page_not_found', null, new Text_multiline([
                'wrong cron key',
                'go to <a href="/">front page</a>'
            ], [], BR.BR));
        }
    }

}
