<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class url {

  # valid urls:
  # ┌──────────────────────────────────────────────────────────╥──────────┬──────────────────┬──────────────────┬───────────┬────────┬──────────────────────────────────────────────────────────┬───────────────────────────────────┐
  # │ url                                                      ║ protocol │ domain           │ path             │ query     │ anchor │ full_get()                                               │ tiny_get()                        │
  # ╞══════════════════════════════════════════════════════════╬══════════╪══════════════════╪══════════════════╪═══════════╪════════╪══════════════════════════════════════════════════════════╪═══════════════════════════════════╡
  # │                        /                                 ║ http     │ subdomain.domain │ /                │           │        │ http://subdomain.domain                                  │ /                                 │
  # │                        /?key=value                       ║ http     │ subdomain.domain │ /                │ key=value │        │ http://subdomain.domain/?key=value                       │ /?key=value                       │
  # │                        /#anchor                          ║ http     │ subdomain.domain │ /                │           │ anchor │ http://subdomain.domain/#anchor                          │ /#anchor                          │
  # │                        /?key=value#anchor                ║ http     │ subdomain.domain │ /                │ key=value │ anchor │ http://subdomain.domain/?key=value#anchor                │ /?key=value#anchor                │
  # │                        /dir/subdir/page                  ║ http     │ subdomain.domain │ /dir/subdir/page │           │        │ http://subdomain.domain/dir/subdir/page                  │ /dir/subdir/page                  │
  # │                        /dir/subdir/page?key=value        ║ http     │ subdomain.domain │ /dir/subdir/page │ key=value │        │ http://subdomain.domain/dir/subdir/page?key=value        │ /dir/subdir/page?key=value        │
  # │                        /dir/subdir/page#anchor           ║ http     │ subdomain.domain │ /dir/subdir/page │           │ anchor │ http://subdomain.domain/dir/subdir/page#anchor           │ /dir/subdir/page#anchor           │
  # │                        /dir/subdir/page?key=value#anchor ║ http     │ subdomain.domain │ /dir/subdir/page │ key=value │ anchor │ http://subdomain.domain/dir/subdir/page?key=value#anchor │ /dir/subdir/page?key=value#anchor │
  # │        subdomain.domain                                  ║ http     │ subdomain.domain │ /                │           │        │ http://subdomain.domain                                  │ /                                 │
  # │        subdomain.domain/?key=value                       ║ http     │ subdomain.domain │ /                │ key=value │        │ http://subdomain.domain/?key=value                       │ /?key=value                       │
  # │        subdomain.domain/#anchor                          ║ http     │ subdomain.domain │ /                │           │ anchor │ http://subdomain.domain/#anchor                          │ /#anchor                          │
  # │        subdomain.domain/?key=value#anchor                ║ http     │ subdomain.domain │ /                │ key=value │ anchor │ http://subdomain.domain/?key=value#anchor                │ /?key=value#anchor                │
  # │        subdomain.domain/dir/subdir/page                  ║ http     │ subdomain.domain │ /dir/subdir/page │           │        │ http://subdomain.domain/dir/subdir/page                  │ /dir/subdir/page                  │
  # │        subdomain.domain/dir/subdir/page?key=value        ║ http     │ subdomain.domain │ /dir/subdir/page │ key=value │        │ http://subdomain.domain/dir/subdir/page?key=value        │ /dir/subdir/page?key=value        │
  # │        subdomain.domain/dir/subdir/page#anchor           ║ http     │ subdomain.domain │ /dir/subdir/page │           │ anchor │ http://subdomain.domain/dir/subdir/page#anchor           │ /dir/subdir/page#anchor           │
  # │        subdomain.domain/dir/subdir/page?key=value#anchor ║ http     │ subdomain.domain │ /dir/subdir/page │ key=value │ anchor │ http://subdomain.domain/dir/subdir/page?key=value#anchor │ /dir/subdir/page?key=value#anchor │
  # │ http://subdomain.domain                                  ║ http     │ subdomain.domain │ /                │           │        │ http://subdomain.domain                                  │ /                                 │
  # │ http://subdomain.domain/?key=value                       ║ http     │ subdomain.domain │ /                │ key=value │        │ http://subdomain.domain/?key=value                       │ /?key=value                       │
  # │ http://subdomain.domain/#anchor                          ║ http     │ subdomain.domain │ /                │           │ anchor │ http://subdomain.domain/#anchor                          │ /#anchor                          │
  # │ http://subdomain.domain/?key=value#anchor                ║ http     │ subdomain.domain │ /                │ key=value │ anchor │ http://subdomain.domain/?key=value#anchor                │ /?key=value#anchor                │
  # │ http://subdomain.domain/dir/subdir/page                  ║ http     │ subdomain.domain │ /dir/subdir/page │           │        │ http://subdomain.domain/dir/subdir/page                  │ /dir/subdir/page                  │
  # │ http://subdomain.domain/dir/subdir/page?key=value        ║ http     │ subdomain.domain │ /dir/subdir/page │ key=value │        │ http://subdomain.domain/dir/subdir/page?key=value        │ /dir/subdir/page?key=value        │
  # │ http://subdomain.domain/dir/subdir/page#anchor           ║ http     │ subdomain.domain │ /dir/subdir/page │           │ anchor │ http://subdomain.domain/dir/subdir/page#anchor           │ /dir/subdir/page#anchor           │
  # │ http://subdomain.domain/dir/subdir/page?key=value#anchor ║ http     │ subdomain.domain │ /dir/subdir/page │ key=value │ anchor │ http://subdomain.domain/dir/subdir/page?key=value#anchor │ /dir/subdir/page?key=value#anchor │
  # └──────────────────────────────────────────────────────────╨──────────┴──────────────────┴──────────────────┴───────────┴────────┴──────────────────────────────────────────────────────────┴───────────────────────────────────┘

  # wrong urls:
  # ┌──────────────────────────╥──────────────────────────────────────────────────────────────────────┐
  # │ url                      ║ behavior                                                             │
  # ╞══════════════════════════╬══════════════════════════════════════════════════════════════════════╡
  # │ http://subdomain.domain/ ║ should be redirected to 'http://subdomain.domain'                    │
  # │ subdomain.domain/        ║ should be redirected to 'http://subdomain.domain'                    │
  # │ /subdomain.domain        ║ this domain described like a path (first character is the slash)     │
  # │ dir/subdir/page          ║ this path described like a domain (first character is not the slash) │
  # └──────────────────────────╨──────────────────────────────────────────────────────────────────────┘

  # note:
  # ════════════════════════════════════════════════════════════════════════════════════════════
  # 1. in the next url 'http://name:password@subdomain.domain:port/dir/subdir/page?key=value#anchor'
  #    the name, password and port values after parsing will be in the $domain property.
  #    in any case, the use of credentials in this form is deprecated.
  #    for more details see RFC 3986 clause 3.2.1 (user information) and 7.5 (sensitive information)
  # 2. anchor is not sent through the browser
  # ────────────────────────────────────────────────────────────────────────────────────────────

  # matrix check:
  # ┌───┬───────────────────────────────────────────────┐
  # │ a │                       path                    │
  # │ b │                       path +  query           │
  # │ c │                       path +           anchor │
  # │ d │                       path +  query +  anchor │
  # │ e │             domain                            │
  # │ f │             domain +  path                    │
  # │ g │             domain +  path +  query           │
  # │ h │             domain +  path +           anchor │
  # │ i │             domain +  path +  query +  anchor │
  # │ j │ protocol +  domain                            │
  # │ k │ protocol +  domain +  path                    │
  # │ l │ protocol +  domain +  path +  query           │
  # │ m │ protocol +  domain +  path +           anchor │
  # │ n │ protocol +  domain +  path +  query +  anchor │
  # └───┴───────────────────────────────────────────────┘
  #                           │
  #                           ▼
  # ┌───┬───────────────────────────────────────────────┐
  # │ a │!protocol + !domain +  path + !query + !anchor │
  # │ b │!protocol + !domain +  path +  query + !anchor │
  # │ c │!protocol + !domain +  path + !query +  anchor │
  # │ d │!protocol + !domain +  path +  query +  anchor │
  # │ e │!protocol +  domain + !path + !query + !anchor │
  # │ f │!protocol +  domain +  path + !query + !anchor │
  # │ g │!protocol +  domain +  path +  query + !anchor │
  # │ h │!protocol +  domain +  path + !query +  anchor │
  # │ i │!protocol +  domain +  path +  query +  anchor │
  # │ j │ protocol +  domain + !path + !query + !anchor │
  # │ k │ protocol +  domain +  path + !query + !anchor │
  # │ l │ protocol +  domain +  path +  query + !anchor │
  # │ m │ protocol +  domain +  path + !query +  anchor │
  # │ n │ protocol +  domain +  path +  query +  anchor │
  # └───┴───────────────────────────────────────────────┘

  public $protocol;
  public $domain;
  public $path;
  public $query;
  public $anchor;
  public $has_error;

  function __construct($url) {
    $matches = [];
    preg_match('%^(?:(?<protocol>[a-z]+)://|)'.
                    '(?<domain>[a-z0-9\\-\\.\\]\\[:@]{2,200}|)'.
                    '(?<path>[^?#]*)'.
              '(?:\\?(?<query>[^#]*)|)'.
              '(?:\\#(?<anchor>.*)|)$%S', core::sanitize_url($url), $matches);
    if ( ( empty($matches['protocol']) &&  empty($matches['domain']) && !empty($matches['path']) &&  empty($matches['query']) &&  empty($matches['anchor'])) ||  # a
         ( empty($matches['protocol']) &&  empty($matches['domain']) && !empty($matches['path']) && !empty($matches['query']) &&  empty($matches['anchor'])) ||  # b
         ( empty($matches['protocol']) &&  empty($matches['domain']) && !empty($matches['path']) &&  empty($matches['query']) && !empty($matches['anchor'])) ||  # c
         ( empty($matches['protocol']) &&  empty($matches['domain']) && !empty($matches['path']) && !empty($matches['query']) && !empty($matches['anchor'])) ||  # d
         ( empty($matches['protocol']) && !empty($matches['domain']) &&  empty($matches['path']) &&  empty($matches['query']) &&  empty($matches['anchor'])) ||  # e
         ( empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) &&  empty($matches['query']) &&  empty($matches['anchor'])) ||  # f
         ( empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) && !empty($matches['query']) &&  empty($matches['anchor'])) ||  # g
         ( empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) &&  empty($matches['query']) && !empty($matches['anchor'])) ||  # h
         ( empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) && !empty($matches['query']) && !empty($matches['anchor'])) ||  # i
         (!empty($matches['protocol']) && !empty($matches['domain']) &&  empty($matches['path']) &&  empty($matches['query']) &&  empty($matches['anchor'])) ||  # j
         (!empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) &&  empty($matches['query']) &&  empty($matches['anchor'])) ||  # k
         (!empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) && !empty($matches['query']) &&  empty($matches['anchor'])) ||  # l
         (!empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) &&  empty($matches['query']) && !empty($matches['anchor'])) ||  # m
         (!empty($matches['protocol']) && !empty($matches['domain']) && !empty($matches['path']) && !empty($matches['query']) && !empty($matches['anchor'])) ) { # n
      $this->protocol = !empty($matches['protocol']) ? $matches['protocol'] : (!empty($matches['domain']) ? 'http' : ( /* case for local ulr */ core::server_get_request_scheme()));
      $this->domain   = !empty($matches['domain'  ]) ? $matches['domain'  ] :                                        ( /* case for local ulr */ core::server_get_host());
      $this->path     = !empty($matches['path'    ]) ? $matches['path'    ] : '/';
      $this->query    = !empty($matches['query'   ]) ? $matches['query'   ] : '';
      $this->anchor   = !empty($matches['anchor'  ]) ? $matches['anchor'  ] : '';
           $this->has_error = false;
    } else $this->has_error = true;
  }

  function file_info_get() {
    return static::path_parse($this->path_get());
  }

  function type_get() {
    return ltrim(strtolower(strrchr($this->path, '.')), '.');
  }

  function protocol_get() {return $this->protocol;}
  function domain_get  () {return $this->domain;  }
  function path_get    () {return $this->path;    }
  function query_get   () {return $this->query;   }
  function anchor_get  () {return $this->anchor;  }

  function tiny_get() {
    if (!$this->has_error) {
      $result = $this->path;
      if ($this->query ) $result.= '?'.$this->query;
      if ($this->anchor) $result.= '#'.$this->anchor;
      return $result;
    }
  }

  function full_get() {
    if (!$this->has_error) {
      $result = $this->protocol.'://'.$this->domain.$this->path;
      if ($this->query ) $result.= '?'.$this->query;
      if ($this->anchor) $result.= '#'.$this->anchor;
      return rtrim($result, '/');
    }
  }

  function query_arg_select($name)         {$args = []; parse_str($this->query, $args); return $args[$name] ?? null;}
  function query_arg_insert($name, $value) {$args = []; parse_str($this->query, $args); $args[$name] = $value; $this->query = http_build_query($args);}
  function query_arg_delete($name)         {$args = []; parse_str($this->query, $args); unset($args[$name]);   $this->query = http_build_query($args);}

  function path_arg_select($name) {
    $args = explode('/', $this->path);
    return $args[$name] ?? null;
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache == null) {
      static::$cache = new static(core::server_get_request_uri());
    }
  }

  static function path_parse($path) {
  # each path should begin with '/' and have at least one more character
    if (strlen($path) == 0 || $path[0] !== '/') return;
    $result = new \stdClass;
    $result->dirs = '';
    $result->name = '';
    $result->type = '';
    $full_name = substr(strrchr($path, '/'), 1);
    if ($full_name === false || $full_name === '' || $full_name === '..' || $full_name === '.') return;
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

  static function get_current() {
    static::init();
    return static::$cache;
  }

  static function back_url_get() {
    $url = new url(urldecode(static::get_current()->query_arg_select('back')));
    return core::validate_url($url->full_get()) ?: '';
  }

  static function back_part_make() {
    return 'back='.urlencode(static::get_current()->tiny_get());
  }

  static function is_local($url) {
    return (new static($url))->domain == core::server_get_host();
  }

  static function is_active($url, $compare_type = 'full') {
    $checked_url = new static($url);
    $current_url =     static::get_current();
    switch ($compare_type) {
      case 'full': return $checked_url->full_get() ==
                          $current_url->full_get();
      case 'path': return $checked_url->domain_get().$checked_url->path_get() ==
                          $current_url->domain_get().$current_url->path_get();
    }
  }

  static function is_active_trail($url) {
    $checked_url = new static($url);
    $current_url =     static::get_current();
    return strpos($current_url->full_get().'/',
                  $checked_url->full_get().'/') === 0;
  }

  static function go($url) {
    $messages = message::select_all(false);
    foreach ($messages as $c_type => $c_messages)
      foreach ($c_messages as $c_message)
        message::insert_to_storage($c_message, $c_type);
    core::send_header_and_exit('redirect', null, null,
      (new static($url))->full_get()
    );
  }

}}