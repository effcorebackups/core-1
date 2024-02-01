<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use Exception;
use stdClass;

class File {

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

    const READ_BLOCK_SIZE = 1024;

    const ERR_MESSAGE_UNKNOWN              = 'Unknown error!';
    const ERR_MESSAGE_PATH_IS_INVALID      = 'File path is invalid!';
    const ERR_MESSAGE_IS_NOT_EXISTS        = 'File is not exists!';
    const ERR_MESSAGE_IS_EXISTS            = 'File is exists!';
    const ERR_MESSAGE_IS_NOT_READABLE      = 'File is not readable!';
    const ERR_MESSAGE_IS_NOT_WRITABLE      = 'File is not writable!';
    const ERR_MESSAGE_PERM_ARE_TOO_STRICT  = 'File permissions are too strict!';
    const ERR_MESSAGE_MODE_IS_NOT_READABLE = 'File mode does not support reading!';
    const ERR_MESSAGE_MODE_IS_NOT_WRITABLE = 'File mode does not support writing!';
    const ERR_MESSAGE_MODE_IS_NOT_SEEKABLE = 'File mode does not support seeking!';

    const ERR_CODE_UNKNOWN              = 0;
    const ERR_CODE_PATH_IS_INVALID      = 1;
    const ERR_CODE_IS_NOT_EXISTS        = 2;
    const ERR_CODE_IS_EXISTS            = 3;
    const ERR_CODE_IS_NOT_READABLE      = 4;
    const ERR_CODE_IS_NOT_WRITABLE      = 5;
    const ERR_CODE_PERM_ARE_TOO_STRICT  = 6;
    const ERR_CODE_MODE_IS_NOT_READABLE = 7;
    const ERR_CODE_MODE_IS_NOT_WRITABLE = 8;
    const ERR_CODE_MODE_IS_NOT_SEEKABLE = 9;

    public $protocol;
    public $dirs;
    public $name;
    public $type;
    public $data;

    function __construct($path) {
        $this->parse($path);
    }

    function parse($path) {
        $info = static::__path_parse($path);
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
    function dirs_get_absolute() {return $this->is_path_absolute() ?        $this->dirs :       DIR_ROOT.    $this->dirs;}
    function dirs_get_relative() {return $this->is_path_absolute() ? substr($this->dirs, strlen(DIR_ROOT)) : $this->dirs;}

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
        return is_string($this->type) && strlen($this->type) ?
            $this->name.'.'.$this->type :
            $this->name;
    }

    # ─────────────────────────────────────────────────────────────────────
    # work with path (protocol + '://' + dirs + name + '.' + type)
    # ─────────────────────────────────────────────────────────────────────

    function path_get() {
        return (is_string($this->protocol) && strlen($this->protocol) ?
                          $this->protocol.'://' : '').
                          $this->dirs.
                          $this->file_get();
    }

    function path_get_absolute() {
        return (is_string($this->protocol) && strlen($this->protocol) ?
                          $this->protocol.'://' : '').
                          $this->dirs_get_absolute().
                          $this->file_get();
    }

