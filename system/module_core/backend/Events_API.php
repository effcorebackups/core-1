<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Cron;
use effcore\Request;
use effcore\Response;
use effcore\Timer;
use effcore\User;

abstract class Events_API {

    const TEXT_WRONG_TOKEN = 'wrong token';

    static function on_cron_run($page, $args = []) {
        $token  = $page->args_get('key');
        $format = Request::value_get('format', 0, '_GET', Response::FORMAT_JSON);

        if ($token === User::key_get('cron')) {

            # run all handlers
            Timer::tap('cron');
            $result = Cron::run();
            Timer::tap('cron');

            # make the report
            $report = [
                'execution time' => Timer::period_get('cron', -1, -2),
                'handlers' => [],
            ];
            foreach ($result as $c_handler => $c_result) {
                $report['handlers'][] = $c_handler;
            }
            Response::send_and_exit(
                $report,
                Response::EXIT_STATE_OK,
                $format
            );

        } else {
            Response::send_and_exit(
                static::TEXT_WRONG_TOKEN,
                Response::EXIT_STATE_ERROR,
                $format
            );
        }
    }

}
