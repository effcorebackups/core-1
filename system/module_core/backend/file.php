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
  # ┌───────────────╥───────┬──────────┬──────┬──────────┐
  # │ path          ║ dirs  │ name     │ type │ relative │
  # ╞═══════════════╬═══════╪══════════╪══════╪══════════╡
  # │ ...           ║       │ ...      │      │ yes      │
  # │ 0             ║       │ 0        │      │ yes      │
  # │ 0.            ║       │ 0.       │      │ yes      │
  # │ 0..           ║       │ 0..      │      │ yes      │
  # │ 0...          ║       │ 0...     │      │ yes      │
  # │ 0             ║       │ 0        │      │ yes      │
  # │ .0            ║       │          │ 0    │ yes      │
  # │ ..0           ║       │ .        │ 0    │ yes      │
  # │ ...0          ║       │ ..       │ 0    │ yes      │
  # │ 01            ║       │ 01       │      │ yes      │
  # │ 0.1           ║       │ 0        │ 1    │ yes      │
  # │ 0..1          ║       │ 0.       │ 1    │ yes      │
  # │ 0...1         ║       │ 0..      │ 1    │ yes      │
  # │ 10            ║       │ 10       │      │ yes      │
  # │ 1.0           ║       │ 1        │ 0    │ yes      │
  # │ 1..0          ║       │ 1.       │ 0    │ yes      │
  # │ 1...0         ║       │ 1..      │ 0    │ yes      │
  # │ .0.           ║       │ .0.      │      │ yes      │
  # │ ..0..         ║       │ ..0..    │      │ yes      │
  # │ .0.1.         ║       │ .0.1.    │      │ yes      │
  # │ ..0.1..       ║       │ ..0.1..  │      │ yes      │
  # │ ..0..1..      ║       │ ..0..1.. │      │ yes      │
  # │ .1.0.         ║       │ .1.0.    │      │ yes      │
  # │ ..1.0..       ║       │ ..1.0..  │      │ yes      │
  # │ ..1..0..      ║       │ ..1..0.. │      │ yes      │
  # │ /...          ║ /     │ ...      │      │          │
  # │ /0            ║ /     │ 0        │      │          │
  # │ /0.           ║ /     │ 0.       │      │          │
  # │ /0..          ║ /     │ 0..      │      │          │
  # │ /0...         ║ /     │ 0...     │      │          │
  # │ /0            ║ /     │ 0        │      │          │
  # │ /.0           ║ /     │          │ 0    │          │
  # │ /..0          ║ /     │ .        │ 0    │          │
  # │ /...0         ║ /     │ ..       │ 0    │          │
  # │ /01           ║ /     │ 01       │      │          │
  # │ /0.1          ║ /     │ 0        │ 1    │          │
  # │ /0..1         ║ /     │ 0.       │ 1    │          │
  # │ /0...1        ║ /     │ 0..      │ 1    │          │
  # │ /10           ║ /     │ 10       │      │          │
  # │ /1.0          ║ /     │ 1        │ 0    │          │
  # │ /1..0         ║ /     │ 1.       │ 0    │          │
  # │ /1...0        ║ /     │ 1..      │ 0    │          │
  # │ /.0.          ║ /     │ .0.      │      │          │
  # │ /..0..        ║ /     │ ..0..    │      │          │
  # │ /.0.1.        ║ /     │ .0.1.    │      │          │
  # │ /..0.1..      ║ /     │ ..0.1..  │      │          │
  # │ /..0..1..     ║ /     │ ..0..1.. │      │          │
  # │ /.1.0.        ║ /     │ .1.0.    │      │          │
  # │ /..1.0..      ║ /     │ ..1.0..  │      │          │
  # │ /..1..0..     ║ /     │ ..1..0.. │      │          │
  # │ dir/...       ║ dir/  │ ...      │      │ yes      │
  # │ dir/0         ║ dir/  │ 0        │      │ yes      │
  # │ dir/0.        ║ dir/  │ 0.       │      │ yes      │
  # │ dir/0..       ║ dir/  │ 0..      │      │ yes      │
  # │ dir/0...      ║ dir/  │ 0...     │      │ yes      │
  # │ dir/0         ║ dir/  │ 0        │      │ yes      │
  # │ dir/.0        ║ dir/  │          │ 0    │ yes      │
  # │ dir/..0       ║ dir/  │ .        │ 0    │ yes      │
  # │ dir/...0      ║ dir/  │ ..       │ 0    │ yes      │
  # │ dir/01        ║ dir/  │ 01       │      │ yes      │
  # │ dir/0.1       ║ dir/  │ 0        │ 1    │ yes      │
  # │ dir/0..1      ║ dir/  │ 0.       │ 1    │ yes      │
  # │ dir/0...1     ║ dir/  │ 0..      │ 1    │ yes      │
  # │ dir/10        ║ dir/  │ 10       │      │ yes      │
  # │ dir/1.0       ║ dir/  │ 1        │ 0    │ yes      │
  # │ dir/1..0      ║ dir/  │ 1.       │ 0    │ yes      │
  # │ dir/1...0     ║ dir/  │ 1..      │ 0    │ yes      │
  # │ dir/.0.       ║ dir/  │ .0.      │      │ yes      │
  # │ dir/..0..     ║ dir/  │ ..0..    │      │ yes      │
  # │ dir/.0.1.     ║ dir/  │ .0.1.    │      │ yes      │
  # │ dir/..0.1..   ║ dir/  │ ..0.1..  │      │ yes      │
  # │ dir/..0..1..  ║ dir/  │ ..0..1.. │      │ yes      │
  # │ dir/.1.0.     ║ dir/  │ .1.0.    │      │ yes      │
  # │ dir/..1.0..   ║ dir/  │ ..1.0..  │      │ yes      │
  # │ dir/..1..0..  ║ dir/  │ ..1..0.. │      │ yes      │
  # │ /dir/...      ║ /dir/ │ ...      │      │          │
  # │ /dir/0        ║ /dir/ │ 0        │      │          │
  # │ /dir/0.       ║ /dir/ │ 0.       │      │          │
  # │ /dir/0..      ║ /dir/ │ 0..      │      │          │
  # │ /dir/0...     ║ /dir/ │ 0...     │      │          │
  # │ /dir/0        ║ /dir/ │ 0        │      │          │
  # │ /dir/.0       ║ /dir/ │          │ 0    │          │
  # │ /dir/..0      ║ /dir/ │ .        │ 0    │          │
  # │ /dir/...0     ║ /dir/ │ ..       │ 0    │          │
  # │ /dir/01       ║ /dir/ │ 01       │      │          │
  # │ /dir/0.1      ║ /dir/ │ 0        │ 1    │          │
  # │ /dir/0..1     ║ /dir/ │ 0.       │ 1    │          │
  # │ /dir/0...1    ║ /dir/ │ 0..      │ 1    │          │
  # │ /dir/10       ║ /dir/ │ 10       │      │          │
  # │ /dir/1.0      ║ /dir/ │ 1        │ 0    │          │
  # │ /dir/1..0     ║ /dir/ │ 1.       │ 0    │          │
  # │ /dir/1...0    ║ /dir/ │ 1..      │ 0    │          │
  # │ /dir/.0.      ║ /dir/ │ .0.      │      │          │
  # │ /dir/..0..    ║ /dir/ │ ..0..    │      │          │
  # │ /dir/.0.1.    ║ /dir/ │ .0.1.    │      │          │
  # │ /dir/..0.1..  ║ /dir/ │ ..0.1..  │      │          │
  # │ /dir/..0..1.. ║ /dir/ │ ..0..1.. │      │          │
  # │ /dir/.1.0.    ║ /dir/ │ .1.0.    │      │          │
  # │ /dir/..1.0..  ║ /dir/ │ ..1.0..  │      │          │
  # │ /dir/..1..0.. ║ /dir/ │ ..1..0.. │      │          │
  # └───────────────╨───────┴──────────┴──────┴──────────┘

  # wrong paths:
  # ┌────────────────╥────────────────────────────────────────────┐
  # │ path           ║ behavior                                   │
  # ╞════════════════╬════════════════════════════════════════════╡
  # │ c:\dir\        ║ should be converted to c:/dir/             │
  # │ dir\           ║ should be converted to dir/                │
  # │ \dir\          ║ should be converted to /dir/               │
  # │ \\dir\         ║ should be ignored                          │
  # │ ~/dir/         ║ should be ignored or use realpath() before │
  # │ ./dir/         ║ should be ignored or use realpath() before │
  # │ ../dir/        ║ should be ignored or use realpath() before │
  # │ /dir1/../dir3/ ║ should be ignored or use realpath() before │
  # │ dir            ║ interpreted as file with name 'dir'        │
  # │ dir1/dir2      ║ interpreted as file with name 'dir2'       │
  # └────────────────╨────────────────────────────────────────────┘

  # note:
  # ══════════════════════════════════════════════════════════════════════════════════════════
  # 1. only files with extension are available in the URL!
  # 2. if the first character in the path is '/' - it's a full path, оtherwise - relative path
  # 3. if the last  character in the path is '/' - it's a directory, оtherwise - file
  # 4. path components like  '~/' should be ignored or use realpath() to resolve the path
  # 5. path components like  './' should be ignored or use realpath() to resolve the path
  # 6. path components like '../' should be ignored or use realpath() to resolve the path
  # ──────────────────────────────────────────────────────────────────────────────────────────

  const scan_mode     = fs_iterator::UNIX_PATHS | fs_iterator::SKIP_DOTS;
  const scan_with_dir = ri_iterator::SELF_FIRST;

  public $dirs;
  public $name;
  public $type;
  public $data;

  function __construct($path) {
    $this->parse($path);
  }

  function parse($path) {
    $info = static::path_parse($path);
    if ($info) {
      $this->dirs = $info->dirs;
      $this->name = $info->name;
      $this->type = $info->type;
    } else {
      console::log_insert('file', 'init', 'Invalid file path "%%_path"!', 'error', 0, ['path' => $path]);
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # work with dirs
  # ─────────────────────────────────────────────────────────────────────

  function dirs_get()          {return $this->dirs;}
  function dirs_get_parts()    {return explode('/', trim($this->dirs, '/'));}
  function dirs_get_relative() {return $this->is_path_full() ? substr($this->dirs, strlen(dir_root)) : $this->dirs;}

  function dirs_set($dirs) {
    $this->dirs = $dirs;
  }

  # ─────────────────────────────────────────────────────────────────────
  # work with file (name + '.' + type)
  # ─────────────────────────────────────────────────────────────────────

  function name_get() {return $this->name;}
  function name_set($name) {$this->name = $name;}

  function type_get() {return $this->type;}
  function type_set($type) {$this->type = $type;}

  function file_get() {
    return strlen($this->type) ?
      $this->name.'.'.$this->type :
      $this->name;
  }

  # ─────────────────────────────────────────────────────────────────────
  # work with path (dirs/ + name + '.' + type)
  # ─────────────────────────────────────────────────────────────────────

  function path_get() {
    return strlen($this->type) ?
      $this->dirs.$this->name.'.'.$this->type :
      $this->dirs.$this->name;
  }

  function path_get_relative() {
    return $this->dirs_get_relative().
           $this->file_get();
  }

  # ─────────────────────────────────────────────────────────────────────
  # work with file data
  # ─────────────────────────────────────────────────────────────────────

  function data_get() {
    if (empty($this->data)) $this->load(true);
    return $this->data;
  }

  function data_set($data) {
    $this->data = $data;
  }

  # ─────────────────────────────────────────────────────────────────────

  function parent_name_get() {return ltrim(strrchr(rtrim($this->dirs, '/'), '/'), '/');}
  function hash_get()        {return @md5_file($this->path_get());}
  function size_get()        {return @filesize($this->path_get());}
  function mime_get()        {return function_exists('mime_content_type') ? @mime_content_type($this->path_get()) : null;}

  # ─────────────────────────────────────────────────────────────────────

  function is_path_full() {
    if (DIRECTORY_SEPARATOR != '\\') return isset($this->dirs[0]) && $this->dirs[0] == '/';
    if (DIRECTORY_SEPARATOR == '\\') return isset($this->dirs[1]) && $this->dirs[1] == ':';
  }

  function is_exist() {
    return file_exists($this->path_get());
  }

  # ─────────────────────────────────────────────────────────────────────

  function load($reset = false) {
    $relative = $this->path_get_relative();
    timer::tap('file load: '.$relative);
    if (!$reset && isset(static::$cache_data[$relative]))
           $this->data = static::$cache_data[$relative];
    else   $this->data = static::$cache_data[$relative] = @file_get_contents($this->path_get());
    timer::tap('file load: '.$relative);
    console::log_insert('file', 'load', $relative, 'ok',
      timer::get_period('file load: '.$relative, -1, -2)
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
    $relative = $this->path_get_relative();
    timer::tap('file insert: '.$relative);
    $result = $once ? require_once($this->path_get()) :
                           require($this->path_get());
    timer::tap('file insert: '.$relative);
    console::log_insert('file', 'insertion', $relative, 'ok',
      timer::get_period('file insert: '.$relative, -1, -2)
    );
    return $result;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache_data;
  static protected $cache_file_types;

  static function cache_cleaning() {
    static::$cache_data       = null;
    static::$cache_file_types = null;
  }

  static function init() {
    foreach (storage::get('files')->select('file_types') as $c_module_id => $c_file_types) {
      foreach ($c_file_types as $c_row_id => $c_file_type) {
        if (isset(static::$cache_file_types[$c_file_type->type])) console::log_insert_about_duplicate('file_types', $c_file_type->type, $c_module_id);
        static::$cache_file_types[$c_file_type->type] = $c_file_type;
        static::$cache_file_types[$c_file_type->type]->module_id = $c_module_id;
      }
    }
  }

  static function path_parse($path, $skip_not_file = true) {
  # each path should not end with '/' and have at least one more character
    if (strlen($path) == 0 || ($skip_not_file && $path[strlen($path) - 1] == '/')) return;
    $path = rtrim($path, '/');
    $result = new \stdClass;
    $result->dirs = '';
    $result->name = '';
    $result->type = '';
    $full_name = substr(strrchr($path, '/'), 1);
    if ($full_name === false) $full_name = $path;
    if ($full_name === '' || $full_name === '..' || $full_name === '.') return;
    $result->dirs = substr($path, 0, - strlen($full_name));
    $type = substr(strrchr($full_name, '.'), 1);
    if ($type !== false &&
        $type !== '') {
      $result->type = $type;
      $result->name = substr($full_name, 0, - strlen($type) - 1); } else {
      $result->name = $full_name;
    }
    return $result;
  }

  static function types_get() {
    if    (static::$cache_file_types == null) static::init();
    return static::$cache_file_types;
  }

  static function mkdir_if_not_exist($dirs, $mode = 0777) {
    return !file_exists($dirs) ?
                 @mkdir($dirs, $mode, true) : true;
  }

  static function select_recursive($path, $filter = '', $with_dirs = false) {
    try {
      $result = [];
      $scan = $with_dirs ? new ri_iterator(new rd_iterator($path, static::scan_mode), static::scan_with_dir) :
                           new ri_iterator(new rd_iterator($path, static::scan_mode));
      foreach ($scan as $c_path => $spl_file_info) {
        if (!$filter || ($filter && preg_match($filter, $c_path))) {
          if     ($spl_file_info->isFile()) $result[$c_path] = new static($c_path);
          elseif ($spl_file_info->isDir ()) $result[$c_path] = $c_path;
        }
      }
      krsort($result);
      return $result;
    } catch (\UnexpectedValueException $e) {
      return [];
    }
  }

}}