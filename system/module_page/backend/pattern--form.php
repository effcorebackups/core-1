<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \RecursiveDirectoryIterator as rd_iterator;
          use \RecursiveIteratorIterator as ri_iterator;
          class form extends markup implements has_external_cache {

  const period_expired_h = 60 * 60;

  public $tag_name = 'form';
  public $clicked_button;
  public $validation_id;
  public $validation_data = [];

  function build() {
    $id = $this->attribute_select('id');
    $this->validation_id = static::validation_id_get($id, $this->source_get());
    $this->validation_data = $this->validation_cache_select();
    $data_hash = core::data_hash_get($this->validation_data);
    $this->child_insert(new markup_simple('input', ['type'  => 'hidden', 'name'  => 'form_id',            'value' => $id                 ]), 'hidden_form_id'      );
    $this->child_insert(new markup_simple('input', ['type'  => 'hidden', 'name'  => 'validation_id-'.$id, 'value' => $this->validation_id]), 'hidden_validation_id');
  # send test headers
    if (module::is_enabled('test')) {
      header('X-Form-Validation-Id--'.$id.': '.$this->validation_id);
    }

  # plug external classes
    foreach ($this->children_select_recursive() as $c_npath => $c_element) {
      if ($c_element instanceof pluggable_class) {
        $c_parts = explode('/', $c_npath);
        $c_last_part = end($c_parts);
        $c_pointers = core::npath_pointers_get($this, $c_npath);
        if ($c_element->class_is_exists())
                   $c_pointers[$c_last_part] = $c_element->object_get();
        else unset($c_pointers[$c_last_part]);
      }
    }
  # build all form elements
    foreach ($this->children_select_recursive() as $c_element) {
      if (is_object($c_element) && method_exists($c_element, 'build')) {
        $c_element->build();
      }
    }
  # relate each item with it's form
    foreach ($this->children_select_recursive() as $c_npath => $c_element) {
      if (is_object($c_element) && method_exists($c_element, 'cform_set')) {
        $c_element->cform_set($this);
      }
    }

  # call init handlers
    $items = $this->form_items_get();
    event::start('on_form_init', $id, [&$this, &$items]);
    $items = $this->form_items_get();

  # ─────────────────────────────────────────────────────────────────────
  # if user click the button
  # ─────────────────────────────────────────────────────────────────────

    $this->clicked_button_set();
    if ($this->clicked_button && field::request_value_get('form_id', 0, $this->source_get()) == $id) {

    # call items validate methods
      if (empty($this->clicked_button->novalidate)) {
        foreach ($items as $c_npath => $c_item) {
          if ($c_npath[0] != '#' && is_object($c_item) && method_exists($c_item, 'validate')) {
            $c_item::validate($c_item, $this, $c_npath);
          }
        }
      }

    # call form validate handlers
      if (empty($this->clicked_button->novalidate)) {
        event::start('on_form_validate', $id, [&$this, &$items]);
      }

    # send test headers
      if (module::is_enabled('test')) {
        header('X-Form-Submit-Errors-Count: '.count(static::$errors));
      }

    # show errors
      if (static::$errors) {
        $this->attribute_insert('class', ['error' => 'error']);
        foreach (static::$errors as $c_error) {
          switch (gettype($c_error->message)) {
            case 'string':                                                 message::insert(translation::get($c_error->message, $c_error->args), 'error'); break;
            case 'object': if (method_exists($c_error->message, 'render')) message::insert(                 $c_error->message->render(),        'error'); break;
          }
        }
      }

    # call submit handler (if no errors)
      if (!static::$errors) {
        event::start('on_form_submit', $id, [&$this, &$items]);
      }

    # validation cache
      if (static::$errors != [] && core::data_hash_get($this->validation_data) != $data_hash) $this->validation_cache_update($this->validation_data);
      if (static::$errors == [] ||               count($this->validation_data) == 0         ) $this->validation_cache_delete();
    }
  }

  function source_get() {
    return $this->attribute_select('method') == 'post' ? '_POST' :
          ($this->attribute_select('method') == 'get'  ? '_GET'  : '_GET');
  }

  function form_items_get() {
    $result = [];
    $groups = [];
    foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_item) {
      if ($c_item instanceof container)         $result[$c_npath] = $c_item;
      if ($c_item instanceof button)            $result['~'.$c_item->value_get()] = $c_item;
      if ($c_item instanceof group_mono)        $groups['*'.$c_item->name_first_get()][] = $c_item;
      if ($c_item instanceof field)             $groups['#'.$c_item->name_get()][] = $c_item;
      if ($c_item instanceof field_radiobutton) $groups['#'.$c_item->name_get().':'.$c_item->value_get()][] = $c_item;
    }
    foreach ($groups as $c_name => $c_group) {
      if (count($c_group) == 1) $result[$c_name] = reset($c_group);
      if (count($c_group) >= 2) $result[$c_name] = $c_group;
    }
    return $result;
  }

  function clicked_button_set() {
    $value = field::request_value_get('button', 0, $this->source_get());
    foreach ($this->children_select_recursive() as $c_element) {
      if ($c_element instanceof button        &&
          $c_element->disabled_get() == false &&
          $c_element->value_get()    == $value) {
        $this->clicked_button = $c_element;
        return true;
      }
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for errors
  # ─────────────────────────────────────────────────────────────────────

  function error_set($message = null, $args = []) {
    static::$errors[] = (object)[
      'message' => $message,
      'args'    => $args,
      'pointer' => &$this
    ];
  }

  function has_error() {
    return (bool)count(static::$errors);
  }

  # ──────────────────────────────────────────────────────────────────────────────
  # functionality for validation cache
  # ──────────────────────────────────────────────────────────────────────────────

  function validation_cache_get_date($format = 'Y-m-d') {
    $timestmp = static::validation_id_created_extract($this->validation_id);
    return \DateTime::createFromFormat('U', $timestmp)->format($format);
  }

  protected function validation_cache_select()       {return temporary::select('validation-'.$this->validation_id,         'validation/'.$this->validation_cache_get_date().'/') ?: [];}
  protected function validation_cache_update($cache) {return temporary::update('validation-'.$this->validation_id, $cache, 'validation/'.$this->validation_cache_get_date().'/');}
  protected function validation_cache_delete()       {return temporary::delete('validation-'.$this->validation_id,         'validation/'.$this->validation_cache_get_date().'/');}

  static function validation_tmp_cleaning($limit = 5000) {
    if (file_exists(temporary::directory.'validation/')) {
      $counter = 0;
      foreach (new rd_iterator(temporary::directory.'validation/', file::scan_mode) as $c_dir_path => $c_spl_dir_info) {
        if ($c_spl_dir_info->isDir()) {
          if (core::validate_date($c_spl_dir_info->getFilename()) &&
                                  $c_spl_dir_info->getFilename() < core::date_get()) {
          # try to recursively delete all files in directory
            foreach (new ri_iterator(
                     new rd_iterator($c_dir_path, file::scan_mode)) as $c_file_path => $c_spl_file_info) {
              if ($counter < $limit) {
                @unlink($c_file_path);
                $counter++;
              } else {
                return;
              }
            }
          # try to delete empty directories
            @rmdir($c_dir_path);
          }
        }
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static public $errors = [];

  static function not_external_properties_get() {
    return [];
  }

  # ──────────────────────────────────────────────────────────────────────────────
  # functionality for validation_id
  # ──────────────────────────────────────────────────────────────────────────────

  static function validation_id_generate() {
    $hex_created       = static::validation_id_hex_created_get();
    $hex_ip            = static::validation_id_hex_ip_get();
    $hex_uagent_hash_8 = static::validation_id_hex_uagent_hash_8_get();
    $hex_random        = static::validation_id_hex_random_get();
    $validation_id = $hex_created.       # strlen == 8
                     $hex_ip.            # strlen == 32
                     $hex_uagent_hash_8. # strlen == 8
                     $hex_random;        # strlen == 8
    $validation_id.= core::signature_get($validation_id, 8, 'form_validation');
    return $validation_id;
  }

  static function validation_id_get($form_id, $source = '_POST') {
    global ${$source};
    if (static::validation_id_check(${$source}['validation_id-'.$form_id] ?? ''))
         return                     ${$source}['validation_id-'.$form_id];
    else return static::validation_id_generate();
  }

  static function validation_id_hex_created_get()       {return dechex(time());}
  static function validation_id_hex_ip_get()            {return core::ip_to_hex(core::server_remote_addr_get());}
  static function validation_id_hex_uagent_hash_8_get() {return core::mini_hash_get(core::server_user_agent_get());}
  static function validation_id_hex_random_get()        {return str_pad(dechex(random_int(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);}
  static function validation_id_hex_signature_get($id)  {return core::signature_get(substr($id, 0, 56), 8, 'form_validation');}

  static function validation_id_created_extract($id)           {return hexdec(static::validation_id_hex_created_extract($id));}
  static function validation_id_hex_created_extract($id)       {return substr($id,  0 , 8);}
  static function validation_id_hex_ip_extract($id)            {return substr($id,  8, 32);}
  static function validation_id_hex_uagent_hash_8_extract($id) {return substr($id, 40,  8);}
  static function validation_id_hex_random_extract($id)        {return substr($id, 48,  8);}
  static function validation_id_hex_signature_extract($id)     {return substr($id, 56,  8);}

  static function validation_id_check($id) {
    if (core::validate_hash($id, 64)) {
      $created           = static::validation_id_created_extract($id);
      $hex_ip            = static::validation_id_hex_ip_extract($id);
      $hex_uagent_hash_8 = static::validation_id_hex_uagent_hash_8_extract($id);
      $hex_signature     = static::validation_id_hex_signature_extract($id);
      if ($created <= time()                                                   &&
          $created >= time() - static::period_expired_h                        &&
          $hex_ip            === static::validation_id_hex_ip_get()            &&
          $hex_uagent_hash_8 === static::validation_id_hex_uagent_hash_8_get() &&
          $hex_signature     === static::validation_id_hex_signature_get($id)) {
        return true;
      }
    }
  }

}}