<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class file_uploaded {

  public $name;
  public $type;
  public $file;
  public $mime;
  public $tmp_path; # file in PHP    'tmp'   directory
  public $pre_path; # file in system 'tmp'   directory
  public $new_path; # file in system 'files' directory
  public $old_path; # file in system 'files' directory (p.s. it's new_path after saving files)
  public $error;
  public $size;

  function get_current_path() {
    if     (!empty($this->tmp_path)) return $this->tmp_path;
    elseif (!empty($this->pre_path)) return $this->pre_path;
    elseif (!empty($this->new_path)) return $this->new_path;
    elseif (!empty($this->old_path)) return $this->old_path;
  }

  function get_current_state() {
    if     (!empty($this->tmp_path)) return 'tmp';
    elseif (!empty($this->pre_path)) return 'pre';
    elseif (!empty($this->new_path)) return 'new';
    elseif (!empty($this->old_path)) return 'old';
  }

  # ─────────────────────────────────────────────────────────────────────

  function init_from_old($path_relative) {
    $file = new file(dir_root.$path_relative);
    if ($file->is_exist()) {
      $this->name     = $file->name_get();
      $this->type     = $file->type_get();
      $this->file     = $file->file_get();
      $this->mime     = $file->mime_get();
      $this->size     = $file->size_get();
      $this->old_path = $file->path_get();
      $this->error    = 0;
      return true;
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function move_tmp_to_pre($dst_path) {
    if (isset($this->tmp_path)) {
      $src_file = new file($this->tmp_path);
      $dst_file = new file($dst_path);
      if ($src_file->move_uploaded($dst_file->dirs_get(), $dst_file->file_get())) {
              $this->pre_path = $dst_file->path_get();
        unset($this->tmp_path);
        return true;
      } else {
        message::insert(new text_multiline(['Can not copy file from "%%_from" to "%%_to"!', 'Check directory permissions.'], ['from' => $src_file->dirs_get(), 'to' => $dst_file->dirs_get_relative()]), 'error');
        console::log_insert('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0,                      ['from' => $src_file->dirs_get(), 'to' => $dst_file->dirs_get_relative()]);
      }
    }
  }

  function move_pre_to_new($dst_path, $fixed_name = null, $fixed_type = null) {
    if (isset($this->pre_path)) {
      $src_file = new file($this->pre_path);
      $dst_file = new file($dst_path);
      if ($fixed_name          ) $dst_file->name_set(token::apply($fixed_name));
      if ($fixed_type          ) $dst_file->type_set(token::apply($fixed_type));
      if ($dst_file->is_exist()) $dst_file->name_set($dst_file->name_get().'-'.core::random_part_get());
      if ($src_file->move($dst_file->dirs_get(), $dst_file->file_get())) {
              $this->new_path = $dst_file->path_get();
        unset($this->pre_path);
        return true;
      } else {
        message::insert(new text_multiline(['Can not copy file from "%%_from" to "%%_to"!', 'Check directory permissions.'], ['from' => $src_file->dirs_get_relative(), 'to' => $dst_file->dirs_get_relative()]), 'error');
        console::log_insert('file', 'copy', 'Can not copy file from "%%_from" to "%%_to"!', 'error', 0,                      ['from' => $src_file->dirs_get_relative(), 'to' => $dst_file->dirs_get_relative()]);
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function delete_tmp() {
  # PHP deletes such files immediately after processing the POST request
    return true;
  }

  function delete_pre() {
    if (isset($this->pre_path)) {
      $result = @unlink($this->pre_path);
      if ($result) {
        unset($this->pre_path);
        return true;
      } else {
        message::insert(new text_multiline(['Can not delete file "%%_file"!', 'Check directory permissions.'], ['file' => (new file($this->pre_path))->path_get_relative()]), 'error');
        console::log_insert('file', 'copy', 'Can not delete file "%%_file"!', 'error', 0,                      ['file' => (new file($this->pre_path))->path_get_relative()]);
      }
    }
  }

  function delete_old() { # 'new' after saving file turns into → 'old'
    if (isset($this->old_path)) {
      $result = @unlink($this->old_path);
      if ($result) {
        unset($this->old_path);
        return true;
      } else {
        message::insert(new text_multiline(['Can not delete file "%%_file"!', 'Check directory permissions.'], ['file' => (new file($this->old_path))->path_get_relative()]), 'error');
        console::log_insert('file', 'copy', 'Can not delete file "%%_file"!', 'error', 0,                      ['file' => (new file($this->old_path))->path_get_relative()]);
      }
    }
  }

  # ─────────────────────────────────────────────────────────────────────

  function sanitize_tmp($allowed_characters = 'a-zA-Z0-9_\\-\\.', $max_length_name = 227, $max_length_type = 10) {
    $this->name = core::sanitize_file_part($this->name, $allowed_characters, $max_length_name);
    $this->type = core::sanitize_file_part($this->type, $allowed_characters, $max_length_type);
    if (!strlen($this->name)) $this->name = core::random_part_get();
    if (!strlen($this->type)) $this->type = 'unknown';
    $this->file = $this->name.'.'.$this->type;
  # special case for IIS, Apache, NGINX
    if ($this->file == 'web.config' || $this->type == 'htaccess' || $this->type == 'nginx') {
      $this->name = core::random_part_get();
      $this->type = 'unknown';
      $this->file = $this->name.'.'.$this->type;
    }
  }

}}