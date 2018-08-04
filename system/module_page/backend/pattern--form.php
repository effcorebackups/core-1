<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \RecursiveDirectoryIterator as rd_iterator;
          use \RecursiveIteratorIterator as ri_iterator;
          class form extends markup
          implements has_external_cache {

  const period_expire_h = 60 * 60;

  public $tag_name = 'form';
  public $clicked_button;
  public $clicked_button_name;
  public $validation_id;
  public $validation_data = [];
  protected $_errors = [];

  function build() {
    $this->validation_id = static::validation_id_get($this->source_get());
    $this->validation_data = $this->validation_cache_select();
    $data_hash = core::hash_data_get($this->validation_data);
    $id = $this->attribute_select('id');
    $this->button_clicked_set(field::request_value_get('button', 0, $this->source_get()));
  # build all form elements
    foreach ($this->children_select_recursive() as $c_element) {
      if (method_exists($c_element, 'build')) {
        $c_element->build();
      }
    }
  # relate each field with it's form
    foreach ($this->children_select_recursive() as $c_path => $c_element) {
      if (method_exists($c_element, 'form_set')) $c_element->form_set($this);
      if (method_exists($c_element, 'path_set')) $c_element->path_set($c_path);
    }
  # renew all variables after build process
    $elements = $this->children_select_recursive();
    $items    = $this->form_items_get();

  # call init handlers
    event::start('on_form_init', $id, [$this, $items]);

  # if user click the button
    if ($this->clicked_button_name &&
        field::request_value_get('form_id', 0, $this->source_get()) == $id) {

    # call items validate handlers
      if (empty($this->clicked_button->novalidate)) {
        foreach ($items as $c_npath => $c_item) {
          if (method_exists($c_item, 'validate')) {
            $c_item::validate($c_item, $this, $c_npath);
          }
        }
      }

    # call form validate handlers
      if (empty($this->clicked_button->novalidate)) {
        event::start('on_form_validate', $id, [$this, $items]);
      }

    # send specific header
      header('X-Submit-Errors-Count: '.$this->errors_count_get());

    # show errors
      if ($this->errors_count_get() != 0) {
        $this->attribute_insert('class', ['error' => 'error']);
        foreach ($this->errors_get() as $c_errors) {
          foreach ($c_errors as $c_error) {
            if ($c_error) {
              message::insert($c_error, 'error');
            }
          }
        }
      }

    # call submit handler (if no errors)
      if ($this->errors_count_get() == 0) {
        event::start('on_form_submit', $id, [$this, $items]);
      }

    # validation cache
      if ($this->errors_count_get() != 0 && core::hash_data_get($this->validation_data) != $data_hash) {
        $this->validation_cache_update($this->validation_data);
      }
      if ($this->errors_count_get() == 0 || count($this->validation_data) == 0) {
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

  function error_add($message = null) {
    $this->_errors['_form'][] = $message;
  }

  function errors_count_get() {
    $return = 0;
    foreach ($this->errors_get() as $c_errors) {
      $return += count($c_errors);
    }
    return $return;
  }

  function errors_get() {
    $return = $this->_errors;
    foreach ($this->fields_get() as $c_npath => $c_field) {
      if ($c_field->errors_count_get()) {
        $return[$c_npath] = $c_field->errors_get();
      }
    }
    return $return;
  }

  function source_get() {
    return $this->attribute_select('method') == 'post' ? '_POST' :
          ($this->attribute_select('method') == 'get'  ? '_GET'  : '_GET');
  }

  function fields_get() {
    $return = [];
    foreach ($this->children_select_recursive() as $c_npath => $c_item) {
      if ($c_item instanceof \effcore\field) {
        $return[$c_npath] = $c_item;
      }
    }
    return $return;
  }

  function form_items_get() {
    $return = [];
    $items = [];
    foreach ($this->children_select_recursive() as $c_npath => $c_item) {
      if ($c_item instanceof \effcore\container)         $return[$c_npath] = $c_item;
      if ($c_item instanceof \effcore\group_mono)        $items['##'.$c_item->first_element_name_get()][] = $c_item;
      if ($c_item instanceof \effcore\field)             $items['#'.$c_item->element_name_get()][] = $c_item;
      if ($c_item instanceof \effcore\field_radiobutton) $items['#'.$c_item->element_name_get().':'.$c_item->value_get()][] = $c_item;
    }
    foreach ($items as $c_name => $c_group) {
      if (count($c_group) == 1) $return[$c_name] = reset($c_group);
      if (count($c_group) >= 2) $return[$c_name] = $c_group;
    }
    return $return;
  }

  function button_clicked_set($value) {
    foreach ($this->children_select_recursive() as $c_element) {
      if ($c_element instanceof markup                      &&
          $c_element->tag_name == 'button'                  &&
          $c_element->attribute_select('type' ) == 'submit' &&
          $c_element->attribute_select('value') == $value) {
        $this->clicked_button      = $c_element;
        $this->clicked_button_name = $value;
        break;
      }
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

  # ──────────────────────────────────────────────────────────────────────────────
  # functionality for validation cache
  # ──────────────────────────────────────────────────────────────────────────────

  function validation_cache_get_date($format = 'Y-m-d') {
    $timestamp = static::validation_id_decode_created($this->validation_id);
    return \DateTime::createFromFormat('U', $timestamp)->format($format);
  }

  protected function validation_cache_select()       {return temporary::select('validation-'.$this->validation_id,         'validation/'.$this->validation_cache_get_date().'/') ?: [];}
  protected function validation_cache_update($cache) {return temporary::update('validation-'.$this->validation_id, $cache, 'validation/'.$this->validation_cache_get_date().'/');}
  protected function validation_cache_delete()       {return temporary::delete('validation-'.$this->validation_id,         'validation/'.$this->validation_cache_get_date().'/');}

  static function validation_cache_clean($limit = 5000) {
    if (file_exists(temporary::directory.'validation/')) {
      $counter = 0;
      foreach (new rd_iterator(temporary::directory.'validation/', file::scan_dir_mode) as $c_dir_path => $c_dir_info) {
        if ($c_dir_info->isDir() &&
            core::validate_date($c_dir_info->getFilename()) &&
                                $c_dir_info->getFilename() < core::date_get()) {
          foreach (new ri_iterator(
                   new rd_iterator($c_dir_path, file::scan_dir_mode)) as $c_file_path => $null) {
            if ($counter < $limit) {
              unlink($c_file_path);
              $counter++;
            } else {
              return;
            }
          }
        # try to delete empty directories
          rmdir($c_dir_path);
        }
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static function not_external_properties_get() {
    return [];
  }

  # ──────────────────────────────────────────────────────────────────────────────
  # functionality for validation_id
  # ──────────────────────────────────────────────────────────────────────────────

  static function validation_id_generate() {
    $hex_created = dechex(time());
    $hex_ip = core::ip_to_hex(core::server_remote_addr_get());
    $hex_uagent_hash_8 = substr(md5(core::server_user_agent_get()), 0, 8);
    $hex_random = str_pad(dechex(rand(0, 0xffffffff)), 8, '0', STR_PAD_LEFT);
    $validation_id = $hex_created.       # strlen == 8
                     $hex_ip.            # strlen == 8
                     $hex_uagent_hash_8. # strlen == 8
                     $hex_random;        # strlen == 8
    $validation_id.= core::signature_get($validation_id, 8, 'form_validation');
    return $validation_id;
  }

  static function validation_id_get($source = '_POST') {
    global ${$source};
    if (static::validation_id_check(
          isset(${$source}['validation_id']) ?
                ${$source}['validation_id'] : '')) {
      return    ${$source}['validation_id']; } else {
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
      if ($created <= time()                           &&
          $created >= time() - static::period_expire_h &&
          $ip === core::server_remote_addr_get()       &&
          $uagent_hash_8 === substr(md5(core::server_user_agent_get()), 0, 8) &&
          $signature === core::signature_get(substr($value, 0, 32), 8, 'form_validation')) {
        return true;
      }
    }
  }

}}