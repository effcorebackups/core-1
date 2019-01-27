<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class session {

  const period_expired_d = 60 * 60 * 24;
  const period_expired_m = 60 * 60 * 24 * 30;
  const empty_ip = '::';

  static function select() {
    $session_id       = static::id_get();
    $session_hex_type = static::id_hex_type_extract($session_id);
    if ($session_hex_type == 'f') {
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

  static function insert($id_user, $session_params = []) {
    $is_remember = isset($session_params['is_remember']);
    $is_fixed_ip = isset($session_params['is_fixed_ip']);
    $period = !$is_remember ? static::period_expired_d : static::period_expired_m;
    static::id_regenerate('f', $session_params);
    (new instance('session', [
      'id'          => static::id_get(),
      'id_user'     => $id_user,
      'is_remember' => $is_remember ? 1 : 0,
      'is_fixed_ip' => $is_fixed_ip ? 1 : 0,
      'expired'     => core::datetime_get('+'.$period.' second'),
    ]))->insert();
  }

  static function delete($id_user) {
    (new instance('session', [
      'id'      => static::id_get(),
      'id_user' => $id_user
    ]))->delete();
    static::id_regenerate('a');
  }

  static function cleaning() {
    $storage = $s = storage::get(entity::get('session')->storage_name);
    $catalog_name =              entity::get('session')->catalog_name;
    $storage->query('DELETE', 'FROM', $s->tables($catalog_name), 'WHERE', $s->condition('expired', core::datetime_get(), '<'));
  }

  ####################################
  ### functionality for session_id ###
  ####################################

  # ┌─────────────┬───────────┬───────────┬────────┬───────────────────┬───────┐
  # │ session id  │ anonymous │ remember? │ on ip? │ secure │ on https │ used? │
  # ╞═════════════╪═══════════╪═══════════╪════════╪═══════════════════╪═══════╡
  # │ a--01--00-- │ yes       │ no        │ no     │ n/a    | n/a      │ no    │
  # │ a--01--ip-- │ yes       │ no        │ yes    │ +      | ++       │ yes   │
  # │ a--30--00-- │ yes       │ yes       │ no     │ n/a    | n/a      │ no    │
  # │ a--30--ip-- │ yes       │ yes       │ yes    │ n/a    | n/a      │ no    │
  # ├─────────────┼───────────┼───────────┼────────┼───────────────────┼───────┤
  # │ f--01--00-- │ no        │ no        │ no     │ +      | ++       │ yes   │
  # │ f--01--ip-- │ no        │ no        │ yes    │ ++     | +++      │ yes   │
  # │ f--30--00-- │ no        │ yes       │ no     │ +      | ++       │ yes   │
  # │ f--30--ip-- │ no        │ yes       │ yes    │ ++     | +++      │ yes   │
  # └─────────────┴───────────┴───────────┴────────┴───────────────────┴───────┘
  # note: n/a = not applicable

  static function id_regenerate($hex_type, $session_params = []) {
    $is_remember = isset($session_params['is_remember']);
    $is_fixed_ip = isset($session_params['is_fixed_ip']);
    if ($hex_type == 'f' && $is_remember == false) $expired = time() + static::period_expired_d;
    if ($hex_type == 'f' && $is_remember)          $expired = time() + static::period_expired_m;
    if ($hex_type == 'a')                          $expired = time() + static::period_expired_d;
    if ($hex_type == 'f' && $is_fixed_ip == false) $ip = static::empty_ip;
    if ($hex_type == 'f' && $is_fixed_ip)          $ip = core::server_remote_addr_get();
    if ($hex_type == 'a')                          $ip = core::server_remote_addr_get();
  # $hex_type: a - anonymous user | f - authenticated user
    $hex_expired       = static::id_hex_expired_get($expired);
    $hex_ip            = static::id_hex_ip_get($ip);
    $hex_uagent_hash_8 = static::id_hex_uagent_hash_8_get();
    $hex_random        = static::id_hex_random_get();
    $session_id = $hex_type.          # strlen == 1
                  $hex_expired.       # strlen == 8
                  $hex_ip.            # strlen == 32
                  $hex_uagent_hash_8. # strlen == 8
                  $hex_random;        # strlen == 8
    $session_id.= core::signature_get($session_id, 8, 'session');
    setcookie('session_id', ($_COOKIE['session_id'] = $session_id), $expired, '/');
    setcookie('cookies_is_on', 'true',                              $expired, '/');
    return $session_id;
  }

  static function id_get() {
    if (static::id_check($_COOKIE['session_id'] ?? ''))
           return        $_COOKIE['session_id'];
      else return static::id_regenerate('a');
  }

  static function id_hex_expired_get($expired) {return dechex($expired);}
  static function id_hex_ip_get($ip)           {return core::ip_to_hex($ip);}
  static function id_hex_uagent_hash_8_get()   {return core::mini_hash_get(core::server_user_agent_get());}
  static function id_hex_random_get()          {return str_pad(dechex(random_int(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);}
  static function id_hex_signature_get($id)    {return core::signature_get(substr($id, 0, 56 + 1), 8, 'session');}

  static function id_expired_extract($id)           {return hexdec(static::id_hex_expired_extract($id));}
  static function id_hex_expired_extract($id)       {return substr($id,      1,  8);}
  static function id_hex_type_extract($id)          {return substr($id,      0,  1);}
  static function id_hex_ip_extract($id)            {return substr($id,  8 + 1, 32);}
  static function id_hex_uagent_hash_8_extract($id) {return substr($id, 40 + 1,  8);}
  static function id_hex_random_extract($id)        {return substr($id, 48 + 1,  8);}
  static function id_hex_signature_extract($id)     {return substr($id, 56 + 1,  8);}

  static function id_check($id) {
    if (core::validate_hash($id, 65)) {
      $expired           = static::id_expired_extract($id);
      $hex_type          = static::id_hex_type_extract($id);
      $hex_ip            = static::id_hex_ip_extract($id);
      $hex_uagent_hash_8 = static::id_hex_uagent_hash_8_extract($id);
      $hex_signature     = static::id_hex_signature_extract($id);
      if ($expired >= time()                                        &&
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