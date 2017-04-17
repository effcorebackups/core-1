<?php

namespace effectivecore {
          use \effectivecore\modules\data\db;
          class instance {

  public $name;
  public $fields;

  function __construct($name = '', $fields = null) {
    $this->name = $name;
    if (is_array($fields)) {
      $this->fields = new \StdClass;
      foreach ($fields as $c_key => $_value) {
        $this->fields->{$c_key} = $_value;
      }
    }
  }

  function select() {
    $entities = [];
    foreach (settings::$data['entities'] as $c_entities) {
      foreach ($c_entities as $c_entity) {
        $entities[$c_entity->name] = $c_entity;
      }
    }
    $data = reset(db::query('SELECT '.implode(', ', array_keys((array)$entities[$this->name]->fields)).' '.
                            'FROM `'.$this->name.'` '.
                            'WHERE id = "'.$this->fields->id.'" '.
                            'LIMIT 1'
    ));
    if (is_array($data)) {
      foreach ($data as $c_key => $c_value) {
        $this->fields->{$c_key} = $c_value;
      }
      return $this;
    }
  }

  function insert() {
  }

  function update() {
  }

  function delete() {
  }

}}