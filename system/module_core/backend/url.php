<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class url {

  # valid urls:
  # ─────────────────────────────────────────────────────────────────────
  # $urls[] =                        '/';
  # $urls[] =                        '/?key=value';
  # $urls[] =                        '/#anchor';
  # $urls[] =                        '/?key=value#anchor';
  # $urls[] =                        '/dir/subdir/page';
  # $urls[] =                        '/dir/subdir/page?key=value';
  # $urls[] =                        '/dir/subdir/page#anchor';
  # $urls[] =                        '/dir/subdir/page?key=value#anchor';
  # $urls[] =        'subdomain.domain';
  # $urls[] =        'subdomain.domain/?key=value';
  # $urls[] =        'subdomain.domain/#anchor';
  # $urls[] =        'subdomain.domain/?key=value#anchor';
  # $urls[] =        'subdomain.domain/dir/subdir/page';
  # $urls[] =        'subdomain.domain/dir/subdir/page?key=value';
  # $urls[] =        'subdomain.domain/dir/subdir/page#anchor';
  # $urls[] =        'subdomain.domain/dir/subdir/page?key=value#anchor';
  # $urls[] = 'http://subdomain.domain';
  # $urls[] = 'http://subdomain.domain/?key=value';
  # $urls[] = 'http://subdomain.domain/#anchor';
  # $urls[] = 'http://subdomain.domain/?key=value#anchor';
  # $urls[] = 'http://subdomain.domain/dir/subdir/page';
  # $urls[] = 'http://subdomain.domain/dir/subdir/page?key=value';
  # $urls[] = 'http://subdomain.domain/dir/subdir/page#anchor';
  # $urls[] = 'http://subdomain.domain/dir/subdir/page?key=value#anchor';
  # ─────────────────────────────────────────────────────────────────────

  # wrong urls:
  # ─────────────────────────────────────────────────────────────────────
  # 1. 'http://subdomain.domain/' - should be redirected to 'http://subdomain.domain'
  # 2. 'subdomain.domain/'        - should be redirected to 'http://subdomain.domain'
  # 3. '/subdomain.domain'        - this domain described like a directory (first char is the slash)
  # 4. 'dir/subdir/page'          - this directory described like a domain (first char is not the slash)
  # ─────────────────────────────────────────────────────────────────────

  # note:
  # ─────────────────────────────────────────────────────────────────────
  # 1. in the next url "http://name:pass@subdomain.domain:port/dir/subdir/page?key=value#anchor"
  #    the name, password and port values after parsing will be in the $domain property
  # ─────────────────────────────────────────────────────────────────────

  public $protocol;
  public $domain;
  public $path;
  public $query;
  public $anchor;

  function __construct($url) {
    $matches = [];
    preg_match('%^(?:(?<protocol>[a-z]+)://|)'.
                    '(?<domain>[^/]*)'.
                    '(?<path>[^?#]*)'.
              '(?:\\?(?<query>[^\\#]*)|)'.
              '(?:\\#(?<anchor>.*)|)$%S', core::sanitize_url($url), $matches);
    $this->protocol = !empty($matches['protocol']) ? $matches['protocol'] : (!empty($matches['domain']) ? 'http' : ( /* case for local ulr */ $_SERVER['REQUEST_SCHEME']));
    $this->domain   = !empty($matches['domain'])   ? $matches['domain']   :                                        ( /* case for local ulr */ $_SERVER['HTTP_HOST']);
    $this->path     = !empty($matches['path'])     ? $matches['path']     : '/';
    $this->query    = !empty($matches['query'])    ? $matches['query']    : '';
    $this->anchor   = !empty($matches['anchor'])   ? $matches['anchor']   : '';
  }

  function get_type()     {return ltrim(strtolower(strrchr($this->path, '.')), '.');}
  function get_protocol() {return $this->protocol;}
  function get_domain()   {return $this->domain;}
  function get_path()     {return $this->path;}
  function get_query()    {return $this->query;}
  function get_anchor()   {return $this->anchor;}
  function get_relative() {return ($this->path == '/' && !$this->query && !$this->anchor ? '' : $this->path).
                                  ($this->query  ? '?'.$this->query  : '').
                                  ($this->anchor ? '#'.$this->anchor : '');}
  function get_full()     {return ($this->protocol.'://'.$this->domain).
                                  ($this->path == '/' && !$this->query && !$this->anchor ? '' : $this->path).
                                  ($this->query  ? '?'.$this->query  : '').
                                  ($this->anchor ? '#'.$this->anchor : '');}

  function get_query_arg($arg_id) {
    $args = [];
    parse_str($this->query, $args);
    return isset($args[$arg_id]) ?
                 $args[$arg_id] : null;
  }

  function get_path_arg($arg_id) {
    $args = explode('/', $this->path);
    return isset($args[$arg_id]) ?
                 $args[$arg_id] : null;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    static::$cache = new url($_SERVER['REQUEST_URI']);
  }

  static function current_get() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function get_back_url() {
    $back_url = static::current_get()->get_query_arg('back');
    return $back_url ? urldecode($back_url) : '';
  }

  static function make_back_part() {
    return 'back='.urlencode(static::current_get()->get_full());
  }

  static function is_local($url) {
    return (new url($url))->domain == $_SERVER['HTTP_HOST'];
  }

  static function is_active($url) {
    return (new url($url))->get_full() == static::current_get()->get_full();
  }

  static function is_active_trail($url) {
    return strpos(static::current_get()->get_full(), (new url($url))->get_full()) === 0;
  }

  static function go($url) {
    core::send_header_and_exit('redirect', '', '',
      (new url($url))->get_full()
    );
  }

}}