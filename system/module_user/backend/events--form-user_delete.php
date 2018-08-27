<?php

  ##################################################################
  ### Copyright © 2017—2019 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\br;
          use \effcore\entity;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\page;
          use \effcore\translation;
          use \effcore\url;
          abstract class events_form_user_delete extends \effcore\events_form {

  static function on_submit_user_delete($form, $items) {
    $id_user = page::current_get()->args_get('id_user');
    switch ($form->clicked_button->value_get()) {
      case 'delete':
        $user = (new instance('user', [
          'id' => $id_user,
        ]))->select();
        if ($user) {
          $nick = $user->nick;
          if ($user->delete()) {
          # remove user sessions
            $sessions = entity::get('session')->instances_select(['id_user' => $id_user]);
            if ($sessions) {
              foreach ($sessions as $c_session) {
                $c_session->delete();
              }
            }
               message::insert(translation::get('User %%_nick was deleted.',     ['nick' => $nick]));}
          else message::insert(translation::get('User %%_nick was not deleted!', ['nick' => $nick]), 'error');
        }
        url::go(url::back_url_get() ?: '/manage/users');
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/manage/users');
        break;
    }
  }

}}