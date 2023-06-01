<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use Exception;
use PDO;
use PDOException;
use PDOStatement;

class storage_sql_pdo implements has_external_cache {

    public $name;
    public $driver;
    public $credentials = [];
    public $table_prefix = '';
    public $args = [];
    public $args_previous = [];
    public $error_reporting = true;
    public $errors = [];
    protected $queries = [];
    protected $connection;

    function is_available() {
        return (bool)$this->connection;
    }

    function is_installed() {
        return $this->driver &&
               $this->credentials;
    }

    function init($driver = null, $credentials = [], $table_prefix = '') {
        if ($this->connection) return
            $this->connection;
        else {
            if ($driver)       $this->driver       =         $driver;
            if ($credentials)  $this->credentials  = (object)$credentials;
            if ($table_prefix) $this->table_prefix =         $table_prefix;
            if ($this->driver &&
                $this->credentials) {
                try {
                    event::start('on_storage_init_before', 'pdo', ['storage' => &$this]);
                    switch ($this->driver) {
                        case 'mysql':
                            $this->connection = new PDO(
                                $this->driver               .':host='.
                                $this->credentials->host    .';port='.
                                $this->credentials->port    .';dbname='.
                                $this->credentials->database.';charset=utf8',
                                $this->credentials->login,
                                $this->credentials->password);
                            break;
                        case 'sqlite':
                            $this->connection = new PDO(
                                $this->driver.':'.data::DIRECTORY.
                                $this->credentials->file_name);
                            $this->query(['action' => 'PRAGMA', 'command' => 'encoding',     'operator' => '=', 'value' => '"UTF-8"']);
                            $this->query(['action' => 'PRAGMA', 'command' => 'foreign_keys', 'operator' => '=', 'value' =>  'ON'    ]);
                            break;
                    }
                    $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                    event::start('on_storage_init_after', 'pdo', ['storage' => &$this]);
                    return $this->connection;
                } catch (PDOException $e) {
                    message::insert(new text(
                        'Storage "%%_name" is not available!', ['name' => $this->name]), 'error'
                    );
                }
            } else {
                $path = (new file(data::DIRECTORY.'changes.php'))->path_get_relative();
                $link = (new markup('a', ['href' => '/install/en'], 'Installation'))->render();
                message::insert(new text_multiline([
                    'Credentials for storage "%%_name" was not set!',
                    'Restore the storage credentials in "%%_file" or reinstall this system on the page: %%_link'], [
                    'name' => $this->name,
                    'file' => $path,
                    'link' => $link]), 'credentials'
                );
            }
        }
    }

