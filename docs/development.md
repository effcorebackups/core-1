

Activate console
=====================================================================

In "/dynamic/data/data--changes.php" change:
    data::$data['changes']['page']->update['settings/page/console_display'] = 'no';
to:
    data::$data['changes']['page']->update['settings/page/console_display'] = 'yes';
run "/shell/cache_clear.sh"


Activate Neor Profile SQL
=====================================================================

In "/dynamic/data/data--changes.php" change:
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->host = '[::1]';
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '3306';
to:
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->host = '127.0.0.1';
    data::$data['changes']['core']->insert['storages/storage/sql']->credentials->port = '4040';
run "/shell/cache_clear.sh"

