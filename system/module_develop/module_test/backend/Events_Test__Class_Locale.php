<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Test;
use effcore\Text;
use effcore\Locale;
use effcore\Core;

abstract class Events_Test__Class_Locale {

    static function test_step_code__conversion(&$test, $dpath) {

        $user_timezone = Core::timezone_get_client();

        Core::timezone_set_client('Pacific/Kiritimati'); # +14:00

        $received['+utc--locale-time_utc_to_loc'    ] = Locale::    time_utc_to_loc('12:00:00')            === '02:00:00';
        $received['+utc--locale-date_utc_to_loc'    ] = Locale::    date_utc_to_loc('2030-02-01')          === '2030-02-01'; # note: time = '00:00:00' because the time is unknown
        $received['+utc--locale-datetime_utc_to_loc'] = Locale::datetime_utc_to_loc('2030-02-01 12:00:00') === '2030-02-02 02:00:00';
        $received['+utc--locale-time_loc_to_utc'    ] = Locale::    time_loc_to_utc('02:00:00')            === '12:00:00';
        $received['+utc--locale-date_loc_to_utc'    ] = Locale::    date_loc_to_utc('2030-02-01')          === '2030-02-01'; # note: time = '00:00:00' because the time is unknown
        $received['+utc--locale-datetime_loc_to_utc'] = Locale::datetime_loc_to_utc('2030-02-02 02:00:00') === '2030-02-01 12:00:00';

        Core::timezone_set_client('Pacific/Pago_Pago'); # -11:00

        $received['-utc--locale-time_utc_to_loc'    ] = Locale::    time_utc_to_loc('10:00:00')            === '23:00:00';
        $received['-utc--locale-date_utc_to_loc'    ] = Locale::    date_utc_to_loc('2030-02-01')          === '2030-02-01'; # note: time = '00:00:00' because the time is unknown
        $received['-utc--locale-datetime_utc_to_loc'] = Locale::datetime_utc_to_loc('2030-02-01 10:00:00') === '2030-01-31 23:00:00';
        $received['-utc--locale-time_loc_to_utc'    ] = Locale::    time_loc_to_utc('23:00:00')            === '10:00:00';
        $received['-utc--locale-date_loc_to_utc'    ] = Locale::    date_loc_to_utc('2030-02-01')          === '2030-02-01'; # note: time = '00:00:00' because the time is unknown
        $received['-utc--locale-datetime_loc_to_utc'] = Locale::datetime_loc_to_utc('2030-01-31 23:00:00') === '2030-02-01 10:00:00';

        $received['core-t_datetime_to_datetime'] = Core::T_datetime_to_datetime('2030-02-01T01:02:03') === '2030-02-01 01:02:03';
        $received['core-datetime_to_t_datetime'] = Core::datetime_to_T_datetime('2030-02-01 01:02:03') === '2030-02-01T01:02:03';

        Core::timezone_set_client($user_timezone);

        foreach ($received as $c_row_id => $с_received) {
            $c_expected = true;
            $c_result = $с_received === $c_expected;
            if ($c_result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('success'))->render()]);
            if ($c_result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => $c_row_id, 'result' => (new Text('failure'))->render()]);
            if ($c_result !== true) {
                yield new Text('expected value: %%_value', ['value' => Test::result_prepare($c_expected)]);
                yield new Text('received value: %%_value', ['value' => Test::result_prepare($с_received)]);
                yield Test::FAILED;
            }
        }
    }

}
