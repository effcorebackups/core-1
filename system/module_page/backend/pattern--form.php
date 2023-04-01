<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \RecursiveDirectoryIterator as rd_iterator;
          use  \RecursiveIteratorIterator as ri_iterator;
          class form extends markup implements has_external_cache {

  public $tag_name = 'form';
  public $template = 'form';
  public $attributes = ['accept-charset' => 'UTF-8'];
  public $title;
  public $title_tag_name = 'h2';
  public $title_is_visible = 1;
  public $title_attributes = ['data-form-title' => true];
  public $clicked_button;
  public $number;
  public $validation_id;
  public $validation_cache;
  public $validation_cache_hash;
  public $validation_cache_is_persistent = false;
  public $has_no_fields = false;
  public $has_no_items = false;
  public $has_error_on_build = false;
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
      $this->number        = static::current_number_generate();
      $this->validation_id = static::validation_id_get($this);

    # hidden fields
      $this->child_insert(new field_hidden('form_id',       $id                 ), 'hidden_id_form'      );
      $this->child_insert(new field_hidden('validation_id', $this->validation_id), 'hidden_id_validation');

    # send test headers 'X-Form-Validation-Id--form_id: validation_id'
      if (module::is_enabled('test')) {
        header('X-Form-Validation-Id--'.$id.': '.$this->validation_id);
      }

    # call "build" handlers
      event::start('on_form_build', $id, ['form' => &$this]);

    # resolve form plugins
      foreach ($this->children_select_recursive() as $c_npath => $c_child) {
        if ($c_child instanceof form_plugin) {
          $c_npath_parts = explode('/', $c_npath);
          $c_npath_last_part = end($c_npath_parts);
          $c_pointers = core::npath_get_pointers($this, $c_npath);
          if ($c_child->is_available()) $c_pointers[$c_npath_last_part] = $c_child->object_get();
          else                    unset($c_pointers[$c_npath_last_part]);
        }
      }

    # set cform → build → set cform (note: for new items after build)
      foreach ($this->children_select_recursive() as $c_child) if (          $c_child instanceof control                  ) $c_child->cform = $this;
      foreach ($this->children_select_recursive() as $c_child) if (is_object($c_child) && method_exists($c_child, 'build')) $c_child->build();
      foreach ($this->children_select_recursive() as $c_child) if (          $c_child instanceof control                  ) $c_child->cform = $this;

    # call "init" handlers
      $this->items_update();
      event::start('on_form_init', $id, ['form' => &$this, 'items' => &$this->items], /* on_before_step */ null,
        function ($event, $form, $items) { /* on_after_step */
          $form->items_update();
        }
      );

    # if user submit this form (note: dynamic buttons should be inserted before)
      if ($this->is_submitted()) {
        $this->clicked_button = $this->clicked_button_get();
        if ($this->clicked_button) {

        # call "on_request_value_set" method
          if (empty($this->clicked_button->break_on_request_value_set)) {
            foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) {
              if (is_object($c_child) && method_exists($c_child, 'on_request_value_set')) {
                $c_result = event::start_local('on_request_value_set', $c_child, ['form' => $this, 'npath' => $c_npath]);
                console::log_insert('form', 'value_set', $c_npath);
              }
            }
          }

        # call "on_validate" handlers (parent should be at the end)
          if (empty($this->clicked_button->break_on_validate)) {
            foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) if (is_object($c_child) && method_exists($c_child, 'on_validate'        )) {$c_result = event::start_local('on_validate',         $c_child, ['form' => $this, 'npath' => $c_npath]); console::log_insert('form', 'validation_1', $c_npath, $c_result ? 'ok' : 'warning');}
            foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) if (is_object($c_child) && method_exists($c_child, 'on_validate_phase_2')) {$c_result = event::start_local('on_validate_phase_2', $c_child, ['form' => $this, 'npath' => $c_npath]); console::log_insert('form', 'validation_2', $c_npath, $c_result ? 'ok' : 'warning');}
            foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) if (is_object($c_child) && method_exists($c_child, 'on_validate_phase_3')) {$c_result = event::start_local('on_validate_phase_3', $c_child, ['form' => $this, 'npath' => $c_npath]); console::log_insert('form', 'validation_3', $c_npath, $c_result ? 'ok' : 'warning');}
            event::start('on_form_validate', $id, ['form' => &$this, 'items' => &$this->items]);
          }

        # send test headers 'X-Form-Submit-Errors-Count: N' (before a possible redirect)
          if (module::is_enabled('test')) {
            header('X-Form-Submit-Errors-Count: '.count(static::$errors));
          }

        # show errors before submit (before a possible redirect after submit)
          $this->errors_show();

        # call "on_submit" handlers (if no errors)
          if (!$this->has_error()) {
            foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child)
              if (is_object($c_child) && method_exists($c_child, 'on_submit')) {
                event::start_local('on_submit', $c_child, ['form' => $this, 'npath' => $c_npath]); console::log_insert('form', 'submission', $c_npath); }
            event::start('on_form_submit', $id, ['form' => &$this, 'items' => &$this->items]);
          # show errors after call "on_submit" handlers for buttons with 'break_on_validate' (will not be shown if a redirect has occurred)
            $this->errors_show();
          }

        # update or delete validation cache (will not be deleted if redirection has occurred)
          if ($this->validation_cache !== null && $this->validation_cache_is_persistent !== false &&                                core::hash_get($this->validation_cache) !== $this->validation_cache_hash) $this->validation_cache_storage_update();
          if ($this->validation_cache !== null && $this->validation_cache_is_persistent === false && $this->has_error() === true && core::hash_get($this->validation_cache) !== $this->validation_cache_hash) $this->validation_cache_storage_update();
          if ($this->validation_cache !== null && $this->validation_cache_is_persistent === false && $this->has_error() !== true                                                                            ) $this->validation_cache_storage_delete();

        }
      }

      $this->is_builded = true;
    }
  }

  function render() {
    $this->build();
    return parent::render();
  }

  function render_self() {
    if ($this->title && (bool)$this->title_is_visible !== true) return (new markup($this->title_tag_name, $this->title_attributes + ['aria-hidden' => 'true'], $this->title))->render();
    if ($this->title && (bool)$this->title_is_visible === true) return (new markup($this->title_tag_name, $this->title_attributes + [                       ], $this->title))->render();
  }

  # ─────────────────────────────────────────────────────────────────────
  # shared functionality
  # ─────────────────────────────────────────────────────────────────────

  function is_submitted() {
  # check if 'form_id' is match
    if ($this->child_select('hidden_id_form')->value_request_get(0, $this->source_get()) ===
        $this->child_select('hidden_id_form')->value_get()) {
    # check if form 'number' + 'created' + 'ip' + 'uagent' + 'random' is match
      if (static::validation_id_check(static::validation_id_get_raw($this), $this)) {
        return true;
      }
    }
  }

  function id_get() {
    return $this->attribute_select('id');
  }

  function source_get() {
    return $this->attribute_select('method') === 'get' ? '_GET' : '_POST';
  }

  function clicked_button_get() {
    foreach ($this->children_select_recursive() as $c_child) {
      if ($c_child instanceof button &&
          $c_child->is_clicked(0, $this->source_get())) {
        return $c_child;
      }
    }
  }

  function items_update() {
    $this->items = [];
    $groups      = [];
    foreach ($this->children_select_recursive(null, '', true) as $c_npath => $c_child) {
      if ($c_child instanceof container                                      ) $this->items[    $c_npath                                                ] = $c_child;
      if ($c_child instanceof button                                         ) $this->items['~'.$c_child->value_get       ()                            ] = $c_child;
      if ($c_child instanceof field_hidden                                   ) $this->items['!'.$c_child->name_get        ()                            ] = $c_child;
      if ($c_child instanceof field                                          ) $groups     ['#'.$c_child->name_get        ()                          ][] = $c_child;
      if ($c_child instanceof field_radiobutton                              ) $groups     ['#'.$c_child->name_get        ().':'.$c_child->value_get()][] = $c_child;
      if ($c_child instanceof control_complex && $c_child->name_get_complex()) $groups     ['*'.$c_child->name_get_complex()                          ][] = $c_child;
    }
    foreach ($groups as $c_name => $c_group) {
      if (count($c_group) === 1) $this->items[$c_name] = reset($c_group);
      if (count($c_group)  >  1) $this->items[$c_name] =       $c_group;
    }
    return $this->items;
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for errors
  # ─────────────────────────────────────────────────────────────────────

  function has_error() {
    return (bool)count(static::$errors);
  }

  function error_set($message = null, $args = []) {
    $new_error = new \stdClass;
    $new_error->message = $message;
    $new_error->args    = $args;
    $new_error->pointer = &$this;
    static::$errors[] = $new_error;
  }

  function errors_show() {
    if ($this->has_error()) {
      $this->attribute_insert('aria-invalid', 'true');
      foreach (static::$errors as $c_error) {
        switch (gettype($c_error->message)) {
          case 'string': message::insert(new text($c_error->message, $c_error->args), 'error'); break;
          case 'object': message::insert(         $c_error->message,                  'error'); break;
        }
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for validation cache
  # ─────────────────────────────────────────────────────────────────────

  function validation_cache_date_get($format = 'Y-m-d') {
    $timestmp = static::validation_id_extract_created($this->validation_id);
    return \DateTime::createFromFormat('U', $timestmp)->format($format);
  }

  function validation_cache_init() {
    if ($this->validation_cache === null) {
      $instance = (new instance('cache_validation', ['id' => $this->validation_id]))->select();
      $this->validation_cache = $instance ? $instance->data : [];
      $this->validation_cache_hash = core::hash_get($this->validation_cache);
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

  static function validation_cleaning($files_limit = 5000) {
  # delete items from the storage
    entity::get('cache_validation')->instances_delete(['conditions' => [
      'updated_!f' => 'updated',
      'operator'   => '<',
      'updated_!v' => time() - core::date_period_d
    ]]);
  # delete temporary files
    if (file_exists(temporary::directory.'validation/')) {
      $counter = 0;
      foreach (new rd_iterator(temporary::directory.'validation/', file::scan_mode) as $c_dir_path => $c_spl_dir_info) {
        if ($c_spl_dir_info->isDir()) {
          if (core::validate_date($c_spl_dir_info->getFilename()) &&
                                  $c_spl_dir_info->getFilename() < core::date_get()) {
          # try to recursively delete all files and directories in current "YYYY-MM-DD" directory
            foreach (new ri_iterator(new rd_iterator($c_dir_path, file::scan_mode), file::scan_with_dir_at_last) as $c_df_path => $c_spl_dir_or_file_info) {
              if     ($counter >= $files_limit) return;
              if     ($c_spl_dir_or_file_info->isFile()) {@unlink($c_df_path); $counter++;}
              elseif ($c_spl_dir_or_file_info->isDir ()) {@rmdir ($c_df_path);}
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

  static protected $c_form_number = 0;

  static function current_number_generate() {
    return static::$c_form_number++;
  }

  # ─────────────────────────────────────────────────────────────────────
  # functionality for validation_id
  # ─────────────────────────────────────────────────────────────────────

  static function validation_id_generate($form) {
    $hex_number        = static::validation_id_get_hex_number       ($form->number);
    $hex_created       = static::validation_id_get_hex_created      (             );
    $hex_ip            = static::validation_id_get_hex_ip           (             );
    $hex_uagent_hash_8 = static::validation_id_get_hex_uagent_hash_8(             );
    $hex_random        = static::validation_id_get_hex_random       (             );
    $validation_id = $hex_number.        # strlen === 2
                     $hex_created.       # strlen === 8
                     $hex_ip.            # strlen === 32
                     $hex_uagent_hash_8. # strlen === 8
                     $hex_random;        # strlen === 8
    $validation_id.= user::signature_get($validation_id, 'form', 8);
    return $validation_id;
  }

  static function validation_id_get($form) {
    if (static::validation_id_check(static::validation_id_get_raw ($form), $form))
         return                     static::validation_id_get_raw ($form);
    else return                     static::validation_id_generate($form);
  }

  static function validation_id_get_raw($form) {
    $source = $form->source_get();
    global ${$source};
    return ${$source}['validation_id'] ?? '';
  }

  static function validation_id_get_hex_number($number) {return str_pad(dechex($number), 2, '0', STR_PAD_LEFT);}
  static function validation_id_get_hex_created      () {return dechex(time());}
  static function validation_id_get_hex_ip           () {return core::ip_to_hex(request::addr_remote_get());}
  static function validation_id_get_hex_uagent_hash_8() {return core::hash_get_mini(request::user_agent_get());}
  static function validation_id_get_hex_random       () {return str_pad(dechex(random_int(0, PHP_INT_32_MAX)), 8, '0', STR_PAD_LEFT);}
  static function validation_id_get_hex_signature ($id) {return user::signature_get(substr($id, 0, 58), 'form', 8);}

  static function validation_id_extract_number           ($id) {return hexdec(static::validation_id_extract_hex_number ($id));}
  static function validation_id_extract_created          ($id) {return hexdec(static::validation_id_extract_hex_created($id));}
  static function validation_id_extract_hex_number       ($id) {return substr($id,  0,  2);}
  static function validation_id_extract_hex_created      ($id) {return substr($id,  2,  8);}
  static function validation_id_extract_hex_ip           ($id) {return substr($id, 10, 32);}
  static function validation_id_extract_hex_uagent_hash_8($id) {return substr($id, 42,  8);}
  static function validation_id_extract_hex_random       ($id) {return substr($id, 50,  8);}
  static function validation_id_extract_hex_signature    ($id) {return substr($id, 58,  8);}

  static function validation_id_check($id, $form) {
    if (core::validate_hash($id, 66)) {
      $number            = static::validation_id_extract_number           ($id);
      $created           = static::validation_id_extract_created          ($id);
      $hex_ip            = static::validation_id_extract_hex_ip           ($id);
      $hex_uagent_hash_8 = static::validation_id_extract_hex_uagent_hash_8($id);
      $hex_signature     = static::validation_id_extract_hex_signature    ($id);
      if ($created <= time()                                                   &&
          $created >= time() - core::date_period_d                             &&
          $form->number      === $number                                       &&
          $hex_ip            === static::validation_id_get_hex_ip           () &&
          $hex_uagent_hash_8 === static::validation_id_get_hex_uagent_hash_8() &&
          $hex_signature     === static::validation_id_get_hex_signature($id)) {
        return true;
      }
    }
  }

}}