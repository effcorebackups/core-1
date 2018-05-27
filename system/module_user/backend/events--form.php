<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\user {
          use const \effcore\br;
          use \effcore\core;
          use \effcore\entity;
          use \effcore\file;
          use \effcore\instance;
          use \effcore\message;
          use \effcore\page;
          use \effcore\session;
          use \effcore\translation;
          use \effcore\url;
          use \effcore\user;
          abstract class events_form extends \effcore\events_form {

  #########################
  ### form: user_delete ###
  #########################

  static function on_submit_user_delete($form, $fields, &$values) {
    $id_user = page::get_current()->args_get('id_user');
    switch ($form->clicked_button_name) {
      case 'delete':
        $user = (new instance('user', [
          'id' => $id_user,
        ]))->select();
        if ($user) {
          $nick = $user->nick;
          if ($user->delete()) {
            $sessions = entity::get('session')->select_instances(['id_user' => $id_user]);
            if ($sessions) {
              foreach ($sessions as $c_session) {
                $c_session->delete();
              }
            }
               message::insert(translation::get('User %%_nick was deleted.',     ['nick' => $nick]));}
          else message::insert(translation::get('User %%_nick was not deleted!', ['nick' => $nick]), 'error');
        }
        url::go(url::get_back_url() ?: '/manage/users');
        break;
      case 'cancel':
        url::go(url::get_back_url() ?: '/manage/users');
        break;
    }
  }

  #######################
  ### form: user_edit ###
  #######################

  static function on_init_user_edit($form, $fields, &$values) {
    $id_user = page::get_current()->args_get('id_user');
    $user = (new instance('user', ['id' => $id_user]))->select();
    $fields['credentials/email']->child_select('element')->attribute_insert('value', $user->email);
    $fields['credentials/nick']->child_select('element')->attribute_insert('value', $user->nick);
    $fields['credentials/avatar']->pool_values_init_old([$user->avatar_path_relative]);
  }

  static function on_validate_user_edit($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'save':
        if (count($form->errors) == 0) {
          $id_user = page::get_current()->args_get('id_user');
        # check security
          $test_pass = (new instance('user', ['id' => $id_user]))->select();
          if ($test_pass->password_hash !== core::hash_password_get($values['password'][0])) {
            $form->add_error('credentials/password/element',
              translation::get('Field "%%_title" contains incorrect value!', [
                'title' => translation::get($fields['credentials/password']->title)
              ])
            );
            return;
          }
        # test email
          $test_email = (new instance('user', ['email' => strtolower($values['email'][0])]))->select();
          if ($test_email &&
              $test_email->id != $id_user) {
            $form->add_error('credentials/email/element', 'User with this EMail was already registered!');
            return;
          }
        # test nick
          $test_nick = (new instance('user', ['nick' => strtolower($values['nick'][0])]))->select();
          if ($test_nick &&
              $test_nick->id != $id_user) {
            $form->add_error('credentials/nick/element', 'User with this Nick was already registered!');
            return;
          }
        # test new password
          if ($values['password'][0] ==
              $values['password_new'][0]) {
            $form->add_error('credentials/password_new/element',
              'New password must be different from the current password!'
            );
            return;
          }
        }
        break;
    }
  }

  static function on_submit_user_edit($form, $fields, &$values) {
    $id_user = page::get_current()->args_get('id_user');
    switch ($form->clicked_button_name) {
      case 'save':
        $user = (new instance('user', ['id' => $id_user]))->select();
        $user->email = strtolower($values['email'][0]);
        $user->nick  = strtolower($values['nick'][0]);
        if ($values['password_new'][0]) {
          $user->password_hash = core::hash_password_get($values['password_new'][0]);
        }
        $avatar_info = $fields['credentials/avatar']->pool_files_save();
        if (count($avatar_info))
                  $avatar_info = array_shift($avatar_info);
        if (isset($avatar_info->path) &&
                  $avatar_info->path) {
           $c_file = new file($avatar_info->path);
           $user->avatar_path_relative = $c_file->get_path_relative(); } else {
           $user->avatar_path_relative = '';
        }
        if ($user->update()) {
          message::insert(
            translation::get('User %%_nick was updated.', ['nick' => $user->nick])
          );
          url::go(url::get_back_url() ?: '/user/'.$id_user);
        } else {
          message::insert(
            translation::get('User %%_nick was not updated.', ['nick' => $user->nick]), 'warning'
          );
        }
        break;
      case 'cancel':
        url::go(url::get_back_url() ?: '/user/'.$id_user);
        break;
    }
  }

  ###################
  ### form: login ###
  ###################

  static function on_init_login($form, $fields, &$values) {
    if (!isset($_COOKIE['cookies_is_on'])) {
      message::insert(
        translation::get('Cookies are disabled. You can not log in!').br.
        translation::get('Enable cookies before login.'), 'warning');
    }
  }

  static function on_validate_login($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'login':
        if (count($form->errors) == 0) {
          $user = (new instance('user', [
            'email' => strtolower($values['email'][0])
          ]))->select();
          if (!$user || (
               $user->password_hash &&
               $user->password_hash !== core::hash_password_get($values['password'][0]))) {
            $form->add_error('credentials/email/element');
            $form->add_error('credentials/password/element');
            message::insert('Incorrect email or password!', 'error');
          }
        }
        break;
    }
  }

  static function on_submit_login($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'login':
        $user = (new instance('user', [
          'email' => strtolower($values['email'][0])
        ]))->select();
        if ($user &&
            $user->password_hash === core::hash_password_get($values['password'][0])) {
          session::insert($user->id,
            isset($values['session_params']) ? core::array_kmap(
                  $values['session_params']) : []);
          url::go('/user/'.$user->id);
        }
        break;
    }
  }

  ##########################
  ### form: registration ###
  ##########################

  static function on_validate_registration($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'register':
        if (count($form->errors) == 0) {
        # test email
          if ((new instance('user', ['email' => strtolower($values['email'][0])]))->select()) {
            $form->add_error('credentials/email/element', 'User with this EMail was already registered!');
            return;
          }
        # test nick
          if ((new instance('user', ['nick' => strtolower($values['nick'][0])]))->select()) {
            $form->add_error('credentials/nick/element', 'User with this Nick was already registered!');
            return;
          }
        }
        break;
    }
  }

  static function on_submit_registration($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'register':
        $user = (new instance('user', [
          'email'         => strtolower($values['email'][0]),
          'nick'          => strtolower($values['nick'][0]),
          'password_hash' => core::hash_password_get($values['password'][0])
        ]))->insert();
        if ($user) {
          session::insert($user->id,
            isset($values['session_params']) ? core::array_kmap(
                  $values['session_params']) : []);
          url::go('/user/'.$user->id);
        } else {
          message::insert('User was not registered!', 'error');
        }
        break;
    }
  }

  ####################
  ### form: logout ###
  ####################

  static function on_submit_logout($form, $fields, &$values) {
    switch ($form->clicked_button_name) {
      case 'logout':
        session::delete(user::get_current()->id);
        url::go('/');
      case 'cancel':
        url::go(url::get_back_url() ?: '/');
        break;
    }
  }

}}