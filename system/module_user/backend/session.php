<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class session {

  static protected $current;

  static function select_all_by_id_user($id_user) {
    return entity::get('session')->instances_select([
      'conditions' => [
        'id_user_!f' => 'id_user', 'operator' => '=',
        'id_user_!v' => $id_user], 'order'    => [
        'expired_!f' => 'expired', 'DESC']], 'id'
    );
  }

  static function select() {
    $session_id       = static::id_get();
    $session_hex_type = static::id_extract_hex_type($session_id);
    if ($session_hex_type == 'f') {
      if (!static::$current)
           static::$current = (new instance('session', ['id' => $session_id]))->select();
      if (!static::$current) {
        static::id_regenerate('a');
        message::insert('invalid session was deleted!', 'warning');
        return null;
      } else {
        return static::$current;
      }
    }
  }

  static function insert($id_user, $session_params = []) {
    $is_remember = isset($session_params['is_remember']);
    $is_fixed_ip = isset($session_params['is_fixed_ip']);
    $period = !$is_remember ? core::date_period_d : core::date_period_m;
    static::id_regenerate('f', $session_params);
    (new instance('session', [
      'id'          => static::id_get(),
      'id_user'     => $id_user,
      'is_remember' => $is_remember ? 1 : 0,
      'is_fixed_ip' => $is_fixed_ip ? 1 : 0,
      'expired'     => core::datetime_get('+'.$period.' second'),
      'data'        => (object)['user_agent' => core::server_get_user_agent(2048)]
    ]))->insert();
  }

  static function delete($id_user, $id_session = null) {
    $result = (new instance('session', [
      'id'      => $id_session ?: static::id_get(),
      'id_user' => $id_user
    ]))->delete();
  # regenerate id_session if session is current
    if ($id_session == null) static::id_regenerate('a');
    return $result;
  }

  static function cleaning() {
    entity::get('session')->instances_delete([
      'conditions' => ['expired_!f' => 'expired', '<', 'expired_!v' => core::datetime_get()]
    ]);
  }

  ####################################
  ### functionality for session_id ###
  ####################################

  # ┌─────────────┬───────────┬───────────┬────────┬───────────────────┬───────┐
  # │ session id  │ anonymous │ remember? │ on ip? │ secure │ on https │ used? │
  # ╞═════════════╪═══════════╪═══════════╪════════╪═══════════════════╪═══════╡
  # │ a--00--00-- │ yes       │ no        │ no     │ n/a    | n/a      │ no    │
  # │ a--00--ip-- │ yes       │ no        │ yes    │ +      | ++       │ yes   │
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
    $cookie_domain = storage::get('files')->select('settings/core/cookie_domain');
    $is_remember = isset($session_params['is_remember']);
    $is_fixed_ip = isset($session_params['is_fixed_ip']);
    if ($hex_type == 'f' && $is_remember == false) $expired = time() + core::date_period_d;
    if ($hex_type == 'f' && $is_remember)          $expired = time() + core::date_period_m;
    if ($hex_type == 'a')                          $expired = 0;
    if ($hex_type == 'f' && $is_fixed_ip == false) $ip = core::empty_ip;
    if ($hex_type == 'f' && $is_fixed_ip)          $ip = core::server_get_addr_remote();
    if ($hex_type == 'a')                          $ip = core::server_get_addr_remote();
  # $hex_type: a - anonymous user | f - authenticated user
    $hex_expired       = static::id_get_hex_expired($expired);
    $hex_ip            = static::id_get_hex_ip($ip);
    $hex_uagent_hash_8 = static::id_get_hex_uagent_hash_8();
    $hex_random        = static::id_get_hex_random();
    $session_id = $hex_type.          # strlen == 1
                  $hex_expired.       # strlen == 8
                  $hex_ip.            # strlen == 32
                  $hex_uagent_hash_8. # strlen == 8
                  $hex_random;        # strlen == 8
    $session_id.= core::signature_get($session_id, 'session', 8);
    setcookie('session_id', ($_COOKIE['session_id'] = $session_id), $expired, '/', $cookie_domain);
    setcookie('cookies_is_enabled', 'true',                         $expired, '/', $cookie_domain);
    return $session_id;
  }

  static function id_get() {
    if (static::id_check($_COOKIE['session_id'] ?? ''))
           return        $_COOKIE['session_id'];
      else return static::id_regenerate('a');
  }

  static function id_get_hex_expired($expired) {return str_pad(dechex($expired), 8, '0', STR_PAD_LEFT);}
  static function id_get_hex_ip          ($ip) {return core::ip_to_hex($ip);}
  static function id_get_hex_uagent_hash_8  () {return core::hash_get_mini(core::server_get_user_agent(80));} # note: why 80? when you add a page to your favourites in Safari the browser sends a user-agent header with a shorter string length than usual
  static function id_get_hex_random         () {return str_pad(dechex(random_int(0, 0x7fffffff)), 8, '0', STR_PAD_LEFT);}
  static function id_get_hex_signature   ($id) {return core::signature_get(substr($id, 0, 56 + 1), 'session', 8);}

  static function id_extract_expired          ($id) {return hexdec(static::id_extract_hex_expired($id));}
  static function id_extract_hex_expired      ($id) {return substr($id,      1,  8);}
  static function id_extract_hex_type         ($id) {return substr($id,      0,  1);}
  static function id_extract_hex_ip           ($id) {return substr($id,  8 + 1, 32);}
  static function id_extract_hex_uagent_hash_8($id) {return substr($id, 40 + 1,  8);}
  static function id_extract_hex_random       ($id) {return substr($id, 48 + 1,  8);}
  static function id_extract_hex_signature    ($id) {return substr($id, 56 + 1,  8);}

  static function id_check($id) {
    if (core::validate_hash($id, 65)) {
      $expired           = static::id_extract_expired          ($id);
      $hex_type          = static::id_extract_hex_type         ($id);
      $hex_ip            = static::id_extract_hex_ip           ($id);
      $hex_uagent_hash_8 = static::id_extract_hex_uagent_hash_8($id);
      $hex_signature     = static::id_extract_hex_signature    ($id);
      if (($hex_type === 'a' && $expired === 0) ||
          ($hex_type === 'f' && $expired >= time())) {
        if ($hex_signature === static::id_get_hex_signature($id)) {
          if ($hex_uagent_hash_8 === static::id_get_hex_uagent_hash_8()) {
            if (($hex_type === 'a' && $hex_ip === core::ip_to_hex(core::server_get_addr_remote())) ||
                ($hex_type === 'f' && $hex_ip === core::ip_to_hex(core::server_get_addr_remote())) ||
                ($hex_type === 'f' && $hex_ip === core::ip_to_hex(core::empty_ip))) {
              return true;
            }
          }
        }
      }
    }
  }

}}