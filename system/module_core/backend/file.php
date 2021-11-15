<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \FilesystemIterator as fs_iterator;
          use \RecursiveDirectoryIterator as rd_iterator;
          use \RecursiveIteratorIterator as ri_iterator;
          class file {

  # valid paths (path = protocol + dirs + name + type):
  # ┌──────────────────────────────╥────────────┬──────────┬────────┬────────┬─────────────┐
  # │ path                         ║ protocol   │ dirs     │ name   │ type   │ is relative │
  # ╞══════════════════════════════╬════════════╪══════════╪════════╪════════╪═════════════╡
  # │ ''                           ║ NULL                                                  │
  # │ 'name'                       ║ ''         │ ''       │ 'name' │ ''     │ +           │
  # │ '.type'                      ║ ''         │ ''       │ ''     │ 'type' │ +           │
  # │ 'name.type'                  ║ ''         │ ''       │ 'name' │ 'type' │ +           │
  # │ '/'                          ║ NULL                                                  │
  # │ '/name'                      ║ ''         │ '/'      │ 'name' │ ''     │             │
  # │ '/.type'                     ║ ''         │ '/'      │ ''     │ 'type' │             │
  # │ '/name.type'                 ║ ''         │ '/'      │ 'name' │ 'type' │             │
  # │ 'dirs/'                      ║ NULL                                                  │
  # │ 'dirs/name'                  ║ ''         │ 'dirs/'  │ 'name' │ ''     │ +           │
  # │ 'dirs/.type'                 ║ ''         │ 'dirs/'  │ ''     │ 'type' │ +           │
  # │ 'dirs/name.type'             ║ ''         │ 'dirs/'  │ 'name' │ 'type' │ +           │
  # │ '/dirs/'                     ║ NULL                                                  │
  # │ '/dirs/name'                 ║ ''         │ '/dirs/' │ 'name' │ ''     │             │
  # │ '/dirs/.type'                ║ ''         │ '/dirs/' │ ''     │ 'type' │             │
  # │ '/dirs/name.type'            ║ ''         │ '/dirs/' │ 'name' │ 'type' │             │
  # │ 'protocol://'                ║ NULL                                                  │
  # │ 'protocol://name'            ║ 'protocol' │ ''       │ 'name' │ ''     │ +           │
  # │ 'protocol://.type'           ║ 'protocol' │ ''       │ ''     │ 'type' │ +           │
  # │ 'protocol://name.type'       ║ 'protocol' │ ''       │ 'name' │ 'type' │ +           │
  # │ 'protocol:///'               ║ NULL                                                  │
  # │ 'protocol:///name'           ║ 'protocol' │ '/'      │ 'name' │ ''     │             │
  # │ 'protocol:///.type'          ║ 'protocol' │ '/'      │ ''     │ 'type' │             │
  # │ 'protocol:///name.type'      ║ 'protocol' │ '/'      │ 'name' │ 'type' │             │
  # │ 'protocol://dirs/'           ║ NULL                                                  │
  # │ 'protocol://dirs/name'       ║ 'protocol' │ 'dirs/'  │ 'name' │ ''     │ +           │
  # │ 'protocol://dirs/.type'      ║ 'protocol' │ 'dirs/'  │ ''     │ 'type' │ +           │
  # │ 'protocol://dirs/name.type'  ║ 'protocol' │ 'dirs/'  │ 'name' │ 'type' │ +           │
  # │ 'protocol:///dirs/'          ║ NULL                                                  │
  # │ 'protocol:///dirs/name'      ║ 'protocol' │ '/dirs/' │ 'name' │ ''     │             │
  # │ 'protocol:///dirs/.type'     ║ 'protocol' │ '/dirs/' │ ''     │ 'type' │             │
  # │ 'protocol:///dirs/name.type' ║ 'protocol' │ '/dirs/' │ 'name' │ 'type' │             │
  # └──────────────────────────────╨────────────┴──────────┴────────┴────────┴─────────────┘

  # wrong paths:
  # ┌────────────────╥─────────────────────────────────────────────────────┐
  # │ path           ║ behavior                                            │
  # ╞════════════════╬═════════════════════════════════════════════════════╡
  # │ c:\dir\        ║ should be converted to c:/dir/                      │
  # │ dir\           ║ should be converted to dir/                         │
  # │ \dir\          ║ should be converted to /dir/                        │
  # │ \\dir\         ║ should be ignored or use function 'realpath' before │
  # │ ~/dir/         ║ should be ignored or use function 'realpath' before │
  # │ ./dir/         ║ should be ignored or use function 'realpath' before │
  # │ ../dir/        ║ should be ignored or use function 'realpath' before │
  # │ /dir1/../dir3/ ║ should be ignored or use function 'realpath' before │
  # │ dir            ║ interpreted as file with name 'dir'                 │
  # │ dir1/dir2      ║ interpreted as file with name 'dir2'                │
  # └────────────────╨─────────────────────────────────────────────────────┘

  # ───────────────────────────────────────────────────────────────────────────────────────────────
  # note:
  # ═══════════════════════════════════════════════════════════════════════════════════════════════
  # 1. only files with extension are available in the URL!
  # 2. if the first character in the path is '/' - it is a absolute path, otherwise - relative path
  # 3. if the last  character in the path is '/' - it is a directory, otherwise - file
  # 4. path components like  '~/' should be ignored or use function 'realpath' to resolve the path
  # 5. path components like  './' should be ignored or use function 'realpath' to resolve the path
  # 6. path components like '../' should be ignored or use function 'realpath' to resolve the path
  # ───────────────────────────────────────────────────────────────────────────────────────────────

  const scan_mode              = fs_iterator::UNIX_PATHS|fs_iterator::SKIP_DOTS;
  const scan_with_dir_at_first = ri_iterator::SELF_FIRST;
  const scan_with_dir_at_last  = ri_iterator::CHILD_FIRST;

  public $protocol;
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
      $this->protocol = $info->protocol;
      $this->dirs     = $info->dirs;
      $this->name     = $info->name;
      $this->type     = $info->type;
    }
  }

  # ─────────────────────────────────────────────────────────────────────
  # work with dirs
  # ─────────────────────────────────────────────────────────────────────

  function dirs_get()          {return $this->dirs;}
  function dirs_get_parts()    {return explode('/', trim($this->dirs, '/'));}
  function dirs_get_absolute() {return $this->is_path_absolute() ?        $this->dirs :       dir_root.    $this->dirs;}
  function dirs_get_relative() {return $this->is_path_absolute() ? substr($this->dirs, strlen(dir_root)) : $this->dirs;}

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
  # work with path (protocol + '://' + dirs + name + '.' + type)
  # ─────────────────────────────────────────────────────────────────────

  function path_get() {
    return (strlen($this->protocol) ?
                   $this->protocol.'://' : '').
                   $this->dirs.
                   $this->file_get();
  }

  function path_get_absolute() {
    return (strlen($this->protocol) ?
                   $this->protocol.'://' : '').
                   $this->dirs_get_absolute().
                   $this->file_get();
  }

  function path_get_relative() {
    return (strlen($this->protocol) ?
                   $this->protocol.'://' : '').
                   $this->dirs_get_relative().
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

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function parent_get_name() {return ltrim(strrchr(rtrim($this->dirs, '/'), '/'), '/');}
  function hash_get()        {return @md5_file($this->path_get());}
  function size_get()        {return @filesize($this->path_get());}
  function mime_get()        {return function_exists('mime_content_type') ? @mime_content_type($this->path_get()) : null;}

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function is_path_absolute() {
    if (DIRECTORY_SEPARATOR !== '\\') return isset($this->dirs[0]) && $this->dirs[0] === '/';
    if (DIRECTORY_SEPARATOR === '\\') return isset($this->dirs[1]) && $this->dirs[1] === ':';
  }

  function is_exists() {
    return file_exists($this->path_get());
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function load($reset = false) {
    $relative = $this->path_get_relative();
    timer::tap('file load: '.$relative);
    if (!$reset && isset(static::$cache_data[$relative]))
         $this->data  =  static::$cache_data[$relative];
    else $this->data  =  static::$cache_data[$relative] = @file_get_contents($this->path_get());
    timer::tap('file load: '.$relative);
    console::log_insert('file', 'load', $relative, 'ok',
      timer::period_get('file load: '.$relative, -1, -2)
    );
    return $this->data;
  }

  function save() {
    static::mkdir_if_not_exists($this->dirs_get());
    return   @file_put_contents($this->path_get(), $this->data);
  }

  function append_direct($data) {
    static::mkdir_if_not_exists($this->dirs_get());
    return   @file_put_contents($this->path_get(), $data, FILE_APPEND);
  }

  function copy($new_dirs, $new_name = null, $this_reset = false) {
    $path_old = $this->path_get();
    $path_new = $new_dirs.($new_name ?: $this->file_get());
    static::mkdir_if_not_exists($new_dirs);
    if (@copy($path_old, $path_new)) {
      if ($this_reset)
          $this->__construct($path_new);
      return true;
    }
  }

  function move($new_dirs, $new_name = null) {
    $path_old = $this->path_get();
    $path_new = $new_dirs.($new_name ?: $this->file_get());
    static::mkdir_if_not_exists($new_dirs);
    if (@rename($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function move_uploaded($new_dirs, $new_name = null) {
    $path_old = $this->path_get();
    $path_new = $new_dirs.($new_name ?: $this->file_get());
    static::mkdir_if_not_exists($new_dirs);
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

  function require($once = true) {
    $relative = $this->path_get_relative();
    timer::tap('file insert: '.$relative);
    $result = $once ? require_once($this->path_get()) :
                           require($this->path_get());
    timer::tap('file insert: '.$relative);
    if (console::visible_mode_get()) {
      $memory_consumption = static::cache_op_info_get($this->path_get_absolute())['memory_consumption'] ?? null;
           console::log_insert('file', 'insertion', $relative, 'ok', timer::period_get('file insert: '.$relative, -1, -2), [], ['memory consumption' => $memory_consumption ?: '—']);
    } else console::log_insert('file', 'insertion', $relative, 'ok', timer::period_get('file insert: '.$relative, -1, -2), []);
    return $result;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache_data;
  static protected $cache_file_types;
  static protected $cache_op;

  static function cache_cleaning() {
    static::$cache_data       = null;
    static::$cache_file_types = null;
    static::$cache_op         = null;
  }

  static function cache_op_info_get($path_absolute) {
    if (static::$cache_op === null)
        static::$cache_op = opcache_get_status(true);
    return static::$cache_op['scripts'][$path_absolute] ?? null;
  }

  static function init() {
    if (static::$cache_file_types === null) {
      foreach (storage::get('data')->select_array('file_types') as $c_module_id => $c_file_types) {
        foreach ($c_file_types as $c_row_id => $c_file_type) {
          if (isset(static::$cache_file_types[$c_file_type->type])) console::report_about_duplicate('file_types', $c_file_type->type, $c_module_id, static::$cache_file_types[$c_file_type->type]);
                    static::$cache_file_types[$c_file_type->type] = $c_file_type;
                    static::$cache_file_types[$c_file_type->type]->module_id = $c_module_id;
        }
      }
    }
  }

  static function path_parse($path, $is_ignore_name = false) {
    if (strlen((string)$path)) {
      $result = new \stdClass;
      $matches = [];
      preg_match('%^(?:(?<type>[^./]+)\.|)'.
                      '(?<name>[^/]+|)'.
                      '(?<dirs>.*?)'.
                '(?://:(?<protocol>[a-z]{1,20})|)$%S', strrev((string)$path), $matches);
      $result->protocol = array_key_exists('protocol', $matches) ? strrev($matches['protocol']) : '';
      $result->dirs     = array_key_exists('dirs',     $matches) ? strrev($matches['dirs'    ]) : '';
      $result->name     = array_key_exists('name',     $matches) ? strrev($matches['name'    ]) : '';
      $result->type     = array_key_exists('type',     $matches) ? strrev($matches['type'    ]) : '';
      if (strlen($result->name) === 0 && strlen($result->type) === 0 && $is_ignore_name !== true) return;
      if (strlen($result->name) !== 0 && strlen($result->type) === 0 && ($result->name === '.' || $result->name === '..')) return;
      return $result;
    }
  }

  static function types_get() {
    static::init();
    return static::$cache_file_types;
  }

  static function mkdir_if_not_exists($dirs, $mode = 0777) {
    return !file_exists($dirs) ?
                 @mkdir($dirs, $mode, true) : true;
  }

  static function select_recursive($path, $filter = '', $with_dirs = false) {
    try {
      $result = [];
      $scan = $with_dirs ? new ri_iterator(new rd_iterator($path, static::scan_mode), static::scan_with_dir_at_first) :
                           new ri_iterator(new rd_iterator($path, static::scan_mode));
      foreach ($scan as $c_path => $c_spl_file_info) {
        if (!$filter || ($filter && preg_match($filter, $c_path))) {
          if     ($c_spl_file_info->isFile()) $result[$c_path] = new static($c_path);
          elseif ($c_spl_file_info->isDir ()) $result[$c_path] =            $c_path;
        }
      }
      krsort($result);
      return $result;
    } catch (\UnexpectedValueException $e) {
      return [];
    }
  }

}}