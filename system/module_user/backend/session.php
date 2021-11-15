<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          abstract class session {

  static protected $current;

  static function select_all_by_id_user($id_user) {
    return entity::get('session')->instances_select([
      'conditions' => [
        'id_user_!f'       => 'id_user',
        'id_user_operator' => '=',
        'id_user_!v'       => $id_user],
      'order' => ['expired_!f' => 'expired', 'DESC']], 'id'
    );
  }

  static function select() {
    $session_id       = static::id_get();
    $session_hex_type = static::id_extract_hex_type($session_id);
    if ($session_hex_type === 'f') {
      if (!static::$current)
           static::$current = (new instance('session', ['id' => $session_id]))->select();
      if (!static::$current) {
        static::id_regenerate('a');
        message::insert('Invalid session was deleted!', 'warning');
        return null;
      } else {
        return static::$current;
      }
    }
  }

  static function insert($id_user, $session_params = []) {
    $settings = module::settings_get('user');
    $is_long_session = isset($session_params['is_long_session']);
    $is_fixed_ip     = isset($session_params['is_fixed_ip']);
    if ($is_long_session === false) $period = $settings->session_duration_min * core::date_period_d;
    if ($is_long_session !== false) $period = $settings->session_duration_max * core::date_period_d;
    static::id_regenerate('f', $session_params);
    (new instance('session', [
      'id'              => static::id_get(),
      'id_user'         => $id_user,
      'is_long_session' => $is_long_session ? 1 : 0,
      'is_fixed_ip'     => $is_fixed_ip     ? 1 : 0,
      'expired'         => core::datetime_get('+'.$period.' second'),
      'data'            => (object)['user_agent' => request::user_agent_get(2048)]
    ]))->insert();
    event::start('on_session_insert_after', null, [
      'id_user'     => $id_user,
      'id_session'  => static::id_get(),
      'params'      => $session_params
    ]);
  }

  static function delete($id_user, $id_session = null) {
    event::start('on_session_delete_before', null, [
      'id_user'    => $id_user,
      'id_session' => $id_session]);
    $result = (new instance('session', [
      'id'      => $id_session ?: static::id_get(),
      'id_user' => $id_user
    ]))->delete();
  # regenerate Session ID for CURRENT session
    if ($id_session === null)
      static::id_regenerate('a');
    return $result;
  }

  static function cleaning() {
    entity::get('session')->instances_delete(['conditions' => [
      'expired_!f' => 'expired',
      'operator'   => '<',
      'expired_!v' => core::datetime_get()
    ]]);
  }

  ####################################
  ### functionality for session_id ###
  ####################################

  # ┌──────┬──────────┬──────────────────────────────────┬──────────┬──────────┬──────────┬──────────────────────────────────────────────────────────┐
  # │ type │ expired  │ IP-v6 (100000f7… == 127.0.0.1)   │ u-agent  │ random   │ hash     │ description                                              │
  # ╞══════╪══════════╪══════════════════════════════════╪══════════╪══════════╪══════════╪══════════════════════════════════════════════════════════╡
  # │    a---00000000---100000f7000000000000000000000000---abcdefab---abcdefab---abcdefab │ anonymous                                                │
  # │    f---abcdef01---00000000000000000000000000000000---abcdefab---abcdefab---abcdefab │ registered without parameters                            │
  # │    f---abcdef30---00000000000000000000000000000000---abcdefab---abcdefab---abcdefab │ registered with "long session"                           │
  # │    f---abcdef01---100000f7000000000000000000000000---abcdefab---abcdefab---abcdefab │ registered with "bind to my IP address"                  │
  # │    f---abcdef30---100000f7000000000000000000000000---abcdefab---abcdefab---abcdefab │ registered with "long session" + "bind to my IP address" │
  # └──────┴──────────┴──────────────────────────────────┴──────────┴──────────┴──────────┴──────────────────────────────────────────────────────────┘

  static function id_regenerate($hex_type, $session_params = []) {
    $settings = module::settings_get('user');
    $is_long_session = isset($session_params['is_long_session']);
    $is_fixed_ip     = isset($session_params['is_fixed_ip']);
    if ($hex_type === 'f' && $is_long_session !== true) $expired = time() + ($settings->session_duration_min * core::date_period_d);
    if ($hex_type === 'f' && $is_long_session === true) $expired = time() + ($settings->session_duration_max * core::date_period_d);
    if ($hex_type === 'a'                             ) $expired = 0;
    if ($hex_type === 'f' && $is_fixed_ip !== true) $ip = core::empty_ip;
    if ($hex_type === 'f' && $is_fixed_ip === true) $ip = request::addr_remote_get();
    if ($hex_type === 'a'                         ) $ip = request::addr_remote_get();
  # $hex_type: a - anonymous user | f - authenticated user
    $hex_expired       = static::id_get_hex_expired($expired);
    $hex_ip            = static::id_get_hex_ip($ip);
    $hex_uagent_hash_8 = static::id_get_hex_uagent_hash_8();
    $hex_random        = static::id_get_hex_random();
    $session_id = $hex_type.          # strlen === 1
                  $hex_expired.       # strlen === 8
                  $hex_ip.            # strlen === 32
                  $hex_uagent_hash_8. # strlen === 8
                  $hex_random;        # strlen === 8
    $session_id.= user::signature_get($session_id, 'user', 8);
    header_remove('Set-Cookie');
    setcookie('session_id', $session_id,    $expired, '/', $settings->cookie_domain, 0, 1);
    setcookie('cookies_is_enabled', 'true', $expired, '/', $settings->cookie_domain);
    $_COOKIE['session_id'] = $session_id;
    return $session_id;
  }

  static function id_get() {
    if (static::id_check($_COOKIE['session_id'] ?? ''))
           return        $_COOKIE['session_id'];
      else return static::id_regenerate('a');
  }

  static function id_get_hex_expired($expired) {return str_pad(dechex($expired), 8, '0', STR_PAD_LEFT);}
  static function id_get_hex_ip          ($ip) {return core::ip_to_hex($ip);}
  static function id_get_hex_uagent_hash_8  () {return core::hash_get_mini(request::user_agent_get(80));} # note: why 80? Safari truncates the "user_agent" value for pages in favorites.
  static function id_get_hex_random         () {return str_pad(dechex(random_int(0, PHP_INT_32_MAX)), 8, '0', STR_PAD_LEFT);}
  static function id_get_hex_signature   ($id) {return user::signature_get(substr($id, 0, 56 + 1), 'user', 8);}

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
            if ( ($hex_type === 'a' && $hex_ip === core::ip_to_hex(request::addr_remote_get())) ||
                 ($hex_type === 'f' && $hex_ip === core::ip_to_hex(request::addr_remote_get())) ||
                 ($hex_type === 'f' && $hex_ip === core::ip_to_hex(core::empty_ip)) ) {
              return true;
            }
          }
        }
      }
    }
  }

}}