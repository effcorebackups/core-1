<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use const effcore\BR;
use effcore\Core;
use effcore\Event;
use effcore\Response;
use effcore\Text_multiline;
use effcore\Timer;
use effcore\User;

abstract class Events_Page_Cron {

    static function block_markup__cron($page, $args = []) {
        if ($page->args_get('key') === User::key_get('cron')) {
            Timer::tap('cron');
            $result = Event::start('on_cron_run');
            Timer::tap('cron');
            foreach ($result as $c_handler => $c_result)
                print 'Run: '.$c_handler.BR;
                print 'Cron execution time: '.Timer::period_get('cron', -1, -2).' sec.';
            Core::cron_run_register();
            exit();
        } else {
            Response::send_header_and_exit('page_not_found', null, new Text_multiline([
                'wrong cron key',
                'go to <a href="/">front page</a>'
            ], [], BR.BR));
        }
    }

}
