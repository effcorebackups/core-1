<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

abstract class Cron {

    const AUTO_RUN_FREQUENCY_PERIOD_DEFAULT = 300;

    static function get_last_run() {
        $settings = Module::settings_get('core');
        return $settings->cron_last_run_date;
    }

    static function get_auto_run_frequency($is_formatted = false) {
        $settings = Module::settings_get('core');
        if ($is_formatted)
             return $settings->cron_auto_run_frequency ? Locale::format_seconds($settings->cron_auto_run_frequency) : 'no';
        else return $settings->cron_auto_run_frequency;
    }

    static function is_runned($period = null) {
        $settings = Module::settings_get('core');
        if ($period === null && $settings->cron_auto_run_frequency === null) $period = static::AUTO_RUN_FREQUENCY_PERIOD_DEFAULT;
        if ($period === null && $settings->cron_auto_run_frequency !== null) $period = $settings->cron_auto_run_frequency;
        return !empty($settings->cron_last_run_date) &&
                      $settings->cron_last_run_date > Core::datetime_get('-'.$period.' second');
    }

    static function run() {
        if (static::changes_store()) {
            return Event::start('on_cron_run');
        }
    }

    static function changes_store() {
        return Storage::get('data')->changes_register('core', 'update', 'settings/core/cron_last_run_date', Core::datetime_get());
    }

}
