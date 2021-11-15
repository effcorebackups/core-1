<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class file_container {

  const wrapper = 'container';
  const meta_title = self::wrapper.'-meta=';
  const head_title = self::wrapper.'-head=';
  const head_title_length = 15;
  const head_length = 0xff;
  const message_file_mode_is_not_writing  = 'File mode does not support writing!';
  const message_file_mode_is_not_reading  = 'File mode does not support reading!';
  const message_file_path_is_invalid      = 'File path is invalid! Correct format: "'.self::wrapper.'://path_to_'.self::wrapper.':path_to_file';

  public $context;

  protected $stream;
  protected $target; # root|file
  protected $path_root;
  protected $path_file;
  protected $mode;
  protected $mode_is_readable = false; # valid reading modes: rb|r+b|a+b|c+b
  protected $mode_is_writable = false; # valid writing modes: r+b|wb|w+b|xb|x+b|cb|c+b
  protected $meta_parsed      = [];
  protected $was_changed      = false;

  # head properties
  protected $version          = 1.0;
  protected $lock_timestmp    = 1000000000;
  protected $meta_offset      = 0;
  protected $meta_offset_prev = 0;
  protected $checksum         = '00000000000000000000000000000000';

  # meta properties
  protected $offset = 0;
  protected $length = 0;

  function __data___pack($data) {return    serialize($data);}
  function __data_unpack($data) {return @unserialize($data);}

  function __head_init() {
    fseek($this->stream, static::head_title_length);
    $head = fread($this->stream, static::head_length - static::head_title_length);
    $head_parsed = static::__data_unpack($head);
    if ($head_parsed && array_key_exists('version',          $head_parsed)) $this->version          = $head_parsed['version'      ];
    if ($head_parsed && array_key_exists('lock_timestmp',    $head_parsed)) $this->lock_timestmp    = $head_parsed['lock_timestmp'];
    if ($head_parsed && array_key_exists('meta_offset',      $head_parsed)) $this->meta_offset      = $head_parsed['meta_offset'  ];
    if ($head_parsed && array_key_exists('meta_offset_prev', $head_parsed)) $this->meta_offset_prev = $head_parsed['meta_offset'  ];
    if ($head_parsed && array_key_exists('checksum',         $head_parsed)) $this->checksum         = $head_parsed['checksum'     ];
  }

  function __meta_init() {
    if ($this->meta_offset !== 0) {
      fseek($this->stream, $this->meta_offset);
      $meta = fread($this->stream, 0xffff);
      $this->meta_parsed = static::__data_unpack($meta);
      if (isset($this->meta_parsed[$this->path_file])) {
        if (array_key_exists('length', $this->meta_parsed[$this->path_file])) $this->length = $this->meta_parsed[$this->path_file]['length'];
        if (array_key_exists('offset', $this->meta_parsed[$this->path_file])) $this->offset = $this->meta_parsed[$this->path_file]['offset'];
      }
    }
  }

  function __head_save() {
    fseek ($this->stream, 0);
    fwrite($this->stream, static::head_title);
    fwrite($this->stream, str_pad(static::__data___pack([
      'version'          => $this->version,
      'lock_timestmp'    => $this->lock_timestmp,
      'meta_offset'      => $this->meta_offset,
      'meta_offset_prev' => $this->meta_offset_prev,
      'checksum'         => $this->checksum
    ]), static::head_length -
        static::head_title_length
    ));
  }

  function __meta_save() {
    fseek ($this->stream, 0, SEEK_END);
    fwrite($this->stream, static::meta_title);
    $this->meta_offset = ftell($this->stream);
    if ($this->length) $this->meta_parsed[$this->path_file]['length'] = $this->length;
    if ($this->offset) $this->meta_parsed[$this->path_file]['offset'] = $this->offset;
    fwrite($this->stream, static::__data___pack($this->meta_parsed));
  }

  function __root_is_exists() {
    return $this->stream;
  }

  function __file_is_exists() {
    return $this->offset !== 0;
  }

  # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

  function stream_open($path, $mode, $options, &$opened_path) {
    $path_parsed = static::__parse_path($path);
    if ($path_parsed['path_root']) {
      $this->path_root = $path_parsed['path_root'];
      $this->path_file = $path_parsed['path_file'];
      $this->target    = $path_parsed['target'   ];
      $this->mode      = $mode ? $mode : 'c+b';
      $this->mode_is_readable = strpbrk($this->mode,  'r' ) || strpos($this->mode, 'a+') === 0 || strpos($this->mode, 'c+') === 0;
      $this->mode_is_writable = strpbrk($this->mode, 'wxc') || strpos($this->mode, 'r+') === 0;
      $this->stream = @fopen($this->path_root, $this->mode, false, $this->context ?: stream_context_create([static::wrapper => []]));
      if ($this->stream) {
        $this->__head_init();
        $this->__meta_init();
        fseek($this->stream, $this->offset);
        return true;
      }
    } else throw new \Exception(
      static::message_file_path_is_invalid
    );
  }

  function stream_stat() {
    if ($this->target === 'root' && $this->__root_is_exists()) return fstat($this->stream);
    if ($this->target === 'file' && $this->__file_is_exists()) {
      $stat = fstat($this->stream);
      $stat['size'] = $this->meta_parsed[$this->path_file]['length'];
      return $stat;
    }
  }

  function url_stat($path, $flags = 0) {
    $opened_path = [];
    $handle = new static;
    $handle->stream_open($path, 'rb', [], $opened_path);
    $result = $handle->stream_stat();
    $handle->stream_close();
    return $result;
  }

  function stream_seek($offset, $whence = SEEK_SET) {
    if ($this->__root_is_exists()) {
      if ($this->__file_is_exists()) {
        $min = $this->offset;
        $max = $this->offset + $this->length;
        if ($whence === SEEK_CUR) {} # not supported by PHP
        if ($whence === SEEK_SET) {$new = $min + $offset; if ($new < $min) $new = $min; if ($new > $max) $new = $max; return fseek($this->stream, $new, SEEK_SET);} # PHP always return -1
        if ($whence === SEEK_END) {$new = $max + $offset; if ($new < $min) $new = $min; if ($new > $max) $new = $max; return fseek($this->stream, $new, SEEK_SET);} # PHP always return -1
      } else throw new \Exception(static::message_file_path_is_invalid);
    }   else throw new \Exception(static::message_file_path_is_invalid);
  }

  function stream_read($length = 0x2000) { # built-in value from PHP
    if ($this->mode_is_readable) {
      if ($this->__root_is_exists()) {
        if ($this->target === 'root') return fread($this->stream, $length);
        if ($this->target === 'file' &&
            $this->__file_is_exists()) {
          $debug = debug_backtrace(null, 2);
          if ($debug[1]['function'] === 'fread') $length =
              $debug[1][  'args'  ][1]; # fix built-in value
          $min = $this->offset;
          $max = $this->offset + $this->length;
          $cur = ftell($this->stream);
          if ($cur < $min) fseek($this->stream, $min);
          if ($cur > $max) fseek($this->stream, $max);
          $cur = ftell($this->stream);
          if ($cur + $length > $max)
                     $length = $max - $cur;
          if ($length >= 1)
               return fread($this->stream, $length);
          else return '';
        } else throw new \Exception(static::message_file_path_is_invalid);
      }   else throw new \Exception(static::message_file_path_is_invalid);
    }     else throw new \Exception(static::message_file_mode_is_not_reading);
  }

  function stream_write($data) {
    if ($this->mode_is_writable) {
      if ($this->__root_is_exists()) {
        if ($this->target === 'root') return fwrite($this->stream, $data);
        if ($this->target === 'file') {
          if (fstat($this->stream)['size'] === 0)
                    $this->__head_save();
          fseek($this->stream, 0, SEEK_END);
          if ($this->was_changed === false)
              $this->offset = ftell($this->stream);
          $result = fwrite($this->stream, $data);
          if ($result) {
            if ($this->was_changed === false) $this->length  = strlen($data);
            if ($this->was_changed !== false) $this->length += strlen($data);
            $this->was_changed = true;
          }
          return $result;
        } else throw new \Exception(static::message_file_path_is_invalid);
      }   else throw new \Exception(static::message_file_path_is_invalid);
    }     else throw new \Exception(static::message_file_mode_is_not_writing);
  }

  function stream_flush() {
    if ($this->__root_is_exists()) {
      return fflush($this->stream);
    }
  }

  function stream_close() {
    if ($this->__root_is_exists()) {
      if ($this->target === 'file' && $this->was_changed === true) $this->__meta_save();
      if ($this->target === 'file' && $this->was_changed === true) $this->__head_save();
      return fclose($this->stream);
    }
  }

  function dir_closedir()                           {} # not supported
  function dir_opendir($path, $options)             {} # not supported
  function dir_readdir()                            {} # not supported
  function dir_rewinddir()                          {} # not supported
  function mkdir($path, $mode, $options)            {} # not supported
  function rename($path_from, $path_to)             {} # not supported
  function rmdir($path, $options)                   {} # not supported
  function stream_cast($cast_as)                    {} # not supported
  function stream_eof()                             {} # not supported
  function stream_lock($operation)                  {} # not supported
  function stream_metadata($path, $option, $value)  {} # not supported
  function stream_set_option($option, $arg1, $arg2) {} # not supported
  function stream_tell()                            {} # not supported by PHP
  function stream_truncate($new_size)               {} # not supported

  ###########################
  ### static declarations ###
  ###########################

  static function __parse_path($path) {
    $path_pure = substr($path, strlen(static::wrapper.'://'));
    $path_file = ltrim(strrchr($path_pure, ':'), ':');
    $path_root = $path_file ? substr($path_pure, 0, -1 - strlen($path_file)) : $path_pure;
    return ['path_root' => $path_root, 'target' => empty($path_file) ? 'root' : 'file',
            'path_file' => $path_file];
  }

  static function meta_get($path) {
    $result = null;
    $opened_path = [];
    $handle = new static;
    $handle->stream_open($path, 'rb', [], $opened_path);
    if ($handle->__root_is_exists()) {
      $meta = $handle->meta_parsed;
      if (is_array($meta) && $handle->target === 'root'                               ) $result = $meta;
      if (is_array($meta) && $handle->target === 'file' && $handle->__file_is_exists()) $result = $meta[$handle->path_file];
      $handle->stream_close();
      return $result;
    }
  }

  static function add_file($path_src, $path_dst) {
    $handle_src = fopen($path_src,  'rb');
    $handle_dst = fopen($path_dst, 'c+b');
    if ($handle_src && $handle_dst) {
      $result = 0;
      stream_set_read_buffer ($handle_dst, 0);
      stream_set_write_buffer($handle_dst, 0);
      while (strlen($c_data = fread($handle_src, 1024)))
        $result+= fwrite($handle_dst, $c_data);
      fclose($handle_src);
      fclose($handle_dst);
      return $result;
    }
  }

  static function add_from_string($data, $path) {
    if (strlen($data)) {
      if ($handle = fopen($path, 'c+b')) {
        stream_set_read_buffer ($handle, 0);
        stream_set_write_buffer($handle, 0);
        $result = fwrite($handle, $data);
        fclose($handle);
        return $result;
      }
    }
  }

  static function unlink($path) {
    $path_parsed = static::__parse_path($path);
    if ($path_parsed['target'] === 'root') unlink($path_parsed['path_root']);
    if ($path_parsed['target'] === 'file') {
      $opened_path = [];
      $handle = new static;
      $handle->stream_open($path, 'c+b', [], $opened_path);
      if ($handle->__root_is_exists()) {
        if ($handle->__file_is_exists()) {
          unset($handle->meta_parsed[$path_parsed['path_file']]);
                $handle->length = 0;
                $handle->offset = 0;
          $handle->__meta_save();
          $handle->__head_save(); }
        return fclose($handle->stream);
      }
    }
  }

  static function cleaning($path) {
    $meta = static::meta_get($path);
    if ($meta) {
      @unlink($path.'.tmp');
      foreach ($meta as $c_internal_path => $null)
        static::add_file($path.':'.$c_internal_path, $path.'.tmp:'.$c_internal_path);
      $result = copy($path.'.tmp', $path);
      @unlink($path.'.tmp');
      return $result;
    }
  }

}}