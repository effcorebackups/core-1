<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class session {

  const period_expire_d = 60 * 60 * 24;
  const period_expire_m = 60 * 60 * 24 * 30;
  const empty_ip = '::';

  static function select() {
    $session_id = static::id_get();
    if ($session_id[0] == 'f') {
      $session = (new instance('session', [
        'id' => $session_id
      ]))->select();
      if (!$session) {
        static::id_regenerate('a');
        message::insert('invalid session was deleted!', 'warning');
        return null;
      } else {
        return $session;
      }
    }
  }

  static function insert($nick, $session_params = []) {
    $is_remember = isset($session_params['is_remember']);
    $is_fixed_ip = isset($session_params['is_fixed_ip']);
    $period = !$is_remember ? static::period_expire_d : static::period_expire_m;
    static::id_regenerate('f', $session_params);
    (new instance('session', [
      'id'          => static::id_get(),
      'nick'        => $nick,
      'is_remember' => $is_remember ? 1 : 0,
      'is_fixed_ip' => $is_fixed_ip ? 1 : 0,
      'expire'      => core::datetime_get('+'.$period.' second'),
    ]))->insert();
  }

  static function delete($nick) {
    (new instance('session', [
      'id'   => static::id_get(),
      'nick' => $nick
    ]))->delete();
    static::id_regenerate('a');
  }

  ####################################
  ### functionality for session_id ###
  ####################################

  # ┌───────────┬───────────┬────────┬─────────────┬────────────────┬─────────────┬───────┐
  # │ anonymous │ remember? │ on ip? │ session id  │ do not track?  │ is secure?  │ used? │
  # ╞═══════════╪═══════════╪════════╪═════════════╪════════════════╪═════════════╪═══════╡
  # │ yes       │ no        │ no     │ a--01--00-- │ yes            │ no          │ -     │
  # │ yes       │ no        │ yes    │ a--01--ip-- │ no             │ no          │ -     │
  # │ yes       │ yes       │ no     │ a--30--00-- │ no             │ no          │ -     │
  # │ yes       │ yes       │ yes    │ a--30--ip-- │ no             │ on https    │ +     │
  # ├───────────┼───────────┼────────┼─────────────┼────────────────┼─────────────┼───────┤
  # │ no        │ no        │ no     │ f--01--00-- │ no - logged in │ no          │ +     │
  # │ no        │ no        │ yes    │ f--01--ip-- │ no - logged in │ no          │ +     │
  # │ no        │ yes       │ no     │ f--30--00-- │ no - logged in │ no          │ +     │
  # │ no        │ yes       │ yes    │ f--30--ip-- │ no - logged in │ on https    │ +     │
  # └───────────┴───────────┴────────┴─────────────┴────────────────┴─────────────┴───────┘

  static function id_regenerate($hex_type, $session_params = []) {
    $is_remember = isset($session_params['is_remember']);
    $is_fixed_ip = isset($session_params['is_fixed_ip']);
    $period      = $hex_type == 'f' && !$is_remember ? static::period_expire_d : static::period_expire_m;
    $ip          = $hex_type == 'f' && !$is_fixed_ip ? static::empty_ip : core::server_remote_addr_get();
  # $hex_type: a - anonymous user | f - authenticated user
    $hex_expire        = static::id_hex_expire_get($period);
    $hex_ip            = static::id_hex_ip_get($ip);
    $hex_uagent_hash_8 = static::id_hex_uagent_hash_8_get();
    $hex_random        = static::id_hex_random_get();
    $session_id = $hex_type.          # strlen == 1
                  $hex_expire.        # strlen == 8
                  $hex_ip.            # strlen == 32
                  $hex_uagent_hash_8. # strlen == 8
                  $hex_random;        # strlen == 8
    $session_id.= core::signature_get($session_id, 8, 'session');
    setcookie('session_id', ($_COOKIE['session_id'] = $session_id), time() + $period, '/');
    setcookie('cookies_is_on', 'true',                              time() + $period, '/');
    return $session_id;
  }

  static function id_get() {
    if (static::id_check($_COOKIE['session_id'] ?? ''))
           return        $_COOKIE['session_id'];
      else return static::id_regenerate('a');
  }

  static function id_hex_expire_get($period) {return dechex(time() + $period);}
  static function id_hex_ip_get($ip)         {return core::ip_to_hex($ip);}
  static function id_hex_uagent_hash_8_get() {return substr(md5(core::server_user_agent_get()), 0, 8);}
  static function id_hex_random_get()        {return str_pad(dechex(random_int(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);}
  static function id_hex_signature_get($id)  {return core::signature_get(substr($id, 0, 56 + 1), 8, 'session');}

  static function id_expire_extract($id)            {return hexdec(substr($id, 1, 8));}
  static function id_hex_type_extract($id)          {return substr($id,      0,  1);}
  static function id_hex_ip_extract($id)            {return substr($id,  8 + 1, 32);}
  static function id_hex_uagent_hash_8_extract($id) {return substr($id, 40 + 1,  8);}
  static function id_hex_random_extract($id)        {return substr($id, 48 + 1,  8);}
  static function id_hex_signature_extract($id)     {return substr($id, 56 + 1,  8);}

  static function id_check($id) {
    if (core::validate_hash($id, 65)) {
      $expire            = static::id_expire_extract($id);
      $hex_type          = static::id_hex_type_extract($id);
      $hex_ip            = static::id_hex_ip_extract($id);
      $hex_uagent_hash_8 = static::id_hex_uagent_hash_8_extract($id);
      $hex_signature     = static::id_hex_signature_extract($id);
      if ($expire >= time()                                         &&
          $hex_uagent_hash_8 === static::id_hex_uagent_hash_8_get() &&
          $hex_signature     === static::id_hex_signature_get($id)) {
        if (($hex_type === 'a' && $hex_ip === core::ip_to_hex(core::server_remote_addr_get())) ||
            ($hex_type === 'f' && $hex_ip === core::ip_to_hex(core::server_remote_addr_get())) ||
            ($hex_type === 'f' && $hex_ip === core::ip_to_hex(static::empty_ip))) {
          return true;
        }
      }
    }
  }

}}