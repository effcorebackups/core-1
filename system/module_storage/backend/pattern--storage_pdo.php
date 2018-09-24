<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \PDO as pdo;
          use \PDOException as pdo_exception;
          class storage_pdo
          implements has_external_cache {

  public $id;
  public $driver;
  public $credentials;
  public $table_prefix = '';
  public $args = [];
  protected $queries = [];
  protected $connection;

  function init() {
    if ($this->connection) return
        $this->connection;
    else {
      if ($this->credentials) {
        try {
          event::start('on_storage_init_before', 'pdo', [&$this]);
          switch ($this->driver) {
            case 'mysql':
              $this->connection = new pdo(
                $this->driver.':host='.
                $this->credentials->host_name.';port='.
                $this->credentials->port.';dbname='.
                $this->credentials->storage_id,
                $this->credentials->user_name,
                $this->credentials->password);
              break;
            case 'sqlite':
              $this->connection = new pdo(
                $this->driver.':'.data::directory.
                $this->credentials->file_name);
              break;
          }
          event::start('on_storage_init_after', 'pdo', [&$this]);
          return $this->connection;
        } catch (pdo_exception $e) {
          message::insert(
            translation::get('Storage %%_id is not available!', ['id' => $this->id]), 'warning'
          );
        }
      } else {
        $path = (new file(data::directory))->path_relative_get();
        $link = (new markup('a', ['href' => '/install'], 'Installation'))->render();
        message::insert(
          translation::get('Credentials for storage %%_id was not set!', ['id' => $this->id]).br.
          translation::get('Restore the storage credentials in "%%_path" dirrectory or reinstall this system on the page: %%_link', ['path' => $path, 'link' => $link]), 'warning'
        );
      }
    }
  }

  function test($driver, $params = []) {
    try {
      switch ($driver) {
        case 'mysql':
          $connection = new pdo(
            $driver.':host='.
            $params->host_name.';port='.
            $params->port.';dbname='.
            $params->storage_id,
            $params->user_name,
            $params->password);
          break;
        case 'sqlite':
          $path = data::directory.$params->file_name;
          $connection = new pdo($driver.':'.$path);
          if (!is_writable($path)) {
            throw new \Exception('File is not writable!');
          }
          break;
      }
      $connection = null;
      return true;
    } catch (pdo_exception $e) {
      return ['message' => $e->getMessage(), 'code' => $e->getCode()]; } catch (\Exception $e) {
      return ['message' => $e->getMessage(), 'code' => $e->getCode()];
    }
  }

  function transaction_begin()     {if ($this->init()) return $this->connection->beginTransaction();}
  function transaction_roll_back() {if ($this->init()) return $this->connection->rollBack();}
  function transaction_commit()    {if ($this->init()) return $this->connection->commit();}

  function query_to_string(...$query) {
    return implode(' ', core::array_values_select_recursive($query)).';';
  }

  function query(...$query) {
    if (is_array($query[0])) $query = $query[0];
    if ($this->init()) {
      $this->queries[] = $query;
      event::start('on_query_before', 'pdo', [&$this, &$query]);
      $result = $this->connection->prepare($this->query_to_string($query));
      if ($result) $result->execute($this->args);
      $errors = $result ? $result->errorInfo() : ['query prepare return the false', 'no', 'no'];
      event::start('on_query_after', 'pdo', [&$this, &$query, &$result, &$errors]);
      $this->args = [];
      if ($errors !== ['00000', null, null]) {
        message::insert(
          translation::get('Query error!').br.
          translation::get('sql state: %%_state', ['state' => translation::get($errors[0])]).br.
          translation::get('driver error code: %%_code', ['code' => translation::get($errors[1])]).br.
          translation::get('driver error text: %%_text', ['text' => translation::get($errors[2])]), 'error'
        );
        return null;
      }
      switch ($query[0]) {
        case 'SELECT': return $result ? $result->fetchAll(pdo::FETCH_CLASS|pdo::FETCH_PROPS_LATE, '\\effcore\\instance') : null;
        case 'INSERT': return $this->connection->lastInsertId();
        case 'UPDATE': return $result->rowCount();
        case 'DELETE': return $result->rowCount();
        default      : return $result;
      }
    }
  }

  function tables(...$tables) {
    $return = [];
    foreach (is_array($tables[0]) ?
                      $tables[0] : $tables as $c_table) {
      switch ($this->driver) {
        case 'mysql' :
          $return[] = '`'.$this->table_prefix.$c_table.'`';
          $return[] = $this->op(',');
          break;
        case 'sqlite':
          $return[] = '"'.$this->table_prefix.$c_table.'"';
          $return[] = $this->op(',');
          break;
      }
    }
    array_pop($return);
    return $return;
  }

  function fields(...$fields) {
    $return = [];
    foreach (is_array($fields[0]) ?
                      $fields[0] : $fields as $c_field) {
      $return[] = $c_field;
      $return[] = $this->op(',');}
    array_pop($return);
    return $return;
  }

  function values(...$values) {
    $return = [];
    foreach (is_array($values[0]) ?
                      $values[0] : $values as $c_value) {
      $this->args[] = $c_value;
      $return[] = '?';
      $return[] = $this->op(',');
    }
    array_pop($return);
    return $return;
  }

  function condition($field, $value, $op = '=') {
    return ['field' => $this->fields($field), 'op' => $op,
            'value' => $this->values($value)];
  }

  function op($op) {
    return $op;
  }

  function attributes($data, $op = 'and') {
    $return = [];
    foreach ($data as $c_field => $c_value) {
      $return[] = is_array($c_value) ? $c_value : $this->condition($c_field, $c_value);
      $return[] = $this->op($op);}
    array_pop($return);
    return $return;
  }

  ################
  ### entities ###
  ################

  function entity_install($entity) {
    if ($this->init()) {
      $fields = [];
      foreach ($entity->fields as $c_name => $c_info) {
      # prepare field type
        $c_properties = [$c_name];
        switch ($c_info->type) {
          case 'autoincrement':
            if ($this->driver == 'mysql')  $c_properties[] = 'integer primary key auto_increment';
            if ($this->driver == 'sqlite') $c_properties[] = 'integer primary key autoincrement';
            break;
          default:
            $c_properties[] = $c_info->type.(isset($c_info->size) ?
                                               '('.$c_info->size.')' : '');
        }
      # prepare field properties
        if (property_exists($c_info, 'not_null') && $c_info->not_null) $c_properties[] = 'not null';
        if (property_exists($c_info, 'null')     && $c_info->null)     $c_properties[] = 'null';
        if (property_exists($c_info, 'default')) {
          if     ($c_info->default === 0)    $c_properties[] = 'default 0';
          elseif ($c_info->default === null) $c_properties[] = 'default null';
          else                               $c_properties[] = 'default \''.$c_info->default.'\'';
        }
        $fields[] = $c_properties;
        $fields[] = ',';
      }
    # prepare constraints
      $auto_name = $entity->auto_name_get();
      foreach ($entity->constraints as $suffix => $c_cstr) {
        if ($c_cstr->fields != [$auto_name => $auto_name]) {
          $s_cstr_name = $this->tables($entity->catalog_id.'_'.$suffix);
          $fields[] = ['CONSTRAINT', $s_cstr_name, $c_cstr->type, '(', $this->fields($c_cstr->fields), ')'];
          $fields[] = ',';
        }
      }
      array_pop($fields);
    # create entity
      $s_table_name = $this->tables($entity->catalog_id);
      $this->transaction_begin();
      $this->query('DROP', 'TABLE', 'IF EXISTS', $s_table_name);
      $this->query('CREATE', 'TABLE', $s_table_name, '(', $fields, ')');
    # create indexes
      foreach ($entity->indexes as $suffix => $c_idx) {
        $s_idx_name = $this->tables($entity->catalog_id.'_'.$suffix);
        $this->query('CREATE', $c_idx->type, $s_idx_name, 'ON', $s_table_name, '(', $this->fields($c_idx->fields), ')');
      }
      return $this->transaction_commit();
    }
  }

  function entity_uninstall($entity) {
    if ($this->init()) {
      return $this->query('DROP', 'TABLE', $this->tables($entity->catalog_id));
    }
  }

  function instances_select($entity, $conditions = [], $order = [], $limit = 0, $offset = 0) {
    if ($this->init()) {
      $query = ['SELECT', '*', 'FROM', $this->tables($entity->catalog_id)];
      if (count($conditions)) array_push($query, 'WHERE',       $this->attributes($conditions));
      if (count($order))      array_push($query, 'ORDER', 'BY', $this->fields($order));
      if ($limit)             array_push($query, 'LIMIT', $limit);
      if ($offset)            array_push($query, 'OFFSET', $offset);
      $result = $this->query($query);
      foreach ($result as $c_instance) {
        $c_instance->entity_name_set($entity->name);
      }
      return $result;
    }
  }

  function instance_select($instance) { # return: null | instance
    if ($this->init()) {
      $entity = $instance->entity_get();
      $idkeys = array_intersect_key($instance->values_get(), $entity->keys_get());
      $fields = $entity->fields_name_get();
      $result = $this->query(
        'SELECT', $this->fields($fields),
        'FROM',   $this->tables($entity->catalog_id),
        'WHERE',  $this->attributes($idkeys), 'LIMIT', 1);
      if (isset($result[0])) {
        foreach ($result[0]->values as $c_name => $c_value) {
          $instance->{$c_name} = $c_value;
        }
        return $instance;
      }
    }
  }

  function instance_insert($instance) { # return: null | instance | instance + new_id
    if ($this->init()) {
      $entity = $instance->entity_get();
      $values = array_intersect_key($instance->values_get(), $entity->fields_name_get());
      $fields = array_keys($values);
      $auto_name = $entity->auto_name_get();
      $new_id = $this->query(
        'INSERT', 'INTO', $this->tables($entity->catalog_id), '(',
                          $this->fields($fields), ')',
        'VALUES', '(',    $this->values($values), ')');
      if ($new_id !== null && $auto_name == null) return $instance;
      if ($new_id !== null && $auto_name != null) {
        $instance->{$auto_name} = $new_id;
        return $instance;
      }
    }
  }

  function instance_update($instance) { # return: null | instance
    if ($this->init()) {
      $entity = $instance->entity_get();
      $idkeys = array_intersect_key($instance->values_get(), $entity->keys_get(true, false));
      $values = array_intersect_key($instance->values_get(), $entity->fields_name_get());
      $row_count = $this->query(
        'UPDATE', $this->tables($entity->catalog_id),
        'SET',    $this->attributes($values, ','),
        'WHERE',  $this->attributes($idkeys));
      if ($row_count === 1) {
        return $instance;
      }
    }
  }

  function instance_delete($instance) { # return: null | instance + empty(values)
    if ($this->init()) {
      $entity = $instance->entity_get();
      $idkeys = array_intersect_key($instance->values_get(), $entity->keys_get(true, false));
      $row_count = $this->query(
        'DELETE', 'FROM', $this->tables($entity->catalog_id),
        'WHERE',          $this->attributes($idkeys));
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
    return ['id' => 'id'];
  }

}}