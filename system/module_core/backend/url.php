<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class url {

  # valid urls:
  # ┌──────────────────────────────────────────────────────────┐
  # │ url                                                      │
  # ╞══════════════════════════════════════════════════════════╡
  # │                        /                                 │
  # │                        /?key=value                       │
  # │                        /#anchor                          │
  # │                        /?key=value#anchor                │
  # │                        /dir/subdir/page                  │
  # │                        /dir/subdir/page?key=value        │
  # │                        /dir/subdir/page#anchor           │
  # │                        /dir/subdir/page?key=value#anchor │
  # │        subdomain.domain                                  │
  # │        subdomain.domain/?key=value                       │
  # │        subdomain.domain/#anchor                          │
  # │        subdomain.domain/?key=value#anchor                │
  # │        subdomain.domain/dir/subdir/page                  │
  # │        subdomain.domain/dir/subdir/page?key=value        │
  # │        subdomain.domain/dir/subdir/page#anchor           │
  # │        subdomain.domain/dir/subdir/page?key=value#anchor │
  # │ http://subdomain.domain                                  │
  # │ http://subdomain.domain/?key=value                       │
  # │ http://subdomain.domain/#anchor                          │
  # │ http://subdomain.domain/?key=value#anchor                │
  # │ http://subdomain.domain/dir/subdir/page                  │
  # │ http://subdomain.domain/dir/subdir/page?key=value        │
  # │ http://subdomain.domain/dir/subdir/page#anchor           │
  # │ http://subdomain.domain/dir/subdir/page?key=value#anchor │
  # └──────────────────────────────────────────────────────────┘

  # wrong urls:
  # ┌──────────────────────────╥──────────────────────────────────────────────────────────────────────┐
  # │ url                      ║ behavior                                                             │
  # ╞══════════════════════════╬══════════════════════════════════════════════════════════════════════╡
  # │ http://subdomain.domain/ ║ should be redirected to 'http://subdomain.domain'                    │
  # │ subdomain.domain/        ║ should be redirected to 'http://subdomain.domain'                    │
  # │ /subdomain.domain        ║ this domain described like a directory (first char is the slash)     │
  # │ dir/subdir/page          ║ this directory described like a domain (first char is not the slash) │
  # └──────────────────────────╨──────────────────────────────────────────────────────────────────────┘

  # note:
  # ════════════════════════════════════════════════════════════════════════════════════════════
  # 1. in the next url 'http://name:pass@subdomain.domain:port/dir/subdir/page?key=value#anchor'
  #    the name, password and port values after parsing will be in the $domain property
  # ────────────────────────────────────────────────────────────────────────────────────────────

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
              '(?:\\?(?<query>[^#]*)|)'.
              '(?:\\#(?<anchor>.*)|)$%S', core::sanitize_url($url), $matches);
    $this->protocol = !empty($matches['protocol']) ? $matches['protocol'] : (!empty($matches['domain']) ? 'http' : ( /* case for local ulr */ core::server_request_scheme_get()));
    $this->domain   = !empty($matches['domain'])   ? $matches['domain']   :                                        ( /* case for local ulr */ core::server_host_get());
    $this->path     = !empty($matches['path'])     ? $matches['path']     : '/';
    $this->query    = !empty($matches['query'])    ? $matches['query']    : '';
    $this->anchor   = !empty($matches['anchor'])   ? $matches['anchor']   : '';
  }

  function type_get()     {return ltrim(strtolower(strrchr($this->path, '.')), '.');}
  function protocol_get() {return $this->protocol;}
  function domain_get()   {return $this->domain;}
  function path_get()     {return $this->path;}
  function query_get()    {return $this->query;}
  function anchor_get()   {return $this->anchor;}
  function relative_get() {return ($this->path == '/' && !$this->query && !$this->anchor ? '' : $this->path).
                                  ($this->query  ? '?'.$this->query  : '').
                                  ($this->anchor ? '#'.$this->anchor : '');}
  function full_get()     {return ($this->protocol.'://'.$this->domain).
                                  ($this->path == '/' && !$this->query && !$this->anchor ? '' : $this->path).
                                  ($this->query  ? '?'.$this->query  : '').
                                  ($this->anchor ? '#'.$this->anchor : '');}

  function query_arg_select($name)         {$args = []; parse_str($this->query, $args); return isset($args[$name]) ? $args[$name] : null;}
  function query_arg_insert($name, $value) {$args = []; parse_str($this->query, $args); $args[$name] = $value; $this->query = http_build_query($args);}
  function query_arg_delete($name)         {$args = []; parse_str($this->query, $args); unset($args[$name]);   $this->query = http_build_query($args);}

  function path_arg_select($name) {
    $args = explode('/', $this->path);
    return isset($args[$name]) ?
                 $args[$name] : null;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function init() {
    static::$cache = new url(core::server_request_uri_get());
  }

  static function current_get() {
    if   (!static::$cache) static::init();
    return static::$cache;
  }

  static function back_url_get() {
    $back_url = static::current_get()->query_arg_select('back');
    return $back_url ? urldecode($back_url) : '';
  }

  static function back_part_make() {
    return 'back='.urlencode(static::current_get()->full_get());
  }

  static function is_local($url) {
    return (new url($url))->domain == core::server_host_get();
  }

  static function is_active($url) {
    return (new url($url))->full_get() == static::current_get()->full_get();
  }

  static function is_active_trail($url) {
    return strpos(static::current_get()->full_get(), (new url($url))->full_get()) === 0;
  }

  static function go($url) {
    core::send_header_and_exit('redirect', '', '',
      (new url($url))->full_get()
    );
  }

}}