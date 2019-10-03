<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \RecursiveDirectoryIterator as rd_iterator;
          use  \RecursiveIteratorIterator as ri_iterator;
          class form extends markup implements has_external_cache {

  public $tag_name = 'form';
  public $attributes = ['accept-charset' => 'UTF-8'];
  public $clicked_button;
  public $validation_id;
  public $validation_cache;
  public $validation_cache_hash;
  public $validation_cache_is_persistent = false;
  protected $items = [];

  function build() {
    $id = $this->id_get();
    if (!$id) {
      message::insert('Form ID is required!', 'warning');
      $this->children_delete();
      return;
    }
    if (!$this->is_builded) {

    # variables for validation
      $this->validation_id = static::validation_id_get($id, $this->source_get());

    # hidden fields
      $this->child_insert(new field_hidden('form_id',       $id                      ), 'hidden_id_form'      );
      $this->child_insert(new field_hidden('validation_id-'.$id, $this->validation_id), 'hidden_id_validation');

    # send test headers "X-Form-Validation-Id--form_id: validation_id"
      if (module::is_enabled('test')) {
        header('X-Form-Validation-Id--'.$id.': '.$this->validation_id);
      }

    # plug external classes
      foreach ($this->children_select_recursive() as $c_npath => $c_child) {
        if ($c_child instanceof pluggable_class) {
          $c_npath_parts = explode('/', $c_npath);
          $c_npath_last_part = end($c_npath_parts);
          $c_pointers = core::npath_get_pointers($this, $c_npath);
          if ($c_child->is_exists_class())
                     $c_pointers[$c_npath_last_part] = $c_child->object_get();
          else unset($c_pointers[$c_npath_last_part]);
        }
      }

    # build all form elements
      foreach ($this->children_select_recursive() as $c_child) {
        if (is_object($c_child) && method_exists($c_child, 'build')) {
          $c_child->build();
        }
      }

    # relate each item with it's form
      foreach ($this->children_select_recursive() as $c_child) {
        if (is_object($c_child) && method_exists($c_child, 'form_current_set')) {
          $c_child->form_current_set($this);
        }
      }

    # call init handlers
      $this->form_items_update();
      event::start('on_form_init', $id, [&$this, &$this->items], null,
        function($event, $form, $items){ # == $on_after_step
          $form->form_items_update();
        }
      );

    # if user click the button (p.s. dynamic buttons may inserted before)
      $this->clicked_button = $this->clicked_button_get();
      if ($this->is_submitted() && $this->clicked_button) {

      # call validate methods (parent must be at the end)
        if (empty($this->clicked_button->novalidate)) {
          foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) if (is_object($c_child) && method_exists($c_child, 'validate'))         {$c_result = $c_child::validate        ($c_child, $this, $c_npath); console::log_insert('form', 'validation_1', $c_npath, $c_result ? 'ok' : 'warning', 0);}
          foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) if (is_object($c_child) && method_exists($c_child, 'validate_phase_2')) {$c_result = $c_child::validate_phase_2($c_child, $this, $c_npath); console::log_insert('form', 'validation_2', $c_npath, $c_result ? 'ok' : 'warning', 0);}
          foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) if (is_object($c_child) && method_exists($c_child, 'validate_phase_3')) {$c_result = $c_child::validate_phase_3($c_child, $this, $c_npath); console::log_insert('form', 'validation_3', $c_npath, $c_result ? 'ok' : 'warning', 0);}
          event::start('on_form_validate', $id, [&$this, &$this->items]);
        }

      # send test headers "X-Form-Submit-Errors-Count: N"
        if (module::is_enabled('test')) {
          header('X-Form-Submit-Errors-Count: '.count(static::$errors));
        }

      # show errors
        if ($this->has_error() == true) {
          $this->attribute_insert('aria-invalid', 'true');
          foreach (static::$errors as $c_error) {
            switch (gettype($c_error->message)) {
              case 'string': message::insert(new text($c_error->message, $c_error->args), 'error'); break;
              case 'object': message::insert(         $c_error->message,                  'error'); break;
            }
          }
        }

      # call submit handler (if no errors)
        if ($this->has_error() == false) {
          event::start('on_form_submit', $id, [&$this, &$this->items]);
        }

      # update or delete validation cache
        if ($this->validation_cache !== null && $this->validation_cache_is_persistent != false &&                                core::hash_get_data($this->validation_cache) != $this->validation_cache_hash) $this->validation_cache_storage_update();
        if ($this->validation_cache !== null && $this->validation_cache_is_persistent == false && $this->has_error() != false && core::hash_get_data($this->validation_cache) != $this->validation_cache_hash) $this->validation_cache_storage_update();
        if ($this->validation_cache !== null && $this->validation_cache_is_persistent == false && $this->has_error() == false                                                                                ) $this->validation_cache_storage_delete();

      }

      $this->is_builded = true;
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # shared functionality
  # ─────────────────────────────────────────────────────────────────────

  function id_get() {
    return $this->attribute_select('id');
  }

  function source_get() {
    return $this->attribute_select('method') == 'post' ? '_POST' :
          ($this->attribute_select('method') == 'get'  ? '_GET'  : '_GET');
  }

  function clicked_button_get() {
    foreach ($this->children_select_recursive() as $c_child) {
      if ($c_child instanceof button &&
          $c_child->is_clicked(0, $this->source_get())) {
        return $c_child;
      }
    }
  }

  function form_items_update() {
    $this->items = [];
    $groups      = [];
    foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) {
      if ($c_child instanceof container)                                $this->items[$c_npath                                                  ] = $c_child;
      if ($c_child instanceof button)                                   $this->items['~'.$c_child->value_get     ()                            ] = $c_child;
      if ($c_child instanceof field_hidden)                             $this->items['!'.$c_child->name_get      ()                            ] = $c_child;
      if ($c_child instanceof field)                                    $groups     ['#'.$c_child->name_get      ()                          ][] = $c_child;
      if ($c_child instanceof field_radiobutton)                        $groups     ['#'.$c_child->name_get      ().':'.$c_child->value_get()][] = $c_child;
      if ($c_child instanceof group_mono && $c_child->name_get_first()) $groups     ['*'.$c_child->name_get_first()                          ][] = $c_child;
    }
    foreach ($groups as $c_name => $c_group) {
      if (count($c_group) == 1) $this->items[$c_name] = reset($c_group);
      if (count($c_group) >= 2) $this->items[$c_name] =       $c_group;
    }
  }

  function is_submitted() {
    return $this->child_select('hidden_id_form')->value_request_get(0, $this->source_get()) ==
           $this->child_select('hidden_id_form')->value_get();
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

  function validation_cache_date_get($format = 'Y-m-d') {
    $timestmp = static::validation_id_extract_created($this->validation_id);
    return \DateTime::createFromFormat('U', $timestmp)->format($format);
  }

  function validation_cache_init() {
    if ($this->validation_cache === null) {
      $instance = (new instance('cache_validation', ['id' => $this->validation_id]))->select();
      $this->validation_cache = $instance ? $instance->data : [];
      $this->validation_cache_hash = core::hash_get_data($this->validation_cache);
    }
  }

  function validation_cache_get($id) {
    $this->validation_cache_init();
    return $this->validation_cache[$id] ?? null;
  }

  function validation_cache_set($id, $data) {
    $this->validation_cache_init();
    $this->validation_cache[$id] = $data;
  }

  function validation_cache_storage_update() {
    $instance = new instance('cache_validation', ['id' => $this->validation_id]);
    if ($instance->select()) {$instance->data = $this->validation_cache; return $instance->update();}
    else                     {$instance->data = $this->validation_cache; return $instance->insert();}
  }

  function validation_cache_storage_delete() {
    return (new instance('cache_validation', [
      'id' => $this->validation_id
    ]))->delete();
  }

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
    entity::get('cache_validation')->instances_delete([
      'conditions' => ['updated_!f' => 'updated', '<', 'updated_!v' => core::datetime_get('-'.session::period_expired_d.' second')]
    ]);
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
    $hex_created       = static::validation_id_get_hex_created      ();
    $hex_ip            = static::validation_id_get_hex_ip           ();
    $hex_uagent_hash_8 = static::validation_id_get_hex_uagent_hash_8();
    $hex_random        = static::validation_id_get_hex_random       ();
    $validation_id = $hex_created.       # strlen == 8
                     $hex_ip.            # strlen == 32
                     $hex_uagent_hash_8. # strlen == 8
                     $hex_random;        # strlen == 8
    $validation_id.= core::signature_get($validation_id, 'form_validation', 8);
    return $validation_id;
  }

  static function validation_id_get($form_id, $source = '_POST') {
    global ${$source};
    if (static::validation_id_check(${$source}['validation_id-'.$form_id] ?? ''))
         return                     ${$source}['validation_id-'.$form_id];
    else return static::validation_id_generate();
  }

  static function validation_id_get_hex_created()       {return dechex(time());}
  static function validation_id_get_hex_ip()            {return core::ip_to_hex(core::server_get_addr_remote());}
  static function validation_id_get_hex_uagent_hash_8() {return core::hash_get_mini(core::server_get_user_agent());}
  static function validation_id_get_hex_random()        {return str_pad(dechex(random_int(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);}
  static function validation_id_get_hex_signature($id)  {return core::signature_get(substr($id, 0, 56), 'form_validation', 8);}

  static function validation_id_extract_created          ($id) {return hexdec(static::validation_id_extract_hex_created($id));}
  static function validation_id_extract_hex_created      ($id) {return substr($id,  0,  8);}
  static function validation_id_extract_hex_ip           ($id) {return substr($id,  8, 32);}
  static function validation_id_extract_hex_uagent_hash_8($id) {return substr($id, 40,  8);}
  static function validation_id_extract_hex_random       ($id) {return substr($id, 48,  8);}
  static function validation_id_extract_hex_signature    ($id) {return substr($id, 56,  8);}

  static function validation_id_check($id) {
    if (core::validate_hash($id, 64)) {
      $created           = static::validation_id_extract_created          ($id);
      $hex_ip            = static::validation_id_extract_hex_ip           ($id);
      $hex_uagent_hash_8 = static::validation_id_extract_hex_uagent_hash_8($id);
      $hex_signature     = static::validation_id_extract_hex_signature    ($id);
      if ($created <= time()                                                   &&
          $created >= time() - session::period_expired_h                       &&
          $hex_ip            === static::validation_id_get_hex_ip()            &&
          $hex_uagent_hash_8 === static::validation_id_get_hex_uagent_hash_8() &&
          $hex_signature     === static::validation_id_get_hex_signature($id)) {
        return true;
      }
    }
  }

}}