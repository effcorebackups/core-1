<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class url {

  # valid urls:
  # ┌────────────────────────────────────────────────────────────╥──────────┬────────────────────────────┬────────────────────┬─────────────┬──────────┬────────────────────────────────────────────────────────────────────┬─────────────────────────────────────┐
  # │ url                                                        ║ protocol │ domain                     │ path               │ query       │ anchor   │ full_get                                                           │ tiny_get                            │
  # ╞════════════════════════════════════════════════════════════╬══════════╪════════════════════════════╪════════════════════╪═════════════╪══════════╪════════════════════════════════════════════════════════════════════╪═════════════════════════════════════╡
  # │                        '/'                                 ║ 'http'   │ 'current.subdomain.domain' │ '/'                │ ''          │ ''       │ 'http://current.subdomain.domain'                                  │ '/'                                 │
  # │                        '/?key=value'                       ║ 'http'   │ 'current.subdomain.domain' │ '/'                │ 'key=value' │ ''       │ 'http://current.subdomain.domain/?key=value'                       │ '/?key=value'                       │
  # │                        '/#anchor'                          ║ 'http'   │ 'current.subdomain.domain' │ '/'                │ ''          │ 'anchor' │ 'http://current.subdomain.domain/#anchor'                          │ '/#anchor'                          │
  # │                        '/?key=value#anchor'                ║ 'http'   │ 'current.subdomain.domain' │ '/'                │ 'key=value' │ 'anchor' │ 'http://current.subdomain.domain/?key=value#anchor'                │ '/?key=value#anchor'                │
  # │                        '/dir/subdir/page'                  ║ 'http'   │ 'current.subdomain.domain' │ '/dir/subdir/page' │ ''          │ ''       │ 'http://current.subdomain.domain/dir/subdir/page'                  │ '/dir/subdir/page'                  │
  # │                        '/dir/subdir/page?key=value'        ║ 'http'   │ 'current.subdomain.domain' │ '/dir/subdir/page' │ 'key=value' │ ''       │ 'http://current.subdomain.domain/dir/subdir/page?key=value'        │ '/dir/subdir/page?key=value'        │
  # │                        '/dir/subdir/page#anchor'           ║ 'http'   │ 'current.subdomain.domain' │ '/dir/subdir/page' │ ''          │ 'anchor' │ 'http://current.subdomain.domain/dir/subdir/page#anchor'           │ '/dir/subdir/page#anchor'           │
  # │                        '/dir/subdir/page?key=value#anchor' ║ 'http'   │ 'current.subdomain.domain' │ '/dir/subdir/page' │ 'key=value' │ 'anchor' │ 'http://current.subdomain.domain/dir/subdir/page?key=value#anchor' │ '/dir/subdir/page?key=value#anchor' │
  # │        'subdomain.domain'                                  ║ 'http'   │         'subdomain.domain' │ '/'                │ ''          │ ''       │         'http://subdomain.domain'                                  │ '/'                                 │
  # │        'subdomain.domain/?key=value'                       ║ 'http'   │         'subdomain.domain' │ '/'                │ 'key=value' │ ''       │         'http://subdomain.domain/?key=value'                       │ '/?key=value'                       │
  # │        'subdomain.domain/#anchor'                          ║ 'http'   │         'subdomain.domain' │ '/'                │ ''          │ 'anchor' │         'http://subdomain.domain/#anchor'                          │ '/#anchor'                          │
  # │        'subdomain.domain/?key=value#anchor'                ║ 'http'   │         'subdomain.domain' │ '/'                │ 'key=value' │ 'anchor' │         'http://subdomain.domain/?key=value#anchor'                │ '/?key=value#anchor'                │
  # │        'subdomain.domain/dir/subdir/page'                  ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ ''          │ ''       │         'http://subdomain.domain/dir/subdir/page'                  │ '/dir/subdir/page'                  │
  # │        'subdomain.domain/dir/subdir/page?key=value'        ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ 'key=value' │ ''       │         'http://subdomain.domain/dir/subdir/page?key=value'        │ '/dir/subdir/page?key=value'        │
  # │        'subdomain.domain/dir/subdir/page#anchor'           ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ ''          │ 'anchor' │         'http://subdomain.domain/dir/subdir/page#anchor'           │ '/dir/subdir/page#anchor'           │
  # │        'subdomain.domain/dir/subdir/page?key=value#anchor' ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ 'key=value' │ 'anchor' │         'http://subdomain.domain/dir/subdir/page?key=value#anchor' │ '/dir/subdir/page?key=value#anchor' │
  # │ 'http://subdomain.domain'                                  ║ 'http'   │         'subdomain.domain' │ '/'                │ ''          │ ''       │         'http://subdomain.domain'                                  │ '/'                                 │
  # │ 'http://subdomain.domain/?key=value'                       ║ 'http'   │         'subdomain.domain' │ '/'                │ 'key=value' │ ''       │         'http://subdomain.domain/?key=value'                       │ '/?key=value'                       │
  # │ 'http://subdomain.domain/#anchor'                          ║ 'http'   │         'subdomain.domain' │ '/'                │ ''          │ 'anchor' │         'http://subdomain.domain/#anchor'                          │ '/#anchor'                          │
  # │ 'http://subdomain.domain/?key=value#anchor'                ║ 'http'   │         'subdomain.domain' │ '/'                │ 'key=value' │ 'anchor' │         'http://subdomain.domain/?key=value#anchor'                │ '/?key=value#anchor'                │
  # │ 'http://subdomain.domain/dir/subdir/page'                  ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ ''          │ ''       │         'http://subdomain.domain/dir/subdir/page'                  │ '/dir/subdir/page'                  │
  # │ 'http://subdomain.domain/dir/subdir/page?key=value'        ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ 'key=value' │ ''       │         'http://subdomain.domain/dir/subdir/page?key=value'        │ '/dir/subdir/page?key=value'        │
  # │ 'http://subdomain.domain/dir/subdir/page#anchor'           ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ ''          │ 'anchor' │         'http://subdomain.domain/dir/subdir/page#anchor'           │ '/dir/subdir/page#anchor'           │
  # │ 'http://subdomain.domain/dir/subdir/page?key=value#anchor' ║ 'http'   │         'subdomain.domain' │ '/dir/subdir/page' │ 'key=value' │ 'anchor' │         'http://subdomain.domain/dir/subdir/page?key=value#anchor' │ '/dir/subdir/page?key=value#anchor' │
  # └────────────────────────────────────────────────────────────╨──────────┴────────────────────────────┴────────────────────┴─────────────┴──────────┴────────────────────────────────────────────────────────────────────┴─────────────────────────────────────┘

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

  # PCRE note:
  # ═══╦════════════════════════════════════════════════════════════════════
  # L  ║ Letter (Includes the following properties: Ll, Lm, Lo, Lt and Lu.)
  # Ll ║ Lower case letter
  # Lm ║ Modifier letter
  # Lo ║ Other letter
  # Lt ║ Title case letter
  # Lu ║ Upper case letter
  # ───╨────────────────────────────────────────────────────────────────────
  # utf8 letters: '[:alpha:]' === '[\\p{L}]' === '[\\p{Ll}\\p{Lm}\\p{Lo}\\p{Lt}\\p{Lu}]'
  # officially allowed characters: ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_,;:.!?+-*/='"`^~(){}[]<>|\$#@%&
  # officially allowed characters ACSII range: \\x21-\\x7e

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

  const is_decode_nothing   = 0b00;
  const is_decode_domain    = 0b01;
  const is_decode_path      = 0b10;
  const valid_unicode_range = '\\p{Ll}\\p{Lo}\\p{Lt}\\p{Lu}';

  public $raw;
  public $protocol;
  public $domain;
  public $port;
  public $path;
  public $query;
  public $anchor;
  public $has_error;

  function __construct($url, $options = []) {
    $options += ['completion' => true, 'decode' => static::is_decode_path, 'extra' => static::valid_unicode_range];
    $this->raw = $url;
    $matches = [];
    preg_match('%^(?:(?<protocol>[a-zA-Z]+)://|)'.
                      '(?<domain>['.$options['extra'].'a-zA-Z0-9\\-\\.]{2,200}(?:\\:(?<port>[0-9]{1,5})|)|)'.
                       '(?<path>/['.$options['extra'].'\\x21-\\x22\\x24-\\x3e\\x40-\\x7e]*|)'. # \\x21-\\x7e + [^?#] === \\x21-\\x22 + \\x24-\\x3e + \\x40-\\x7e
                 '(?:\\?(?<query>['.$options['extra'].'\\x21-\\x22\\x24-\\x7e]*)|)'.           # \\x21-\\x7e +  [^#] === \\x21-\\x22 + \\x24-\\x7e
                '(?:\\#(?<anchor>['.$options['extra'].'\\x21-\\x7e]*)|)$%uS', $url, $matches);
    $protocol = array_key_exists('protocol', $matches) ? $matches['protocol'] : '';
    $domain   = array_key_exists('domain',   $matches) ? $matches['domain'  ] : '';
    $port     = array_key_exists('port',     $matches) ? $matches['port'    ] : '';
    $path     = array_key_exists('path',     $matches) ? $matches['path'    ] : '';
    $query    = array_key_exists('query',    $matches) ? $matches['query'   ] : '';
    $anchor   = array_key_exists('anchor',   $matches) ? $matches['anchor'  ] : '';
    if ( (!strlen($protocol) && !strlen($domain) &&  strlen($path) && !strlen($query) && !strlen($anchor)) ||  # a
         (!strlen($protocol) && !strlen($domain) &&  strlen($path) &&  strlen($query) && !strlen($anchor)) ||  # b
         (!strlen($protocol) && !strlen($domain) &&  strlen($path) && !strlen($query) &&  strlen($anchor)) ||  # c
         (!strlen($protocol) && !strlen($domain) &&  strlen($path) &&  strlen($query) &&  strlen($anchor)) ||  # d
         (!strlen($protocol) &&  strlen($domain) && !strlen($path) && !strlen($query) && !strlen($anchor)) ||  # e
         (!strlen($protocol) &&  strlen($domain) &&  strlen($path) && !strlen($query) && !strlen($anchor)) ||  # f
         (!strlen($protocol) &&  strlen($domain) &&  strlen($path) &&  strlen($query) && !strlen($anchor)) ||  # g
         (!strlen($protocol) &&  strlen($domain) &&  strlen($path) && !strlen($query) &&  strlen($anchor)) ||  # h
         (!strlen($protocol) &&  strlen($domain) &&  strlen($path) &&  strlen($query) &&  strlen($anchor)) ||  # i
         ( strlen($protocol) &&  strlen($domain) && !strlen($path) && !strlen($query) && !strlen($anchor)) ||  # j
         ( strlen($protocol) &&  strlen($domain) &&  strlen($path) && !strlen($query) && !strlen($anchor)) ||  # k
         ( strlen($protocol) &&  strlen($domain) &&  strlen($path) &&  strlen($query) && !strlen($anchor)) ||  # l
         ( strlen($protocol) &&  strlen($domain) &&  strlen($path) && !strlen($query) &&  strlen($anchor)) ||  # m
         ( strlen($protocol) &&  strlen($domain) &&  strlen($path) &&  strlen($query) &&  strlen($anchor)) ) { # n
      $this->protocol = $protocol;
      $this->domain   = $domain;
      $this->port     = $port;
      $this->path     = $path;
      $this->query    = $query;
      $this->anchor   = $anchor;
      if ($options['completion'] && $this->protocol === '') $this->protocol = $this->domain === core::server_get_host(false) ? core::server_get_request_scheme() : 'http';
      if ($options['completion'] && $this->domain   === '') $this->domain   =                   core::server_get_host(false);
      if ($options['completion'] && $this->path     === '') $this->path     = '/';
      if ($options['decode'] & static::is_decode_domain && function_exists('idn_to_utf8') && idn_to_utf8($this->domain)) $this->domain = idn_to_utf8($this->domain);
      if ($options['decode'] & static::is_decode_path) $this->path = rawurldecode($this->path);
           $this->has_error = false;
    } else $this->has_error = true;
  }

  function protocol_get() {return $this->protocol;}
  function domain_get  () {return $this->domain;  }
  function path_get    () {return $this->path;    }
  function query_get   () {return $this->query;   }
  function anchor_get  () {return $this->anchor;  }

  function tiny_get() {
    if (!$this->has_error) {
      $result = $this->path_get();
      if ($this->query_get ()) $result.= '?'.$this->query_get ();
      if ($this->anchor_get()) $result.= '#'.$this->anchor_get();
      return $result;
    }
  }

  function full_get() {
    if (!$this->has_error) {
      $result = $this->protocol_get().'://'.$this->domain_get().$this->path_get();
      if ($this->query_get ()) $result.= '?'.$this->query_get ();
      if ($this->anchor_get()) $result.= '#'.$this->anchor_get();
      return rtrim($result, '/');
    }
  }

  function query_arg_select($name        ) {if ($this->has_error) return; $args = []; parse_str($this->query_get(), $args); return $args[$name] ?? null;                                         }
  function query_arg_insert($name, $value) {if ($this->has_error) return; $args = []; parse_str($this->query_get(), $args);        $args[$name] = $value; $this->query = http_build_query($args);}
  function query_arg_delete($name        ) {if ($this->has_error) return; $args = []; parse_str($this->query_get(), $args);  unset($args[$name]);         $this->query = http_build_query($args);}

  function path_arg_select($name) {
    if (!$this->has_error) {
      $args = explode('/', $this->path_get());
      return $args[$name] ?? null;
    }
  }

  function file_info_get() {
    if (!$this->has_error) {
      return new file(rtrim(dir_root, '/').$this->path_get());
    }
  }

  function file_type_get() {
    if (!$this->has_error) {
      return ltrim(strtolower(strrchr($this->path_get(), '.')), '.');
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $cache;

  static function cache_cleaning() {
    static::$cache = null;
  }

  static function init() {
    if (static::$cache === null)
        static::$cache = new static(core::server_get_request_uri());
  }

  static function get_current() {
    static::init();
    return static::$cache;
  }

  static function back_url_get() {
    $url = new static(static::get_current()->query_arg_select('back'));
    return $url->has_error ? '' :
           $url->full_get();
  }

  static function back_part_make_custom($url) {
    return 'back='.urlencode($url);
  }

  static function back_part_make($full = false) {
    if ($full) return static::back_part_make_custom(static::get_current()->full_get());
    else       return static::back_part_make_custom(static::get_current()->tiny_get());
  }

  static function is_local($url, $decode = false) {
    return (new static($url, ['decode' => $decode ? static::is_decode_domain : static::is_decode_nothing]))->domain_get() === core::server_get_host($decode);
  }

  static function is_active($url, $compare_type = 'full') {
    $checked_url = new static($url);
    $current_url =     static::get_current();
    switch ($compare_type) {
      case 'full': return $checked_url->full_get() ===
                          $current_url->full_get();
      case 'path': return $checked_url->domain_get().$checked_url->path_get() ===
                          $current_url->domain_get().$current_url->path_get();
    }
  }

  static function is_active_trail($url) {
    $checked_url = new static($url);
    $current_url =     static::get_current();
    return strpos($current_url->full_get().'/',
                  $checked_url->full_get().'/') === 0;
  }

  static function utf8_encode($value, $prefix = '', $range = self::valid_unicode_range) {
    return preg_replace_callback('%(?<char>['.$range.'])%uS', function ($c_match) use ($prefix) {
      if (strlen($c_match['char']) === 1) return                               $c_match['char'][0];
      if (strlen($c_match['char']) === 2) return $prefix.strtoupper(dechex(ord($c_match['char'][0]))).$prefix.strtoupper(dechex(ord($c_match['char'][1])));
      if (strlen($c_match['char']) === 3) return $prefix.strtoupper(dechex(ord($c_match['char'][0]))).$prefix.strtoupper(dechex(ord($c_match['char'][1]))).$prefix.strtoupper(dechex(ord($c_match['char'][2])));
      if (strlen($c_match['char']) === 4) return $prefix.strtoupper(dechex(ord($c_match['char'][0]))).$prefix.strtoupper(dechex(ord($c_match['char'][1]))).$prefix.strtoupper(dechex(ord($c_match['char'][2]))).$prefix.strtoupper(dechex(ord($c_match['char'][3])));
    }, $value);
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