    function test($driver, $credentials = []) {
        $credentials = (object)$credentials;
        try {
            switch ($driver) {
                case 'mysql':
                    $connection = new PDO(
                        $driver           .':host='.
                        $credentials->host.';port='.
                        $credentials->port.';dbname='.
                        $credentials->database,
                        $credentials->login,
                        $credentials->password);
                    break;
                case 'sqlite':
                    $path = data::DIRECTORY.$credentials->file_name;
                    $connection = new PDO($driver.':'.$path);
                    if (!is_writable($path)) {
                        throw new Exception('File is not writable!');
                    }
                    break;
            }
            $connection = null;
            return true;
        } catch (PDOException $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()]; } catch (Exception $e) {
            return ['message' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    function title_get() {
        if ($this->init()) {
            if ($this->driver === 'mysql' ) return 'MySQL' ;
            if ($this->driver === 'sqlite') return 'SQLite';
        }
    }

    function version_get() {
        if ($this->init()) {
            if ($this->driver === 'mysql' ) return $this->query(['action' => 'SELECT', 'command' => 'version()',        'alias_begin' => 'as', 'alias' => 'version'])[0]->version;
            if ($this->driver === 'sqlite') return $this->query(['action' => 'SELECT', 'command' => 'sqlite_version()', 'alias_begin' => 'as', 'alias' => 'version'])[0]->version;
        }
    }

    function foreign_keys_checks_set($is_check = true) {
        if ($this->init()) {
            if ($this->driver ===  'mysql' && $is_check !== true) $this->query(['action' => 'SET',    'command' => 'FOREIGN_KEY_CHECKS', 'operator' => '=', 'value' => '0'  ]);
            if ($this->driver === 'sqlite' && $is_check !== true) $this->query(['action' => 'PRAGMA', 'command' => 'foreign_keys',       'operator' => '=', 'value' => 'OFF']);
            if ($this->driver ===  'mysql' && $is_check === true) $this->query(['action' => 'SET',    'command' => 'FOREIGN_KEY_CHECKS', 'operator' => '=', 'value' => '1'  ]);
            if ($this->driver === 'sqlite' && $is_check === true) $this->query(['action' => 'PRAGMA', 'command' => 'foreign_keys',       'operator' => '=', 'value' => 'ON' ]);
        }
    }

    function transaction_begin   () {if ($this->init()) return $this->connection->beginTransaction();}
    function transaction_rollback() {if ($this->init()) return $this->connection->inTransaction() ? $this->connection->rollBack() : true;}
    function transaction_commit  () {if ($this->init()) return $this->connection->inTransaction() ? $this->connection->commit()   : true;}

    function query_test(...$query) {
        if (is_array($query[0]))
            $query = $query[0];
        if ($this->init()) {
            $this->prepare_query($query);
            $query_flat = core::array_values_select_recursive($query);
            $query_flat_string = implode(' ', $query_flat).';';
            $statement = $this->connection->prepare($query_flat_string);
            if ($statement instanceof PDOStatement) {
                $statement->execute($this->args);
                   $error_info = $statement->errorInfo();
            } else $error_info = ['HY007', 0, 'Associated statement is not prepared'];
            $this->args = [];
            return $error_info;
        }
    }

    function query(...$query) {
        if (is_array($query[0]))
            $query = $query[0];
        if ($this->init()) {
            event::start('on_query_before', 'pdo', ['storage' => &$this, 'query' => &$query]);
            $this->queries[] = $query_prepared = $query;
            $this->prepare_query($query_prepared);
            $query_flat = core::array_values_select_recursive($query_prepared);
            $query_flat_string = implode(' ', $query_flat).';';
            $statement = $this->connection->prepare($query_flat_string);
            if ($statement instanceof PDOStatement) {
                $statement->execute($this->args);
                   $error_info = $statement->errorInfo();
            } else $error_info = ['HY007', 0, 'Associated statement is not prepared'];
            event::start('on_query_after', 'pdo', ['storage' => &$this, 'query' => $query, 'statement' => &$statement, 'errors' => &$error_info]);
            $this->args_previous = $this->args;
            $this->args = [];
            if ($error_info[0] !== PDO::ERR_NONE) {
                $this->errors[] = $error_info;
                if ($this->error_reporting === true)
                    static::error_report($error_info, $query_flat_string, $this->args_previous);
                return null;
            }
            switch (strtoupper(array_values($query)[0])) {
                case 'SELECT': return $statement->fetchAll(PDO::FETCH_CLASS| PDO::FETCH_PROPS_LATE, '\\effcore\\instance');
                case 'INSERT': return $this->connection->lastInsertId();
                case 'UPDATE': return $statement->rowCount();
                case 'DELETE': return $statement->rowCount();
                default      : return $statement;
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function prepare_query(&$query = [], $is_emulation = false) {
        foreach ($query as $c_key => &$c_value) {
            $c_modifier = strrchr($c_key, '!');
            if (is_array($c_value) && $c_modifier !== '!v') {
                $this->prepare_query($c_value, $is_emulation);
                switch ($c_modifier) {
                    case '!,':
                    case '!and':
                    case '!or':
                        $c_new_values = [];
                        foreach ($c_value as $c_sub_key => $c_sub_values) {
                            if (!is_int($c_sub_key))
                                 $c_new_values[$c_sub_key] = $c_sub_values;
                            else $c_new_values[          ] = $c_sub_values;
                            $c_new_values[] = ltrim($c_modifier, '!');
                        }
                        array_pop($c_new_values);
                        $c_value = $c_new_values;
                        break;
                }
            } else {
                switch ($c_modifier) {
                    case '!t': $c_value = $this->prepare_table($c_value);                break;
                    case '!f': $c_value = $this->prepare_field($c_value);                break;
                    case '!v': $c_value = $this->prepare_value($c_value, $is_emulation); break;
                }
            }
        }
    }

    function prepare_table($name) {
        if ($name[0] === '~') $name = entity::get(ltrim($name, '~'))->catalog_name;
        if ($this->driver === 'mysql' ) return '`'.$this->table_prefix.$name.'`';
        if ($this->driver === 'sqlite') return '"'.$this->table_prefix.$name.'"';
    }

    function prepare_field($name) {
        if (strpos($name, '.') !== false) {
            $parts = explode('.', $name);
            if ($this->driver === 'mysql' ) return $this->prepare_table($parts[0]).'.'.($parts[1] === '*' ? '*' : '`'.$parts[1].'`');
            if ($this->driver === 'sqlite') return $this->prepare_table($parts[0]).'.'.($parts[1] === '*' ? '*' : '"'.$parts[1].'"'); } else {
            if ($this->driver === 'mysql' ) return $name === '*' ? '*' : '`'.$name.'`';
            if ($this->driver === 'sqlite') return $name === '*' ? '*' : '"'.$name.'"';
        }
    }

    function prepare_value($value, $is_emulation = false) {
        if (!$is_emulation)
            if (is_array($value)) foreach ($value as $c_sub_value)
                 $this->args[] = core::return_rendered($c_sub_value);
            else $this->args[] = core::return_rendered(      $value);
        return is_array($value) ? implode(', ', array_pad([], count($value), '?')) : '?';
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function prepare_tables(...$tables) {
        $result = [];
        foreach (is_array($tables[0]) ? $tables[0] : $tables as $c_id => $c_name)
            $result[$c_id.'_!t'] = $c_name;
        return $result;
    }


    function prepare_fields(...$fields) {
        $result = [];
        foreach (is_array($fields[0]) ? $fields[0] : $fields as $c_id => $c_name)
            $result[$c_id.'_!f'] = $c_name;
        return $result;
    }

    function prepare_values(...$values) {
        $result = [];
        foreach (is_array($values[0]) ? $values[0] : $values as $c_id => $c_name)
            $result[$c_id.'_!v'] = $c_name;
        return $result;
    }

    function prepare_attributes($attributes, $group_op = 'and', $op = '=') {
        $result = [];
        foreach ($attributes as $c_field => $c_value) {
            $result[$c_field] = [
                'field_!f' => $c_field,
                'operator' => $op,
                'value_!v' => $c_value];
            $result[] = $group_op;
        }
        array_pop($result);
        return $result;
    }

    ################
    ### entities ###
    ################

    function entity_install($entity) {
        if ($this->init()) {
            $fields      = [];
            $constraints = [];
            foreach ($entity->fields as $c_name => $c_info) {

                # prepare field name
                $c_field = [
                    'name_!f' => $c_name
                ];

                # ─────────────────────────────────────────────────────────────────────
                # prepare field type
                # ─────────────────────────────────────────────────────────────────────

                switch ($c_info->type) {
                    case 'autoincrement':
                        if ($this->driver ===  'mysql') $c_field += ['type' => 'integer', 'primary_key' => 'primary key', 'autoincrement' => 'auto_increment'];
                        if ($this->driver === 'sqlite') $c_field += ['type' => 'integer', 'primary_key' => 'primary key', 'autoincrement' =>  'autoincrement'];
                        break;
                    default:
                        $c_field['type'] = $c_info->type;
                        if (isset($c_info->size)) {
                            $c_field['size_begin'] = '(';
                            $c_field['size'] = $c_info->size;
                            $c_field['size_end'] = ')';
                        }
                }

                # ─────────────────────────────────────────────────────────────────────
                # prepare field properties
                # ─────────────────────────────────────────────────────────────────────

                if (isset($c_info->collate) && $c_info->collate === 'nocase' && $this->driver === 'mysql' ) $c_field += ['collate_begin' => 'collate', 'collate' => 'utf8_general_ci'];
                if (isset($c_info->collate) && $c_info->collate === 'nocase' && $this->driver === 'sqlite') $c_field += ['collate_begin' => 'collate', 'collate' => 'nocase'         ];
                if (isset($c_info->collate) && $c_info->collate === 'binary' && $this->driver === 'mysql' ) $c_field += ['collate_begin' => 'collate', 'collate' => 'utf8_bin'       ];
                if (isset($c_info->collate) && $c_info->collate === 'binary' && $this->driver === 'sqlite') $c_field += ['collate_begin' => 'collate', 'collate' => 'binary'         ];
                # constraint NOT NULL
                if (property_exists($c_info, 'not_null') &&
                                    $c_info->not_null) $c_field['not_null'] = 'not null';
                # constraint DEFAULT
                if (property_exists($c_info, 'default')) {
                    if     ($c_info->default === 0)    $c_field += ['default_begin' => 'default', 'default' => '0'                       ];
                    elseif ($c_info->default === null) $c_field += ['default_begin' => 'default', 'default' => 'null'                    ];
                    else                               $c_field += ['default_begin' => 'default', 'default' => '\''.$c_info->default.'\''];
                }
                # constraint CHECK
                if ($this->driver === 'sqlite' && isset($c_info->check)) $c_field['check'] = ['check_begin' => 'check', 'check' => $c_info->check];
                $fields[$c_name] = $c_field;
            }

            # ─────────────────────────────────────────────────────────────────────
            # constraints: PRIMARY, UNIQUE, FOREIGN
            # ─────────────────────────────────────────────────────────────────────

            $auto_name = $entity->auto_name_get();
            foreach ($entity->constraints as $c_name => $c_info) {
                if ($c_info->fields !== [$auto_name => $auto_name]) {
                    if ($c_info->type === 'primary') $constraints['constraint-'.$c_name] = ['constraint' => 'CONSTRAINT', 'name_!f' => $this->table_prefix.$entity->catalog_name.'__'.$c_name, 'type' => 'PRIMARY KEY', 'fields_begin' => '(', 'fields_!,' => $this->prepare_fields($c_info->fields), 'fields_end' => ')'];
                    if ($c_info->type ===  'unique') $constraints['constraint-'.$c_name] = ['constraint' => 'CONSTRAINT', 'name_!f' => $this->table_prefix.$entity->catalog_name.'__'.$c_name, 'type' => 'UNIQUE',      'fields_begin' => '(', 'fields_!,' => $this->prepare_fields($c_info->fields), 'fields_end' => ')'];
                    if ($c_info->type === 'foreign') $constraints['constraint-'.$c_name] = ['constraint' => 'CONSTRAINT', 'name_!f' => $this->table_prefix.$entity->catalog_name.'__'.$c_name, 'type' => 'FOREIGN KEY', 'fields_begin' => '(', 'fields_!,' => $this->prepare_fields($c_info->fields), 'fields_end' => ')', 'references_begin' => 'REFERENCES', 'references_target_!t' => $c_info->references, 'references_fields_begin' => '(', 'references_fields_!,' => $this->prepare_fields($c_info->references_fields), 'references_fields_end' => ')', 'on_update_begin' => 'ON UPDATE', 'on_update' => $c_info->on_update ?? 'cascade', 'on_delete_begin' => 'ON DELETE', 'on_delete' => $c_info->on_delete ?? 'cascade'];
                }
            }

            # ─────────────────────────────────────────────────────────────────────
            # create entity
            # ─────────────────────────────────────────────────────────────────────

            $this->transaction_begin();
            $this->foreign_keys_checks_set(false);
            $this->query(['action' => 'DROP', 'type' => 'TABLE', 'if_exists' => 'IF EXISTS', 'target_!t' => '~'.$entity->name]);
            if ($this->driver ===  'mysql') $this->query(['action' => 'CREATE', 'type' => 'TABLE', 'target_!t' => '~'.$entity->name, 'fields_and_constraints_begin' => '(', 'fields_and_constraints_!,' => $fields + $constraints, 'fields_and_constraints_end' => ')', 'charset_begin' => 'CHARSET', 'charset_condition' => '=', 'charset' => 'utf8']);
            if ($this->driver === 'sqlite') $this->query(['action' => 'CREATE', 'type' => 'TABLE', 'target_!t' => '~'.$entity->name, 'fields_and_constraints_begin' => '(', 'fields_and_constraints_!,' => $fields + $constraints, 'fields_and_constraints_end' => ')'                                                                               ]);
            $this->foreign_keys_checks_set(true);

            # ─────────────────────────────────────────────────────────────────────
            # create indexes
            # ─────────────────────────────────────────────────────────────────────

            foreach ($entity->indexes as $c_name => $c_info) {
                $this->query([
                    'action'       => 'CREATE',
                    'type'         => $c_info->type,
                    'name_!f'      => $this->table_prefix.$entity->catalog_name.'__'.$c_name,
                    'on'           => 'ON',
                    'target_!t'    => '~'.$entity->name,
                    'fields_begin' => '(',
                    'fields_!,'    => $this->prepare_fields($c_info->fields),
                    'fields_end'   => ')'
                ]);
            }

            return $this->transaction_commit();
        }
    }

    function entity_uninstall($entity) {
        if ($this->init()) {
            $this->foreign_keys_checks_set(false);
            $result = $this->query([
                'action'    => 'DROP',
                'type'      => 'TABLE',
                'target_!t' => '~'.$entity->name]);
            $this->foreign_keys_checks_set(true);
            return $result;
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function instances_select_count($entity, $options = []) {
        $options += ['join' => [], 'conditions' => [], 'limit' => null, 'offset' => 0];
        if ($this->init()) {
            $query = [
                'action'       => 'SELECT',
                'fields'       => ['count' => [
                    'function_begin' => 'count(',
                    'function_field' => '*',
                    'function_end'   => ')',
                    'alias_begin'    => 'as',
                    'alias'          => 'count']],
                'target_begin' => 'FROM',
                'target_!t'    => '~'.$entity->name];
            foreach ($options['join'] as $c_join_id => $c_join_part)
                     $query  ['join']   [$c_join_id] = $c_join_part;
            if (count($options['conditions'])) $query += ['condition_begin' => 'WHERE', 'condition' => $options['conditions']];
            if (      $options['limit'     ] ) {
                $query += ['limit_begin'  => 'LIMIT',  'limit'  => (int)$options['limit' ]];
                $query += ['offset_begin' => 'OFFSET', 'offset' => (int)$options['offset']];
            }
            $result = $this->query($query);
            if ( isset($result[0]->count) )
                return $result[0]->count;
        }
        return 0;
    }

    function instances_select($entity, $options = [], $idkey = null) {
        $options += ['fields' => [], 'join_fields' => [], 'join' => [], 'conditions' => [], 'group' => [], 'order' => [], 'limit' => null, 'offset' => 0];
        if ($this->init()) {
            $query = [
                'action'       => 'SELECT',
                'fields_!,'    => (count($options['fields']) ? $options['fields'] : ['all_!f' => '~'.$entity->name.'.*']) + $options['join_fields'],
                'target_begin' => 'FROM',
                'target_!t'    => '~'.$entity->name];
            foreach ($options['join'] as $c_join_id => $c_join_part)
                     $query  ['join']   [$c_join_id] = $c_join_part;
            if (count($options['conditions'])) $query += ['condition_begin' => 'WHERE',    'condition' => $options['conditions']];
            if (count($options['group'     ])) $query += ['group_begin'     => 'GROUP BY', 'group'     => $options['group'     ]];
            if (count($options['order'     ])) $query += ['order_begin'     => 'ORDER BY', 'order'     => $options['order'     ]];
            if (      $options['limit'     ] ) {
                $query += ['limit_begin'  => 'LIMIT',  'limit'  => (int)$options['limit' ]];
                $query += ['offset_begin' => 'OFFSET', 'offset' => (int)$options['offset']];
            }
            $result = [];
            foreach ($this->query($query) ?: [] as $c_instance) {
                foreach ($c_instance->values as $c_name => $c_value) {
                    if ($c_value !== null && isset($entity->fields[$c_name]->converter_on_select))
                        $c_instance->{$c_name} =  ($entity->fields[$c_name]->converter_on_select)($c_value);
                }
                $c_instance->entity_set_name($entity->name);
                if ($idkey) $result[$c_instance->{$idkey}] = $c_instance;
                else        $result[                     ] = $c_instance;
            }
            return $result;
        }
    }

    function instances_delete($entity, $options = []) {
        $options += ['conditions' => [], 'limit' => null];
        if ($this->init()) {
            $query = [
                'action'       => 'DELETE',
                'target_begin' => 'FROM',
                'target_!t'    => '~'.$entity->name];
            if (count($options['conditions'])) $query += ['condition_begin' => 'WHERE', 'condition' =>      $options['conditions']];
            if (      $options['limit'     ] ) $query += ['limit_begin    ' => 'LIMIT', 'limit'     => (int)$options['limit'     ]];
            return $this->query($query);
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function instance_select($instance) { # @return: null | instance
        if ($this->init()) {
            $entity = $instance->entity_get();
            $values = $instance->values_get();
            $id_fields = $entity->id_from_values_get($values);
            $result = $this->query([
                'action'          => 'SELECT',
                'fields'          => ['all_!f' => '*'],
                'target_begin'    => 'FROM',
                'target_!t'       => '~'.$entity->name,
                'condition_begin' => 'WHERE',
                'condition'       => $this->prepare_attributes($id_fields),
                'limit_begin'     => 'LIMIT',
                'limit'           => 1]);
            if (isset($result[0])) {
                foreach ($result[0]->values as $c_name => $c_value) {
                    if ( $c_value !== null && isset($entity->fields[$c_name]->converter_on_select) )
                         $instance->{$c_name} =    ($entity->fields[$c_name]->converter_on_select)($c_value);
                    else $instance->{$c_name} =                                                    $c_value;
                    $instance->_id_fields_original = $id_fields; }
                return $instance;
            }
        }
    }

    function instance_insert($instance) { # @return: null | instance | instance + new_id
        if ($this->init()) {
            $entity = $instance->entity_get();
            $values = $instance->values_get();
            foreach ($values as $c_name => $c_value)
                if ($values[$c_name] !== null && isset($entity->fields[$c_name]->converter_on_insert))
                    $values[$c_name] =                ($entity->fields[$c_name]->converter_on_insert)($c_value);
            $values = array_intersect_key($values, $entity->fields_get_name());
            $fields = array_keys($values);
            $auto_name = $entity->auto_name_get();
            $new_id = $this->query([
                'action'         => 'INSERT',
                'action_subtype' => 'INTO',
                'target_!t'      => '~'.$entity->name,
                'fields_begin'   => '(',
                'fields_!,'      => $this->prepare_fields($fields),
                'fields_end'     => ')',
                'values_begin'   => 'VALUES (',
                'values_!,'      => $this->prepare_values($values),
                'values_end'     => ')']);
            if ($new_id !== null && $auto_name === null) return $instance;
            if ($new_id !== null && $auto_name !== null) {
                $instance->{$auto_name} = $new_id;
                return $instance;
            }
        }
    }

    function instance_update($instance) { # @return: null | instance
        if ($this->init()) {
            $entity = $instance->entity_get();
            $values = $instance->values_get();
            foreach ($values as $c_name => $c_value)
                if ($values[$c_name] !== null && isset($entity->fields[$c_name]->converter_on_update))
                    $values[$c_name] =                ($entity->fields[$c_name]->converter_on_update)($c_value);
            $values = array_intersect_key($values, $entity->fields_get_name());
            $id_fields = $entity->id_from_values_get($values);
            $row_count = $this->query([
                'action'                  => 'UPDATE',
                'target_!t'               => '~'.$entity->name,
                'fields_and_values_begin' => 'SET',
                'fields_and_values'       => $this->prepare_attributes($values, ','),
                'condition_begin'         => 'WHERE',
                'condition'               => $this->prepare_attributes($instance->_id_fields_original ?: $id_fields)]);
            if ($row_count === 1) {
                $instance->_id_fields_original = $id_fields;
                return $instance;
            }
        }
    }

    function instance_delete($instance) { # @return: null | instance + empty(values)
        if ($this->init()) {
            $entity = $instance->entity_get();
            $values = $instance->values_get();
            $id_fields = $entity->id_from_values_get($values);
            $row_count = $this->query([
                'action'          => 'DELETE',
                'target_begin'    => 'FROM',
                'target_!t'       => '~'.$entity->name,
                'condition_begin' => 'WHERE',
                'condition'       => $this->prepare_attributes($id_fields)]);
            if ($row_count === 1) {
                $instance->values_set([]);
                return $instance;
            }
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function not_external_properties_get() {
        return [
            'name' => 'name'
        ];
    }

    static function error_report($error_info, $query_string, $args) {
        $query_beautiful = str_replace([' ,', '( ', ' )'], [',', '(', ')'], $query_string);
        $query_beautiful_args = '\''.implode('\', \'', $args).'\'';
        message::insert(new text_multiline([
            'Query error!',
            'SQL state: %%_state',
            'Driver error code: %%_code',
            'Driver error text: %%_text',
            'More info in "%%_info".'], [
            'state' => $error_info[0],
            'code'  => $error_info[1],
            'text'  => $error_info[2],
            'info'  => 'dynamic/logs/']), 'error');
        console::log_insert('storage', 'query',  count($args) ?
            'error state = %%_state'.BR.'error code = %%_code'.BR.'error text = %%_text'.BR.'query = "%%_query"'.BR.'arguments = [%%_args]' :
            'error state = %%_state'.BR.'error code = %%_code'.BR.'error text = %%_text'.BR.'query = "%%_query"',
            'error', 0, [
            'state' => $error_info[0],
            'code'  => $error_info[1],
            'text'  => $error_info[2],
            'query' => $query_beautiful,
            'args'  => $query_beautiful_args]
        );
    }

}
