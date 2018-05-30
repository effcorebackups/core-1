<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class form extends markup
          implements has_external_cache {

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. more info in \effcore\field
  # ─────────────────────────────────────────────────────────────────────

  public $tag_name = 'form';
  public $clicked_button;
  public $clicked_button_name;
  public $errors = [];
  public $validation_id;
  public $validation_data = [];

  function build() {
    $this->validation_id = static::validation_id_get();
    $this->validation_data = $this->validation_cache_select();
    $data_hash = core::hash_data_get($this->validation_data);
    $values = static::get_values();
    $id = $this->attribute_select('id');
  # build all form elements
    $elements = $this->children_select_recursive();
    foreach ($elements as $c_element) {
      if (method_exists($c_element, 'build')) {
        $c_element->build();
      }
    }
  # relate each field with it's form
    $elements = $this->children_select_recursive();
    foreach ($elements as $c_path => $c_element) {
      if (method_exists($c_element, 'set_form')) $c_element->set_form($this);
      if (method_exists($c_element, 'set_path')) $c_element->set_path($c_path);
    }
  # renew elements list after build and get all fields
    $elements   = $this->children_select_recursive();
    $containers = static::get_containers($this);
    $fields     = static::get_fields($this);

  # call init handlers
    event::start('on_form_init', $id, [$this, $containers, &$values]);

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
        event::start('on_form_submit', $id, [$this, $fields, &$values]);
      }
    # validation cache
      if (count($this->errors) != 0 &&
          core::hash_data_get($this->validation_data) != $data_hash) {
        $this->validation_cache_update($this->validation_data);
      }
      if (count($this->errors) == 0 ||
          count($this->validation_data) == 0) {
        $this->validation_cache_delete();
      }
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
      'value' => $this->validation_id,
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

  static function get_containers($form) { # @todo: delete this function
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

  # ──────────────────────────────────────────────────────────────────────────────
  # validation cache functions
  # ──────────────────────────────────────────────────────────────────────────────

  function validation_cache_get_date($format = 'Ymd') {
    $timestamp = static::validation_id_decode_created($this->validation_id);
    return \DateTime::createFromFormat('U', $timestamp)->format($format);
  }

  protected function validation_cache_select()       {return temporary::select('validation-'.$this->validation_id,         'validation/'.$this->validation_cache_get_date().'/') ?: [];}
  protected function validation_cache_update($cache) {return temporary::update('validation-'.$this->validation_id, $cache, 'validation/'.$this->validation_cache_get_date().'/');}
  protected function validation_cache_delete()       {return temporary::delete('validation-'.$this->validation_id,         'validation/'.$this->validation_cache_get_date().'/');}

  # ──────────────────────────────────────────────────────────────────────────────
  # validation id functions
  # ──────────────────────────────────────────────────────────────────────────────

  static function validation_id_generate() {
    $hex_created = dechex(time());
    $hex_ip = core::ip_to_hex($_SERVER['REMOTE_ADDR']);
    $hex_uagent_hash_8 = substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 8);
    $hex_random = str_pad(dechex(rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
    $validation_id = $hex_created.       # strlen == 8
                     $hex_ip.            # strlen == 8
                     $hex_uagent_hash_8. # strlen == 8
                     $hex_random;        # strlen == 8
    $validation_id.= core::signature_get($validation_id, 8);
    return $validation_id;
  }

  static function validation_id_get() {
    if (static::validation_id_check(
          isset($_POST['validation_id']) ?
                $_POST['validation_id'] : '')) {
      return    $_POST['validation_id']; } else {
      return static::validation_id_generate();
    }
  }

  static function validation_id_decode_created($id)       {return hexdec(substr($id, 0, 8));}
  static function validation_id_decode_ip($id)            {return core::hex_to_ip(substr($id, 8, 8));}
  static function validation_id_decode_uagent_hash_8($id) {return substr($id, 16, 8);}
  static function validation_id_decode_random($id)        {return hexdec(substr($id, 24, 8));}
  static function validation_id_decode_signature($id)     {return substr($id, 32, 8);}

  static function validation_id_check($value) {
    if (core::validate_hash($value, 40)) {
      $created       = static::validation_id_decode_created($value);
      $ip            = static::validation_id_decode_ip($value);
      $uagent_hash_8 = static::validation_id_decode_uagent_hash_8($value);
      $random        = static::validation_id_decode_random($value);
      $signature     = static::validation_id_decode_signature($value);
      if ($created <= time()              &&
          $created >= time() - 60 * 60    &&
          $ip === $_SERVER['REMOTE_ADDR'] &&
          $uagent_hash_8 === substr(md5($_SERVER['HTTP_USER_AGENT']), 0, 8) &&
          $signature === core::signature_get(substr($value, 0, 32), 8)) {
        return true;
      }
    }
  }

}}