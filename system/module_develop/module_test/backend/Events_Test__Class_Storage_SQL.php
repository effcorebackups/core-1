<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\test;

use effcore\Core;
use effcore\Entity;
use effcore\Instance;
use effcore\Session;
use effcore\Storage;
use effcore\Test;
use effcore\Text;

abstract class Events_Test__Class_Storage_SQL {

    static function test_step_code__query_prepare(&$test, $dpath) {

        # possible transpositions of '.' + 'table_name' + 'field_name' + 'value'
        # ┌───────────────────────┬─────────────┐
        # │ all variants          │ is possible │
        # ╞═══════════════════════╪═════════════╡
        # │ table_name            │ yes         │
        # │ field_name            │ yes         │
        # │ value                 │ yes         │
        # ├───────────────────────┼─────────────┤
        # │ table_name.table_name │ no          │
        # │ table_name.field_name │ yes         │
        # │ table_name.value      │ no          │
        # ├───────────────────────┼─────────────┤
        # │ field_name.table_name │ no          │
        # │ field_name.field_name │ no          │
        # │ field_name.value      │ no          │
        # ├───────────────────────┼─────────────┤
        # │ value.table_name      │ no          │
        # │ value.field_name      │ no          │
        # │ value.value           │ no          │
        # └───────────────────────┴─────────────┘

        # possible transpositions of ',' + 'table_name' + 'field_name' + 'value' + 'table_name.field_name'
        # ┌──────────────────────────────────────────────┬─────────────┐
        # │ all variants                                 │ is possible │
        # ╞══════════════════════════════════════════════╪═════════════╡
        # │ table_name, table_name                       │ yes         │
        # │ table_name, field_name                       │ no          │
        # │ table_name, value                            │ no          │
        # │ table_name, table_name.field_name            │ no          │
        # ├──────────────────────────────────────────────┼─────────────┤
        # │ field_name, table_name                       │ no          │
        # │ field_name, field_name                       │ yes         │
        # │ field_name, value                            │ yes         │
        # │ field_name, table_name.field_name            │ yes         │
        # ├──────────────────────────────────────────────┼─────────────┤
        # │ value, table_name                            │ no          │
        # │ value, field_name                            │ yes         │
        # │ value, value                                 │ yes         │
        # │ value, table_name.field_name                 │ yes         │
        # ├──────────────────────────────────────────────┼─────────────┤
        # │ table_name.field_name, table_name            │ no          │
        # │ table_name.field_name, field_name            │ yes         │
        # │ table_name.field_name, value                 │ yes         │
        # │ table_name.field_name, table_name.field_name │ yes         │
        # └──────────────────────────────────────────────┴─────────────┘

        # ┌───────────────────────────────────────────────┬───────────────────────────────────────────────────────┬────────────────────────────────────────────────────────────────────────────────────────┐
        # │ valid variants                                │ SQL syntax                                            │ how to make a code                                                                     │
        # ╞═══════════════════════════════════════════════╪═══════════════════════════════════════════════════════╪════════════════════════════════════════════════════════════════════════════════════════╡
        # │ table_name.field_name                         │ `table_name`.`field_name`                             │ 'key_!,' => ['key_!f' => 'table_name.field_name']                                      │
        # │ table_name                                    │ `table_name`                                          │             ['key_!t' => 'table_name'           ]                                      │
        # │ field_name                                    │ `field_name`                                          │             ['key_!f' => 'field_name'           ]                                      │
        # │ value                                         │ "value"                                               │             ['key_!v' => 'value'                ]                                      │
        # ├───────────────────────────────────────────────┼───────────────────────────────────────────────────────┼────────────────────────────────────────────────────────────────────────────────────────┤
        # │ table_name           , table_name             │ `table_name`             , `table_name`               │ 'key_!,' => ['key1!t' => 'table_name'           , 'key2!t' => 'table_name'           ] │
        # │ field_name           , field_name             │ `field_name`             , `field_name`               │ 'key_!,' => ['key1!f' => 'field_name'           , 'key2!f' => 'field_name'           ] │
        # │ field_name           , value                  │ `field_name`             , "value"                    │ 'key_!,' => ['key1!f' => 'field_name'           , 'key2!v' => 'value'                ] │
        # │ field_name           , table_name.field_name  │ `field_name`             , `table_name`.`field_name`  │ 'key_!,' => ['key1!f' => 'field_name'           , 'key2!f' => 'table_name.field_name'] │
        # │ value                , field_name             │ "value"                  , `field_name`               │ 'key_!,' => ['key1!v' => 'value'                , 'key2!f' => 'field_name'           ] │
        # │ value                , value                  │ "value"                  , "value"                    │ 'key_!,' => ['key1!v' => 'value'                , 'key2!v' => 'value'                ] │
        # │ value                , table_name.field_name  │ "value"                  , `table_name`.`field_name`  │ 'key_!,' => ['key1!v' => 'value'                , 'key2!f' => 'table_name.field_name'] │
        # │ table_name.field_name, field_name             │ `table_name`.`field_name`, `field_name`               │ 'key_!,' => ['key1!f' => 'table_name.field_name', 'key2!f' => 'field_name'           ] │
        # │ table_name.field_name, value                  │ `table_name`.`field_name`, "value"                    │ 'key_!,' => ['key1!f' => 'table_name.field_name', 'key2!v' => 'value'                ] │
        # │ table_name.field_name, table_name.field_name  │ `table_name`.`field_name`, `table_name`.`field_name`  │ 'key_!,' => ['key1!f' => 'table_name.field_name', 'key2!f' => 'table_name.field_name'] │
        # └───────────────────────────────────────────────┴───────────────────────────────────────────────────────┴────────────────────────────────────────────────────────────────────────────────────────┘

        $data = [
            '01' => ['data'   => ['key_!t' => 'table_name'                                                ]],
            '02' => ['data'   => ['key_!f' => 'field_name'                                                ]],
            '03' => ['data'   => ['key_!v' => 'value'                                                     ]],
            '04' => ['data'   => ['key_!f' => 'table_name.field_name'                                     ]],
            '05' => ['data!,' => ['key1!t' => 'table_name'           , 'key2!t' => 'table_name'           ]],
            '06' => ['data!,' => ['key1!f' => 'field_name'           , 'key2!f' => 'field_name'           ]],
            '07' => ['data!,' => ['key1!f' => 'field_name'           , 'key2!v' => 'value'                ]],
            '08' => ['data!,' => ['key1!f' => 'field_name'           , 'key2!f' => 'table_name.field_name']],
            '09' => ['data!,' => ['key1!v' => 'value'                , 'key2!f' => 'field_name'           ]],
            '10' => ['data!,' => ['key1!v' => 'value'                , 'key2!v' => 'value'                ]],
            '11' => ['data!,' => ['key1!v' => 'value'                , 'key2!f' => 'table_name.field_name']],
            '12' => ['data!,' => ['key1!f' => 'table_name.field_name', 'key2!f' => 'field_name'           ]],
            '13' => ['data!,' => ['key1!f' => 'table_name.field_name', 'key2!v' => 'value'                ]],
            '14' => ['data!,' => ['key1!f' => 'table_name.field_name', 'key2!f' => 'table_name.field_name']],
        ];

        $expected = [
            '01' => ['data'   => ['key_!t' => '`table_name`'                                                                ]],
            '02' => ['data'   => ['key_!f' => '`field_name`'                                                                ]],
            '03' => ['data'   => ['key_!v' => '?'                                                                           ]],
            '04' => ['data'   => ['key_!f' => '`table_name`.`field_name`'                                                   ]],
            '05' => ['data!,' => ['key1!t' => '`table_name`'             , 0 => ',', 'key2!t' => '`table_name`'             ]],
            '06' => ['data!,' => ['key1!f' => '`field_name`'             , 0 => ',', 'key2!f' => '`field_name`'             ]],
            '07' => ['data!,' => ['key1!f' => '`field_name`'             , 0 => ',', 'key2!v' => '?'                        ]],
            '08' => ['data!,' => ['key1!f' => '`field_name`'             , 0 => ',', 'key2!f' => '`table_name`.`field_name`']],
            '09' => ['data!,' => ['key1!v' => '?'                        , 0 => ',', 'key2!f' => '`field_name`'             ]],
            '10' => ['data!,' => ['key1!v' => '?'                        , 0 => ',', 'key2!v' => '?'                        ]],
            '11' => ['data!,' => ['key1!v' => '?'                        , 0 => ',', 'key2!f' => '`table_name`.`field_name`']],
            '12' => ['data!,' => ['key1!f' => '`table_name`.`field_name`', 0 => ',', 'key2!f' => '`field_name`'             ]],
            '13' => ['data!,' => ['key1!f' => '`table_name`.`field_name`', 0 => ',', 'key2!v' => '?'                        ]],
            '14' => ['data!,' => ['key1!f' => '`table_name`.`field_name`', 0 => ',', 'key2!f' => '`table_name`.`field_name`']],
        ];

        $storage = Storage::get('sql');

        foreach ($data as $c_row_id => $c_info) {
            $c_expected = $expected[$c_row_id];
            $с_received = $c_info;
            $storage->prepare_query($с_received, true);
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

    static function test_step_code__join(&$test, $dpath) {

        # ─────────────────────────────────────────────────────────────────────
        # pure SQL query
        # ─────────────────────────────────────────────────────────────────────
        #
        #    SELECT
        #        `users`.`id`,
        #        `relations_role_with_user`.`id_role`,
        #        `roles`.`title`
        #    FROM `users`
        #    LEFT OUTER JOIN `relations_role_with_user` ON `users`.`id` = `relations_role_with_user`.`id_user`
        #    LEFT OUTER JOIN `roles`                    ON `roles`.`id` = `relations_role_with_user`.`id_role`
        #    WHERE
        #        `users`.`id` = 1 AND
        #        `users`.`nickname` = "Admin"
        #    ORDER BY
        #        `roles`.`title` ASC,
        #        `users`.`id` DESC
        #    LIMIT 1
        #    OFFSET 0;
        #
        # ─────────────────────────────────────────────────────────────────────

        $storage = Storage::get(
            Entity::get('user')->storage_name
        );

        $result_1 = $storage->query([
            'action' => 'SELECT',
            'fields' => [
                'id'      => '`users`.`id`', ',',
                'id_role' => '`relations_role_with_user`.`id_role`', ',',
                'title'   => '`roles`.`title`'],
            'target_begin' => 'FROM',
            'target' => '`users`',
            'join' => [
                'relation_role_with_user' => ['type' => 'LEFT OUTER JOIN', 'target' => '`relations_role_with_user`', 'on' => 'ON', 'left' => '`users`.`id`', 'operator' => '=', 'right' => '`relations_role_with_user`.`id_user`'],
                'role'                    => ['type' => 'LEFT OUTER JOIN', 'target' => '`roles`'                   , 'on' => 'ON', 'left' => '`roles`.`id`', 'operator' => '=', 'right' => '`relations_role_with_user`.`id_role`']],
            'where_begin' => 'WHERE',
            'where' => [
                'id'       => ['field' => '`users`.`id`'      , 'operator' => '=', 'value' => 1], 'and',
                'nickname' => ['field' => '`users`.`nickname`', 'operator' => '=', 'value' => '"Admin"']],
            'order_begin' => 'ORDER BY',
            'order' => [
                'title' => ['field' => '`roles`.`title`', 'direction' => 'ASC'], ',',
                'id'    => ['field' => '`users`.`id`'   , 'direction' => 'DESC']],
            'limit_begin' => 'LIMIT',
            'limit' => 1,
            'offset_begin' => 'OFFSET',
            'offset' => 0
        ]);

        $result_2 = $storage->query([
            'action' => 'SELECT',
            'fields_!,' => [
                'id_!f'      => '~user.id',
                'id_role_!f' => '~relation_role_with_user.id_role',
                'title_!f'   => '~role.title'],
            'target_begin' => 'FROM',
            'target_!t' => '~user',
            'join' => [
                'relation_role_with_user' => ['type' => 'LEFT OUTER JOIN', 'target_!t' => '~relation_role_with_user', 'on' => 'ON', 'left_!f' => '~user.id', 'operator' => '=', 'right_!f' => '~relation_role_with_user.id_user'],
                'role'                    => ['type' => 'LEFT OUTER JOIN', 'target_!t' => '~role'                   , 'on' => 'ON', 'left_!f' => '~role.id', 'operator' => '=', 'right_!f' => '~relation_role_with_user.id_role']],
            'where_begin' => 'WHERE',
            'where' => [
                'conjunction_!and' => [
                    'id'       => ['field_!f' => '~user.id'      , 'operator' => '=', 'value_!v' => 1],
                    'nickname' => ['field_!f' => '~user.nickname', 'operator' => '=', 'value_!v' => 'Admin']]],
            'order_begin' => 'ORDER BY',
            'order_!,' => [
                'title' => ['field_!f' => '~role.title', 'direction' => 'ASC'],
                'id'    => ['field_!f' => '~user.id'   , 'direction' => 'DESC']],
            'limit_begin' => 'LIMIT',
            'limit' => 1,
            'offset_begin' => 'OFFSET',
            'offset' => 0
        ]);

        $result_3 = Entity::get('user')->instances_select([
            'fields' => [
                'id_!f' => '~user.id'],
            'join_fields' => [
                'id_role_!f' => '~relation_role_with_user.id_role',
                'title_!f'   => '~role.title'],
            'join' => [
                'relation_role_with_user' => ['type' => 'LEFT OUTER JOIN', 'target_!t' => '~relation_role_with_user', 'on' => 'ON', 'left_!f' => '~user.id', 'operator' => '=', 'right_!f' => '~relation_role_with_user.id_user'],
                'role'                    => ['type' => 'LEFT OUTER JOIN', 'target_!t' => '~role'                   , 'on' => 'ON', 'left_!f' => '~role.id', 'operator' => '=', 'right_!f' => '~relation_role_with_user.id_role']],
            'where' => [
                'conjunction_!and' => [
                    'id'       => ['field_!f' => '~user.id'      , 'operator' => '=', 'value_!v' => 1],
                    'nickname' => ['field_!f' => '~user.nickname', 'operator' => '=', 'value_!v' => 'Admin']]],
            'order_!,' => [
                'title' => ['field_!f' => '~role.title', 'direction' => 'ASC'],
                'id'    => ['field_!f' => '~user.id'   , 'direction' => 'DESC']],
            'limit' => 1,
            'offset' => 0
        ]);

        $expected = true;
        $received = isset($result_1[0]) &&
                    isset($result_2[0]) &&
                    isset($result_3[0]) &&
                    $result_1[0]->values_get() === $result_2[0]->values_get() &&
                    $result_2[0]->values_get() === $result_3[0]->values_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'join', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'join', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__group_by(&$test, $dpath) {

        # ─────────────────────────────────────────────────────────────────────
        # pure SQL query
        # ─────────────────────────────────────────────────────────────────────
        #
        #    SELECT `id_user`, count(*) as `total`
        #    FROM `sessions`
        #    WHERE `id_user` = 1 AND `expired` < "2100-01-01 00:00:00"
        #    GROUP BY `id_user`
        #    HAVING `total` > 0
        #    ORDER BY `total` DESC
        #    LIMIT 1
        #    OFFSET 0
        #
        # ─────────────────────────────────────────────────────────────────────

        $storage = Storage::get(
            Entity::get('session')->storage_name
        );

        $result_1 = $storage->query([
            'action' => 'SELECT',
            'fields' => [
                'id' => '`id_user`', ',',
                'count' => [
                    'function_begin' => 'count(',
                    'function_field' => '*',
                    'function_end'   => ')',
                    'alias_begin'    => 'as',
                    'alias'          => '`total`']],
            'target_begin' => 'FROM',
            'target' => '`sessions`',
            'where_begin' => 'WHERE',
            'where' => [
                'conjunction' => [
                    'id_user' => ['field' => '`id_user`', 'operator' => '=', 'value' => 1], 'and',
                    'expired' => ['field' => '`expired`', 'operator' => '<', 'value' => '"2100-01-01 00:00:00"']]],
            'group_begin' => 'GROUP BY',
            'group' => [
                'id_user' => '`id_user`'],
            'having_begin' => 'HAVING',
            'having' => [
                'conjunction' => [
                    'total' => [
                        'field'    => '`total`',
                        'operator' => '>',
                        'value'    => 0]]],
            'limit_begin' => 'LIMIT',
            'limit' => 1,
            'offset_begin' => 'OFFSET',
            'offset' => 0
        ]);

        $result_2 = $storage->query([
            'action' => 'SELECT',
            'fields_!,' => [
                'id_!f' => 'id_user',
                'count' => [
                    'function_begin' => 'count(',
                    'function_field' => '*',
                    'function_end'   => ')',
                    'alias_begin'    => 'as',
                    'alias_!f'       => 'total']],
            'target_begin' => 'FROM',
            'target_!t' => '~session',
            'where_begin' => 'WHERE',
            'where' => [
                'conjunction_!and' => [
                    'id_user' => ['field_!f' => 'id_user', 'operator' => '=', 'value_!v' => 1],
                    'expired' => ['field_!f' => 'expired', 'operator' => '<', 'value_!v' => '2100-01-01 00:00:00']]],
            'group_begin' => 'GROUP BY',
            'group' => [
                'id_user_!f' => 'id_user'],
            'having_begin' => 'HAVING',
            'having' => [
                'conjunction_!and' => [
                    'total' => [
                        'field_!f' => 'total',
                        'operator' => '>',
                        'value'    => 0]]],
            'limit_begin' => 'LIMIT',
            'limit' => 1,
            'offset_begin' => 'OFFSET',
            'offset' => 0
        ]);

        $result_3 = Entity::get('session')->instances_select([
            'fields'=> [
                'id_!f' => 'id_user',
                'count' => [
                    'function_begin' => 'count(',
                    'function_field' => '*',
                    'function_end'   => ')',
                    'alias_begin'    => 'as',
                    'alias_!f'       => 'total']],
            'where' => [
                'conjunction_!and' => [
                    'id_user' => ['field_!f' => 'id_user', 'operator' => '=', 'value_!v' => 1],
                    'expired' => ['field_!f' => 'expired', 'operator' => '<', 'value_!v' => '2100-01-01 00:00:00']]],
            'group' => [
                'id_user_!f' => 'id_user'],
            'having' => [
                'conjunction_!and' => [
                    'total' => [
                        'field_!f' => 'total',
                        'operator' => '>',
                        'value'    => 0]]
                ],
            'order_!,' => [
                'total' => [
                    'field_!f'  => 'total',
                    'direction' => 'DESC']],
            'limit' => 1,
            'offset' => 0
        ]);

        $expected = true;
        $received = isset($result_1[0]) &&
                    isset($result_2[0]) &&
                    isset($result_3[0]) &&
                    $result_1[0]->values_get() === $result_2[0]->values_get() &&
                    $result_2[0]->values_get() === $result_3[0]->values_get();
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'group by', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'group by', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__in(&$test, $dpath) {

        # ─────────────────────────────────────────────────────────────────────
        # pure SQL query
        # ─────────────────────────────────────────────────────────────────────
        #
        #    SELECT *
        #    FROM `roles`
        #    WHERE `module_id` IN ('user', 'test')
        #    ORDER BY `title`;
        #
        # ─────────────────────────────────────────────────────────────────────

        $result_1 = Entity::get('role')->instances_select([
            'where' => [
                'field_!f' => 'module_id',
                'in_begin_operator' => 'in (',
                'in_!,' => [
                    'in_value_1_!v' => 'user',
                    'in_value_2_!v' => 'test'],
                'in_end_operator' => ')'
            ],
            'order' => [
                'field_!f' => 'title']
        ]);

        $result_2 = Entity::get('role')->instances_select([
            'where' => [
                'field_!f'          => 'module_id',
                'in_begin_operator' => 'in (',
                'in_value_!v'       => ['user', 'test'],
                'in_end_operator'   => ')'],
            'order' => [
                'field_!f' => 'title']
        ]);

        $expected = true;
        $received = Core::data_serialize($result_1, false, true) === Core::data_serialize($result_2, false, true);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'in', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'in', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__distinct(&$test, $dpath) {

        # ─────────────────────────────────────────────────────────────────────
        # pure SQL query
        # ─────────────────────────────────────────────────────────────────────
        #
        #    SELECT DISTINCT `module_id`
        #    FROM `roles`
        #    ORDER BY `module_id`;
        #
        # ─────────────────────────────────────────────────────────────────────

        $storage = Storage::get(
            Entity::get('role')->storage_name
        );

        $result_1 = $storage->query([
            'action' => 'SELECT',
            'distinct' => 'DISTINCT',
            'fields' => [
                'module_id' => '`module_id`'],
            'target_begin' => 'FROM',
            'target' => '`roles`',
            'order_begin' => 'ORDER BY',
            'order' => [
                'field' => '`module_id`']
        ]);

        $result_2 = Entity::get('role')->instances_select([
            'distinct' => true,
            'fields' => [
                'module_id_!f' => 'module_id'],
            'order' => [
                'field_!f' => 'module_id']
        ]);

        foreach ($result_1 as $c_instance) $c_instance->entity_name = null;
        foreach ($result_2 as $c_instance) $c_instance->entity_name = null;

        $expected = true;
        $received = Core::data_serialize($result_1, false, true) === Core::data_serialize($result_2, false, true);
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'distinct', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'distinct', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

    static function test_step_code__transaction(&$test, $dpath) {

        $session_id = Core::is_CLI() ? str_repeat('0', 65) : Session::id_get();

        $storage = Storage::get(
            Entity::get('message')->storage_name
        );

        $where_clause = [
            'where' => [
                'conjunction_!and' => [
                    'id_session' => ['field_!f' => 'id_session', 'operator' => '=', 'value_!v' => $session_id],
                    'type'       => ['field_!f' => 'type'      , 'operator' => '=', 'value_!v' => 'test']]]
        ];

        ##################################################
        ### transaction_begin() + transaction_commit() ###
        ##################################################

        Entity::get('message')->instances_delete($where_clause);
        $storage->transaction_begin();

        (new Instance('message', [
            'id_session' => $session_id,
            'type'       => 'test',
            'expired'    => time() - 1000,
            'data' => new Text('Test message 1')
        ]))->insert();
        (new Instance('message', [
            'id_session' => $session_id,
            'type'       => 'test',
            'expired'    => time() - 1000,
            'data' => new Text('Test message 2')
        ]))->insert();
        (new Instance('message', [
            'id_session' => $session_id,
            'type'       => 'test',
            'expired'    => time() - 1000,
            'data' => new Text('Test message 3')
        ]))->insert();

        $count_0 = Entity::get('message')->instances_select_count($where_clause);
        $storage->transaction_commit();
        $count_1 = Entity::get('message')->instances_select_count($where_clause);

        Entity::get('message')->instances_delete($where_clause);

        $expected = true;
        $received = $count_0 === 3 && $count_1 === 3;
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'transaction', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'transaction', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }

        ####################################################
        ### transaction_begin() + transaction_rollback() ###
        ####################################################

        Entity::get('message')->instances_delete($where_clause);
        $storage->transaction_begin();

        (new Instance('message', [
            'id_session' => $session_id,
            'type'       => 'test',
            'expired'    => time() - 1000,
            'data' => new Text('Test message 4')
        ]))->insert();
        (new Instance('message', [
            'id_session' => $session_id,
            'type'       => 'test',
            'expired'    => time() - 1000,
            'data' => new Text('Test message 5')
        ]))->insert();
        (new Instance('message', [
            'id_session' => $session_id,
            'type'       => 'test',
            'expired'    => time() - 1000,
            'data' => new Text('Test message 6')
        ]))->insert();

        $count_0 = Entity::get('message')->instances_select_count($where_clause);
        $storage->transaction_rollback();
        $count_1 = Entity::get('message')->instances_select_count($where_clause);

        $expected = true;
        $received = $count_0 === 3 && $count_1 === 0;
        $result = $received === $expected;
        if ($result === true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'transaction', 'result' => (new Text('success'))->render()]);
        if ($result !== true) yield new Text('checking of item "%%_id": "%%_result"', ['id' => 'transaction', 'result' => (new Text('failure'))->render()]);
        if ($result !== true) {
            yield new Text('expected value: %%_value', ['value' => Test::result_prepare($expected)]);
            yield new Text('received value: %%_value', ['value' => Test::result_prepare($received)]);
            yield Test::FAILED;
        }
    }

}
