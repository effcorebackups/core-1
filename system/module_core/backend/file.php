<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \FilesystemIterator as fs_iterator;
          use \RecursiveDirectoryIterator as rd_iterator;
          use \RecursiveIteratorIterator as ri_iterator;
          class file {

  # valid paths (path = dirs/ + name + '.' + type):
  # ┌─────────────────────────╥───────────────┬─────────┬──────┬──────────┐
  # │ path                    ║ dirs/         │ name    │ type │ relative │
  # ╞═════════════════════════╬═══════════════╪═════════╪══════╪══════════╡
  # │ .fiLe                   ║               │ .fiLe   │      │ -        │
  # │ .fiLe.eXt               ║               │ .fiLe   │ eXt  │ -        │
  # │ fiLe                    ║               │ fiLe    │      │ -        │
  # │ fiLe.eXt                ║               │ fiLe    │ eXt  │ -        │
  # │ fiLe.0.eXt              ║               │ fiLe.0  │ eXt  │ -        │
  # │ diR.1/                  ║ diR.1/        │         │      │ yes      │
  # │ diR.1/диР.2/            ║ diR.1/диР.2/  │         │      │ yes      │
  # │ diR.1/диР.2/.fiLe       ║ diR.1/диР.2/  │ .fiLe   │      │ yes      │
  # │ diR.1/диР.2/.fiLe.eXt   ║ diR.1/диР.2/  │ .fiLe   │ eXt  │ yes      │
  # │ diR.1/диР.2/fiLe        ║ diR.1/диР.2/  │ fiLe    │      │ yes      │
  # │ diR.1/диР.2/fiLe.eXt    ║ diR.1/диР.2/  │ fiLe    │ eXt  │ yes      │
  # │ diR.1/диР.2/fiLe.0.eXt  ║ diR.1/диР.2/  │ fiLe.0  │ eXt  │ yes      │
  # │ /diR.1/                 ║ /diR.1/       │         │      │ no       │
  # │ /diR.1/диР.2/           ║ /diR.1/диР.2/ │         │      │ no       │
  # │ /diR.1/диР.2/.fiLe      ║ /diR.1/диР.2/ │ .fiLe   │      │ no       │
  # │ /diR.1/диР.2/.fiLe.eXt  ║ /diR.1/диР.2/ │ .fiLe   │ eXt  │ no       │
  # │ /diR.1/диР.2/fiLe       ║ /diR.1/диР.2/ │ fiLe    │      │ no       │
  # │ /diR.1/диР.2/fiLe.eXt   ║ /diR.1/диР.2/ │ fiLe    │ eXt  │ no       │
  # │ /diR.1/диР.2/fiLe.0.eXt ║ /diR.1/диР.2/ │ fiLe.0  │ eXt  │ no       │
  # └─────────────────────────╨───────────────┴─────────┴──────┴──────────┘

  # wrong paths:
  # ┌─────────────────────────╥────────────────────────────────────────────────┐
  # │ path                    ║ behavior                                       │
  # ╞═════════════════════════╬════════════════════════════════════════════════╡
  # │ c:\dir1                 ║ should be converted to c:/dir1                 │
  # │ \dir1                   ║ should be converted to /dir1                   │
  # │ dir1\                   ║ should be converted to dir/                    │
  # │ ./dir1                  ║ should be ignored                              │
  # │ ../dir1/                ║ should be ignored                              │
  # │ /dir1/../dir3/          ║ should be ignored                              │
  # │ dir1                    ║ should be ignored (interpreted as: file1)      │
  # │ dir1/dir2               ║ should be ignored (interpreted as: dir1/file2) │
  # └─────────────────────────╨────────────────────────────────────────────────┘

  # note:
  # ══════════════════════════════════════════════════════════════════════════════════════════
  # 1. if the first character in the path is '/' - it's a full path, оtherwise - relative path
  # 2. if the last character in the path is '/' - it's a directory, оtherwise - file
  # 3. path components like '../' should be ignored!
  # 4. path components like './' should be ignored!
  # 5. the Windows file path should be convert to UNIX format!
  # ──────────────────────────────────────────────────────────────────────────────────────────

  const scan_dir_mode = fs_iterator::UNIX_PATHS | fs_iterator::SKIP_DOTS;

  public $dirs;
  public $name;
  public $type;
  public $data;

  function __construct($path) {
    $this->parse($path);
  }

  function parse($path) {
    $matches = [];
    preg_match('%^(?<dirs>.*/|)'.
                 '(?<name>.+?|)'.
                 '(?<type>[.][^.]+|)$%S', $path, $matches);
    $this->dirs = $matches['dirs'] ?? '';
    $this->name = $matches['name'] ?? '';
    $this->type = isset($matches['type']) ? ltrim($matches['type'], '.') : '';
  }

  function dirs_set($dirs) {$this->dirs = $dirs;}
  function name_set($name) {$this->name = $name;}
  function type_set($type) {$this->type = $type;}
  function data_set($data) {$this->data = $data;}

  function dirs_get()          {return $this->dirs;}
  function dirs_parts_get()    {return explode('/', trim($this->dirs, '/'));}
  function dirs_relative_get() {return $this->is_path_full() ? substr($this->dirs, strlen(dir_root)) : $this->dirs;}
# ─────────────────────────────────────────────────────────────────────
  function name_get()          {return $this->name;}
  function type_get()          {return $this->type;}
  function file_get()          {return $this->type ? $this->name.'.'.$this->type : $this->name;}
# ─────────────────────────────────────────────────────────────────────
  function path_get()          {return $this->type ? $this->dirs.$this->name.'.'.$this->type : $this->dirs.$this->name;}
  function path_relative_get() {return $this->dirs_relative_get().$this->file_get();}

  function parent_name_get()   {return ltrim(strrchr(rtrim($this->dirs, '/'), '/'), '/');}
  function hash_get()          {return @md5_file($this->path_get());}
  function size_get()          {return @filesize($this->path_get());}
  function mime_get()          {return @mime_content_type($this->path_get());}
  function data_get() {
    if (empty($this->data)) $this->load(true);
    return $this->data;
  }

  function is_path_full() {
    if (DIRECTORY_SEPARATOR != '\\') return isset($this->dirs[0]) && $this->dirs[0] == '/';
    if (DIRECTORY_SEPARATOR == '\\') return isset($this->dirs[1]) && $this->dirs[1] == ':';
  }

  function is_exist() {
    return file_exists($this->path_get());
  }

  function load($reset = false) {
    $relative = $this->path_relative_get();
    timer::tap('file load: '.$relative);
    if (!$reset && isset(static::$cache_data[$relative]))
           $this->data = static::$cache_data[$relative];
    else   $this->data = static::$cache_data[$relative] = @file_get_contents($this->path_get());
    timer::tap('file load: '.$relative);
    console::log_add('file', 'load', $relative, 'ok',
      timer::period_get('file load: '.$relative, -1, -2)
    );
    return $this->data;
  }

  function save() {
    static::mkdir_if_not_exist($this->dirs_get());
    return  @file_put_contents($this->path_get(), $this->data);
  }

  function append_direct($data) {
    static::mkdir_if_not_exist($this->dirs_get());
    return  @file_put_contents($this->path_get(), $data, FILE_APPEND);
  }

  function copy($new_dirs, $new_name = null) {
    $path_old = $this->path_get();
    $path_new = $new_dirs.($new_name ?: $this->file_get());
    static::mkdir_if_not_exist($new_dirs);
    if (@copy($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function move($new_dirs, $new_name = null) {
    $path_old = $this->path_get();
    $path_new = $new_dirs.($new_name ?: $this->file_get());
    static::mkdir_if_not_exist($new_dirs);
    if (@rename($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function move_uploaded($new_dirs, $new_name = null) {
    $path_old = $this->path_get();
    $path_new = $new_dirs.($new_name ?: $this->file_get());
    static::mkdir_if_not_exist($new_dirs);
    if (@move_uploaded_file($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function rename($new_name) {
    $path_old = $this->path_get();
    $path_new = $this->dirs_get().$new_name;
    if (@rename($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function insert($once = true) {
    $relative = $this->path_relative_get();
    timer::tap('file insert: '.$relative);
    $result = $once ? require_once($this->path_get()) :
                           require($this->path_get());
    timer::tap('file insert: '.$relative);
    console::log_add('file', 'insertion', $relative, 'ok',
      timer::period_get('file insert: '.$relative, -1, -2)
    );
    return $result;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache_data;
  static protected $cache_file_types;

  static function init() {
    foreach (storage::get('files')->select('file_types') as $c_module_id => $c_file_types) {
      foreach ($c_file_types as $c_row_id => $c_file_type) {
        if (isset(static::$cache_file_types[$c_file_type->type])) console::log_about_duplicate_add('file_types', $c_file_type->type);
        static::$cache_file_types[$c_file_type->type] = $c_file_type;
        static::$cache_file_types[$c_file_type->type]->module_id = $c_module_id;
      }
    }
  }

  static function types_get() {
    if   (!static::$cache_file_types) static::init();
    return static::$cache_file_types;
  }

  static function mkdir_if_not_exist($dirs) {
    return !file_exists($dirs) ?
                 @mkdir($dirs, 0777, true) : true;
  }

  static function select_recursive($path, $filter = '') {
    try {
      $result = [];
      foreach (new ri_iterator(new rd_iterator($path, static::scan_dir_mode)) as $c_path => $null) {
        if (!$filter || ($filter && preg_match($filter, $c_path))) {
          $result[$c_path] = new static($c_path);
        }
      }
      return $result;
    } catch (\UnexpectedValueException $e) {
      return [];
    }
  }

}}