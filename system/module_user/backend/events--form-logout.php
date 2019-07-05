<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use \effcore\message;
          use \effcore\session;
          use \effcore\text;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form_logout {

  static function on_init($form, $items) {
    $sessions       = session::select_all_by_id_user(user::get_current()->id);
    $session_active = session::select();
    $items['*sessions']->children_delete();
    foreach ($sessions as $c_session) {
      if ($c_session->id == $session_active->id)
        $items['*sessions']->checked[$session_active->id] = $session_active->id;
        $items['*sessions']->field_insert($c_session->id, null, ['value' => $c_session->id]);
    }
  }

  static function on_submit($form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'logout':
        if ($items['*sessions']->values_get()) {
          $messages = [];
          $session_active = session::select();
          foreach ($items['*sessions']->values_get() as $c_id_session) {
            if (session::delete(user::get_current()->id, $session_active->id == $c_id_session ? null /* for regenerate */ : $c_id_session))
                 $messages['ok'     ][] = new text('Session with id = "%%_id" was deleted.',     ['id' => $c_id_session]);
            else $messages['warning'][] = new text('Session with id = "%%_id" was not deleted!', ['id' => $c_id_session]);}
          if (!session::select()) url::go('/'); else {
            static::on_init($form, $items);
            foreach ($messages as $c_type => $c_messages_by_type) {
              foreach ($c_messages_by_type as $c_message) {
                message::insert($c_message, $c_type);
              }
            }
          }
        } else {
          message::insert(
            'Nothing selected!', 'warning'
          );
        }
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/');
        break;
    }
  }

}}