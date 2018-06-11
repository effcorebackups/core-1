<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          use \FilesystemIterator as fs_iterator;
          use \RecursiveDirectoryIterator as rd_iterator;
          use \RecursiveIteratorIterator as ri_iterator;
          class file {

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. path = dirs/ + name + '.' + type
  # 2. if the first character in the path is '/' - it's a full path, оtherwise - relative path
  # 3. if the last character in the path is '/' - it's a directory, оtherwise - file
  # 4. path components like '../' should be ignored!
  # 5. path components like './' should be ignored!
  # 6. windows files naming rules should be ignored!
  # ─────────────────────────────────────────────────────────────────────

  # path                      | dirs/         | name    | type | relative
  # ─────────────────────────────────────────────────────────────────────
  # - .fiLe                   |               | .fiLe   |      | -
  # - .fiLe.eXt               |               | .fiLe   | eXt  | -
  # - fiLe                    |               | fiLe    |      | -
  # - fiLe.eXt                |               | fiLe    | eXt  | -
  # - fiLe.0.eXt              |               | fiLe.0  | eXt  | -
  # - diR.1/                  | diR.1/        |         |      | yes
  # - diR.1/диР.2/            | diR.1/диР.2/  |         |      | yes
  # - diR.1/диР.2/.fiLe       | diR.1/диР.2/  | .fiLe   |      | yes
  # - diR.1/диР.2/.fiLe.eXt   | diR.1/диР.2/  | .fiLe   | eXt  | yes
  # - diR.1/диР.2/fiLe        | diR.1/диР.2/  | fiLe    |      | yes
  # - diR.1/диР.2/fiLe.eXt    | diR.1/диР.2/  | fiLe    | eXt  | yes
  # - diR.1/диР.2/fiLe.0.eXt  | diR.1/диР.2/  | fiLe.0  | eXt  | yes
  # - /diR.1/                 | /diR.1/       |         |      | no
  # - /diR.1/диР.2/           | /diR.1/диР.2/ |         |      | no
  # - /diR.1/диР.2/.fiLe      | /diR.1/диР.2/ | .fiLe   |      | no
  # - /diR.1/диР.2/.fiLe.eXt  | /diR.1/диР.2/ | .fiLe   | eXt  | no
  # - /diR.1/диР.2/fiLe       | /diR.1/диР.2/ | fiLe    |      | no
  # - /diR.1/диР.2/fiLe.eXt   | /diR.1/диР.2/ | fiLe    | eXt  | no
  # - /diR.1/диР.2/fiLe.0.eXt | /diR.1/диР.2/ | fiLe.0  | eXt  | no
  # ─────────────────────────────────────────────────────────────────────
  # - c:\dir1                 | should be ignored
  # - \dir1                   | should be ignored
  # - dir1\                   | should be ignored
  # - ./dir1                  | should be ignored
  # - ../dir1/                | should be ignored
  # - /dir1/../dir3/          | should be ignored
  # - dir1                    | should be ignored (interpreted as: file1)
  # - dir1/dir2               | should be ignored (interpreted as: dir1/file2)
  # ─────────────────────────────────────────────────────────────────────

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
    $this->dirs = isset($matches['dirs']) ? $matches['dirs'] : '';
    $this->name = isset($matches['name']) ? $matches['name'] : '';
    $this->type = isset($matches['type']) ? ltrim($matches['type'], '.') : '';
  }

  function set_dirs($dirs) {$this->dirs = $dirs;}
  function set_name($name) {$this->name = $name;}
  function set_type($type) {$this->type = $type;}
  function set_data($data) {$this->data = $data;}

  function get_dirs()          {return $this->dirs;}
  function get_dirs_parts()    {return explode('/', trim($this->dirs, '/'));}
  function get_dirs_relative() {return isset($this->dirs[0]) && $this->dirs[0] == '/' ? substr($this->dirs, strlen(dir_root)) : $this->dirs;}
# ─────────────────────────────────────────────────────────────────────
  function name_get()          {return $this->name;}
  function get_type()          {return $this->type;}
  function get_file()          {return $this->type ? $this->name.'.'.$this->type : $this->name;}
# ─────────────────────────────────────────────────────────────────────
  function get_path()          {return $this->type ? $this->dirs.$this->name.'.'.$this->type : $this->dirs.$this->name;}
  function get_path_relative() {return $this->get_dirs_relative().$this->get_file();}

  function get_parent_name()   {return ltrim(strrchr(rtrim($this->dirs, '/'), '/'), '/');}
  function get_hash()          {return @md5_file($this->get_path());}
  function get_size()          {return @filesize($this->get_path());}
  function get_mime()          {return @mime_content_type($this->get_path());}
  function get_data() {
    if (empty($this->data)) $this->load(true);
    return $this->data;
  }

  function is_exist() {
    return file_exists($this->get_path());
  }

  function load($reset = false) {
    $relative = $this->get_path_relative();
    timer::tap('file load: '.$relative);
    if (!$reset && isset(static::$cache_data[$relative]))
           $this->data = static::$cache_data[$relative];
    else   $this->data = static::$cache_data[$relative] = @file_get_contents($this->get_path());
    timer::tap('file load: '.$relative);
    console::log_add('file', 'load', $relative, 'ok',
      timer::get_period('file load: '.$relative, -1, -2)
    );
    return $this->data;
  }

  function save() {
    static::mkdir_if_not_exist($this->get_dirs());
    return  @file_put_contents($this->get_path(), $this->data);
  }

  function direct_append($data) {
    static::mkdir_if_not_exist($this->get_dirs());
    return  @file_put_contents($this->get_path(), $data, FILE_APPEND);
  }

  function copy($new_dirs, $new_name = null) {
    $path_old = $this->get_path();
    $path_new = $new_dirs.($new_name ?: $this->get_file());
    static::mkdir_if_not_exist($new_dirs);
    if (@copy($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function move($new_dirs, $new_name = null) {
    $path_old = $this->get_path();
    $path_new = $new_dirs.($new_name ?: $this->get_file());
    static::mkdir_if_not_exist($new_dirs);
    if (@rename($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function move_uploaded($new_dirs, $new_name = null) {
    $path_old = $this->get_path();
    $path_new = $new_dirs.($new_name ?: $this->get_file());
    static::mkdir_if_not_exist($new_dirs);
    if (@move_uploaded_file($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function rename($new_name) {
    $path_old = $this->get_path();
    $path_new = $this->get_dirs().$new_name;
    if (@rename($path_old, $path_new)) {
      $this->__construct($path_new);
      return true;
    }
  }

  function insert($once = true) {
    $relative = $this->get_path_relative();
    timer::tap('file insert: '.$relative);
    $return = $once ? require_once($this->get_path()) :
                           require($this->get_path());
    timer::tap('file insert: '.$relative);
    console::log_add('file', 'insertion', $relative, 'ok',
      timer::get_period('file insert: '.$relative, -1, -2)
    );
    return $return;
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

  static function get_types() {
    if   (!static::$cache_file_types) static::init();
    return static::$cache_file_types;
  }

  static function mkdir_if_not_exist($dirs) {
    return !file_exists($dirs) ?
                 @mkdir($dirs, 0777, true) : true;
  }

  static function select_all_recursive($path, $filter = '') {
    try {
      $return = [];
      foreach (new ri_iterator(new rd_iterator($path, static::scan_dir_mode)) as $c_path => $null) {
        if (!$filter || ($filter && preg_match($filter, $c_path))) {
          $return[$c_path] = new static($c_path);
        }
      }
      return $return;
    } catch (\UnexpectedValueException $e) {
      return [];
    }
  }

}}