    function path_get_relative() {
        return (is_string($this->protocol) && strlen($this->protocol) ?
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

    function require($once = true) {
        $path_relative = $this->path_get_relative();
        Timer::tap('file insert: '.$path_relative);
        $result = $once ? require_once($this->path_get()) :
                          require     ($this->path_get());
        Timer::tap('file insert: '.$path_relative);
        Console::log_insert('file', 'insertion', $path_relative, 'ok', Timer::period_get('file insert: '.$path_relative, -1, -2), [], [
            'memory consumption' => static::cache_op_info_get($this->path_get_absolute())['memory_consumption'] ?? '-']
        );
        return $result;
    }

    function load($reset = false) {
        $path_relative = $this->path_get_relative();
        Timer::tap('file load: '.$path_relative);
        if (!$reset && isset(static::$cache_data[$path_relative]))
             $this->data  =  static::$cache_data[$path_relative];
        else $this->data  =  static::$cache_data[$path_relative] = @file_get_contents($this->path_get());
        Timer::tap('file load: '.$path_relative);
        Console::log_insert('file', 'load', $path_relative, 'ok',
            Timer::period_get('file load: '.$path_relative, -1, -2)
        );
        return $this->data;
    }

    function save() {
        Directory::create($this->dirs_get());
        return @file_put_contents($this->path_get(), $this->data);
    }

    function append_direct($data) {
        Directory::create($this->dirs_get());
        try { return @file_put_contents($this->path_get(), $data, FILE_APPEND); } catch (Exception $e) {}
    }

    function copy($new_dirs, $new_name = null, $this_reset = false) {
        $path_old = $this->path_get();
        $path_new = $new_dirs.($new_name ?: $this->file_get());
        Directory::create($new_dirs);
        if (@copy($path_old, $path_new)) {
            if ($this_reset)
                $this->__construct($path_new);
            return true;
        }
    }

    function move($new_dirs, $new_name = null) {
        $path_old = $this->path_get();
        $path_new = $new_dirs.($new_name ?: $this->file_get());
        Directory::create($new_dirs);
        if (@rename($path_old, $path_new)) {
            $this->__construct($path_new);
            return true;
        }
    }

    function move_uploaded($new_dirs, $new_name = null) {
        $path_old = $this->path_get();
        $path_new = $new_dirs.($new_name ?: $this->file_get());
        Directory::create($new_dirs);
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

    ###########################
    ### static declarations ###
    ###########################

    protected static $cache_data;
    protected static $cache_file_types;
    protected static $cache_op;

    static function cache_cleaning() {
        static::$cache_data       = null;
        static::$cache_file_types = null;
        static::$cache_op         = null;
    }

    static function cache_op_info_get($path_absolute) {
        if (static::$cache_op === null && function_exists('opcache_get_status'))
            static::$cache_op = opcache_get_status(true);
        return static::$cache_op['scripts'][$path_absolute] ?? null;
    }

    static function init() {
        if (static::$cache_file_types === null) {
            foreach (Storage::get('data')->select_array('file_types') as $c_module_id => $c_file_types) {
                foreach ($c_file_types as $c_row_id => $c_file_type) {
                    if (isset(static::$cache_file_types[$c_file_type->type])) Console::report_about_duplicate('file_types', $c_file_type->type, $c_module_id, static::$cache_file_types[$c_file_type->type]);
                              static::$cache_file_types[$c_file_type->type] = $c_file_type;
                              static::$cache_file_types[$c_file_type->type]->module_id = $c_module_id;
                }
            }
        }
    }

    static function types_get() {
        static::init();
        return static::$cache_file_types;
    }

    static function delete($path) {
        try { return @unlink($path); } catch (Exception $e) {}
    }

    static function __path_parse($path, $is_ignore_name = false) {
        if (strlen((string)$path)) {
            $result = new stdClass;
            $matches = [];
            preg_match('%^(?:(?<type>[^./]+)\.|)'.
                            '(?<name>[^/]+|)'.
                            '(?<dirs>.*?)'.
                      '(?://:(?<protocol>[a-z]{1,20})|)$%S', strrev((string)$path), $matches);
            $result->protocol = array_key_exists('protocol', $matches) ? strrev($matches['protocol']) : '';
            $result->dirs     = array_key_exists('dirs'    , $matches) ? strrev($matches['dirs'    ]) : '';
            $result->name     = array_key_exists('name'    , $matches) ? strrev($matches['name'    ]) : '';
            $result->type     = array_key_exists('type'    , $matches) ? strrev($matches['type'    ]) : '';
            if ($result->name === '' && $result->type === '' && $is_ignore_name !== true)                          return;
            if ($result->name !== '' && $result->type === '' && ($result->name === '.' || $result->name === '..')) return;
            if ($result->name === '' && $result->type === '' && $result->dirs === '' && $result->protocol === '')  return;
            return $result;
        }
    }

    # ┌───────────┬─────────────┬─────────────┬─────────────┬──────────────────┬─────────────┬───────┐
    # │ file mode │ is readable │ is writable │ is seekable │ is auto-creation │ is trimming │ seek  │
    # ├───────────┼─────────────┼─────────────┼─────────────┼──────────────────┼─────────────┼───────┤
    # │     r     │     yes     │             │     yes     │                  │             │   0   │
    # │     r+    │     yes     │     yes     │     yes     │                  │             │   0   │
    # │     w     │             │     yes     │     yes     │       yes        │     yes     │   0   │
    # │     w+    │     yes     │     yes     │     yes     │       yes        │     yes     │   0   │
    # │     c     │             │     yes     │     yes     │       yes        │             │   0   │
    # │     c+    │     yes     │     yes     │     yes     │       yes        │             │   0   │
    # │     a     │             │     yes     │             │       yes        │             │ end^2 │
    # │     a+    │     yes     │     yes     │             │       yes        │             │ end^2 │
    # │     x     │             │     yes     │     yes     │       yes^1      │             │   0   │
    # │     x+    │     yes     │     yes     │     yes     │       yes^1      │             │   0   │
    # └───────────┴─────────────┴─────────────┴─────────────┴──────────────────┴─────────────┴───────┘
    # ^1: If the file exists then an error occurs.
    # ^2: Seek only affects the reading position, writes are always appended.

    static function get_mode_info($mode) {
        $symbol = $mode[0];
        $has_plus = str_contains($mode, '+');
        if ($symbol === 'r' && $has_plus === true) return ['is_readable' => true , 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => false];
        if ($symbol === 'w' && $has_plus === true) return ['is_readable' => true , 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => true ];
        if ($symbol === 'c' && $has_plus === true) return ['is_readable' => true , 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => true ];
        if ($symbol === 'a' && $has_plus === true) return ['is_readable' => true , 'is_writable' => true , 'is_seekable' => false, 'is_auto_creation' => true ];
        if ($symbol === 'x' && $has_plus === true) return ['is_readable' => true , 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => true ];
        if ($symbol === 'r' && $has_plus !== true) return ['is_readable' => true , 'is_writable' => false, 'is_seekable' => true , 'is_auto_creation' => false];
        if ($symbol === 'w' && $has_plus !== true) return ['is_readable' => false, 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => true ];
        if ($symbol === 'c' && $has_plus !== true) return ['is_readable' => false, 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => true ];
        if ($symbol === 'a' && $has_plus !== true) return ['is_readable' => false, 'is_writable' => true , 'is_seekable' => false, 'is_auto_creation' => true ];
        if ($symbol === 'x' && $has_plus !== true) return ['is_readable' => false, 'is_writable' => true , 'is_seekable' => true , 'is_auto_creation' => true ];
    }

    static function get_fopen_error_reason($path_root, $path_file, $mode) {
        $mode_info = static::get_mode_info($mode);
        $mode_is_readable        = $mode_info && $mode_info['is_readable'];
        $mode_is_writable        = $mode_info && $mode_info['is_writable'];
        $mode_is_auto_creation   = $mode_info && $mode_info['is_auto_creation'];
        $root_is_executable = Core::is_Win() ? true : is_executable($path_root);
        $root_is_exists     = file_exists($path_root);
        $root_is_writable   = is_writable($path_root);
        $file_is_exists     = file_exists($path_file);
        $file_is_readable   = is_readable($path_file);
        $file_is_writable   = is_writable($path_file);
        # DIRECTORY is NOT exists
        if (!$root_is_exists) {
            return Directory::ERR_CODE_IS_NOT_EXISTS;
        }
        # DIRECTORY permission has 'x' │ FILE is exists │ FOPEN mode is 'x'/x+'
        if ($root_is_executable && $file_is_exists && strpbrk($mode, 'x')) {
            return static::ERR_CODE_IS_EXISTS;
        }
        # DIRECTORY permission has 'x' │ FILE is exists | FOPEN mode is 'r'/'r+'/'w'/'w+'/'c'/'c+'/'a'/'a+'
        if ($root_is_executable && $file_is_exists && strpbrk($mode, 'r'.'w'.'c'.'a')) {
            if (!$file_is_readable && !$file_is_writable                     ) return static::ERR_CODE_PERM_ARE_TOO_STRICT; # FILE permission is '---' │ FOPEN mode is *
            if ( $file_is_readable && !$file_is_writable && $mode_is_writable) return static::ERR_CODE_PERM_ARE_TOO_STRICT; # FILE permission is 'r--' │ FOPEN mode is 'r+'/'w'/'w+'/'c'/'c+'/'a'/'a+'
            if (!$file_is_readable &&  $file_is_writable && $mode_is_readable) return static::ERR_CODE_PERM_ARE_TOO_STRICT; # FILE permission is '-w-' │ FOPEN mode is 'r'/'r+'/'w+'/'c+'/'a+'
        }
        # DIRECTORY permission has 'x' │ FILE is NOT exists
        if ($root_is_executable && !$file_is_exists) {
            if (!$root_is_writable && !$mode_is_auto_creation) return static::ERR_CODE_IS_NOT_EXISTS;          # FOPEN mode is 'r'/'r+'
            if ( $root_is_writable && !$mode_is_auto_creation) return static::ERR_CODE_IS_NOT_EXISTS;          # FOPEN mode is 'r'/'r+'
            if (!$root_is_writable &&  $mode_is_auto_creation) return Directory::ERR_CODE_PERM_ARE_TOO_STRICT; # FOPEN mode is 'w'/'w+'/'c'/'c+'/'a'/'a+'/'x'/'x+'
        }
        # DIRECTORY permission has NOT 'x' │ FILE STATE IS UNDETECTABLE
        if (!$root_is_executable) {
            return Directory::ERR_CODE_PERM_ARE_TOO_STRICT;
        }
    }

}
