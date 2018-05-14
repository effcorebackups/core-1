<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form extends markup
          implements external {

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. more info in \effcore\field
  # ─────────────────────────────────────────────────────────────────────

  public $tag_name = 'form';
  public $clicked_button;
  public $clicked_button_name;
  public $errors = [];
  public $validation_cache;

  function build() {
    $values = static::get_values() + static::get_files();
    $validation_id = static::validation_id_get();
    $id = $this->attribute_select('id');
    $this->validation_cache = temporary::select('form-'.$validation_id);
  # build all form elements
    $elements = $this->children_select_recursive();
    foreach ($elements as $c_element) {
      if (method_exists($c_element, 'build')) {
        $c_element->build();
      }
    }
  # renew elements list after build and get all fields
    $elements   = $this->children_select_recursive();
    $containers = static::get_containers($this);
    $fields     = static::get_fields($this);
  # call init handlers
    event::start('on_form_init', $id, [$this, $containers]);

  # if user click the button
    if (isset($values['form_id'][0]) &&
              $values['form_id'][0] === $id && isset($values['button'][0])) {
    # get more info about clicked button
      foreach ($elements as $c_element) {
        if ($c_element instanceof markup &&
            $c_element->tag_name == 'button' &&
            $c_element->attribute_select('type') == 'submit' &&
            $c_element->attribute_select('value') === $values['button'][0]) {
          $this->clicked_button      = $c_element;
          $this->clicked_button_name = $c_element->attribute_select('value');
          break;
        }
      }
    # call field validate
      if (empty($this->clicked_button->novalidate)) {
        foreach ($fields as $c_npath => $c_field) {
          $c_field::validate($c_field, $this, $c_npath);
        }
      }
    # call form validate handlers
      if (empty($this->clicked_button->novalidate)) {
        event::start('on_form_validate', $id, [$this, $containers, &$values]);
      }
    # show errors and set error class
      foreach ($this->errors as $c_npath => $c_errors) {
        foreach ($c_errors as $c_error) {
          if ($c_npath) $elements[$c_npath]->attribute_insert('class', ['error' => 'error']);
          if ($c_error) message::insert($c_error, 'error');
        }
      }
    # call submit handler (if no errors)
      if (count($this->errors) == 0) {
        event::start('on_form_submit', $id, [$this, $containers, &$values]);
      }
    # validation cache
      if (count($this->errors) &&
                $this->validation_cache)
           temporary::update('form-'.$validation_id, $this->validation_cache);
      else temporary::delete('form-'.$validation_id);
    }

  # add form_id to the form markup
    $this->child_insert(new markup_simple('input', [
      'type'  => 'hidden',
      'name'  => 'form_id',
      'value' => $id,
    ]), 'hidden_form_id');
    $this->child_insert(new markup_simple('input', [
      'type'  => 'hidden',
      'name'  => 'validation_id',
      'value' => static::validation_id_get(),
      ]), 'hidden_validation_id');
  }

  function add_error($element_id = null, $message = null) {
    $this->errors[$element_id][] = $message;
  }

  function render() {
    $this->build();
    return parent::render();
  }

  ###########################
  ### static declarations ###
  ###########################

  static function get_not_external_properties() {
    return [];
  }

  static function get_containers($form) { # @todo: remove this function
    $return = [];
    foreach ($form->children_select_recursive() as $c_npath => $c_child) {
      if ($c_child instanceof \effcore\container) {
        $return[$c_npath] = $c_child;
      }
    }
    return $return;
  }

  static function get_fields($form) {
    $return = [];
    foreach ($form->children_select_recursive() as $c_npath => $c_child) {
      if ($c_child instanceof \effcore\field) {
        $return[$c_npath] = $c_child;
      }
    }
    return $return;
  }

  static function get_values() {
    $return = [];
    foreach ($_POST as $c_field => $c_value) {
      $return[$c_field] = is_array($c_value) ?
                                   $c_value : [$c_value];
    }
    return $return;
  }

  static function get_files() {
    $return = [];
    foreach ($_FILES as $c_field => $c_info) {
      if (!is_array($c_info['name']))     $c_info['name']     = [$c_info['name']];
      if (!is_array($c_info['type']))     $c_info['type']     = [$c_info['type']];
      if (!is_array($c_info['size']))     $c_info['size']     = [$c_info['size']];
      if (!is_array($c_info['tmp_name'])) $c_info['tmp_name'] = [$c_info['tmp_name']];
      if (!is_array($c_info['error']))    $c_info['error']    = [$c_info['error']];
      foreach ($c_info as $c_prop => $c_values) {
        foreach ($c_values as $c_index => $c_value) {
          if ($c_info['error'][$c_index] !== UPLOAD_ERR_NO_FILE) {
            if (!isset($return[$c_field][$c_index]))
                       $return[$c_field][$c_index] = new \stdClass();
            $return[$c_field][$c_index]->{$c_prop} = $c_value;
          }
        }
      }
    }
    return $return;
  }

  static function validation_id_generate() {
    $hex_created = dechex(time());
    $hex_ip = factory::ip_to_hex($_SERVER['REMOTE_ADDR']);
    $hex_uagent_hash_8 = substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 8);
    $hex_random = str_pad(dechex(rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
    return $hex_created.       # strlen == 8
           $hex_ip.            # strlen == 8
           $hex_uagent_hash_8. # strlen == 8
           $hex_random;        # strlen == 8
  }

  static function validation_id_get() {
    if (static::validation_id_check(
          isset($_POST['validation_id']) ?
                $_POST['validation_id'] : '')) {
      return    $_POST['validation_id']; } else {
      return static::validation_id_generate();
    }
  }

  static function validation_id_check($value) {
    if (factory::validate_hash($value, 32)) {
      $created = hexdec(substr($value, 0, 8));
      $ip = factory::hex_to_ip(substr($value, 8, 8));
      $uagent_hash_8 = substr($value, 16, 8);
      $random = hexdec(substr($value, 24, 8));
      if ($created <= time()              &&
          $created >= time() - 60 * 60    &&
          $ip === $_SERVER['REMOTE_ADDR'] &&
          $uagent_hash_8 === substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 8)) {
        return true;
      }
    }
  }

}}