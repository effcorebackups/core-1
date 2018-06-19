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

  static function on_submit_user_delete($form, $items) {
    $id_user = page::current_get()->args_get('id_user');
    switch ($form->clicked_button_name) {
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

  #######################
  ### form: user_edit ###
  #######################

  static function on_init_user_edit($form, $items) {
    $id_user = page::current_get()->args_get('id_user');
    $user = (new instance('user', ['id' => $id_user]))->select();
    $items['#email']->value_set($user->email);
    $items['#nick']->value_set($user->nick);
    $items['#avatar']->pool_values_init_old(
      $user->avatar_path_relative ? [$user->avatar_path_relative] : []
    );
  }

  static function on_validate_user_edit($form, $items) {
    switch ($form->clicked_button_name) {
      case 'save':
        if ($form->errors_count_get() == 0) {
          $id_user = page::current_get()->args_get('id_user');
        # check security
          $test_pass = (new instance('user', ['id' => $id_user]))->select();
          if ($test_pass->password_hash !== core::hash_password_get($items['#password']->value_get())) {
            $items['#password']->error_add(
              translation::get('Field "%%_title" contains incorrect value!', [
                'title' => translation::get($items['#password']->title)
              ])
            );
            return;
          }
        # test email
          $test_email = (new instance('user', [
            'email' => strtolower($items['#email']->value_get())
          ]))->select();
          if ($test_email &&
              $test_email->id != $id_user) {
            $items['#email']->error_add(
              'User with this EMail was already registered!'
            );
            return;
          }
        # test nick
          $test_nick = (new instance('user', [
            'nick' => strtolower($items['#nick']->value_get())
          ]))->select();
          if ($test_nick &&
              $test_nick->id != $id_user) {
            $items['#nick']->error_add(
              'User with this Nick was already registered!'
            );
            return;
          }
        # test new password
          if ($items['#password_new']->value_get() ==
              $items['#password']    ->value_get()) {
            $items['#password_new']->error_add(
              'New password must be different from the current password!'
            );
            return;
          }
        }
        break;
    }
  }

  static function on_submit_user_edit($form, $items) {
    $id_user = page::current_get()->args_get('id_user');
    switch ($form->clicked_button_name) {
      case 'save':
        $user = (new instance('user', ['id' => $id_user]))->select();
        $user->email = strtolower($items['#email']->value_get());
        $user->nick  = strtolower($items['#nick']->value_get());
        if ($items['#password_new']->value_get()) {
          $user->password_hash = core::hash_password_get($items['#password_new']->value_get());
        }
        $avatar_info = $items['#avatar']->pool_files_save();
        if (isset($avatar_info[0]->path) &&
                  $avatar_info[0]->path) {
           $c_file = new file($avatar_info[0]->path);
           $user->avatar_path_relative = $c_file->path_relative_get(); } else {
           $user->avatar_path_relative = '';
        }
        if ($user->update()) {
          message::insert(
            translation::get('User %%_nick was updated.', ['nick' => $user->nick])
          );
          url::go(url::back_url_get() ?: '/user/'.$id_user);
        } else {
          message::insert(
            translation::get('User %%_nick was not updated.', ['nick' => $user->nick]), 'warning'
          );
        }
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/user/'.$id_user);
        break;
    }
  }

  ###################
  ### form: login ###
  ###################

  static function on_init_login($form, $items) {
    if (!isset($_COOKIE['cookies_is_on'])) {
      message::insert(
        translation::get('Cookies are disabled. You can not log in!').br.
        translation::get('Enable cookies before login.'), 'warning');
    }
  }

  static function on_validate_login($form, $items) {
    switch ($form->clicked_button_name) {
      case 'login':
        if ($form->errors_count_get() == 0) {
          $user = (new instance('user', [
            'email' => strtolower($items['#email']->value_get())
          ]))->select();
          if (!$user || (
               $user->password_hash &&
               $user->password_hash !== core::hash_password_get($items['#password']->value_get()))) {
            $items['#email']->error_add();
            $items['#password']->error_add();
            $form->error_add('Incorrect email or password!');
          }
        }
        break;
    }
  }

  static function on_submit_login($form, $items) {
    switch ($form->clicked_button_name) {
      case 'login':
        $user = (new instance('user', [
          'email' => strtolower($items['#email']->value_get())
        ]))->select();
        if ($user &&
            $user->password_hash === core::hash_password_get($items['#password']->value_get())) {
          session::insert($user->id,
            core::array_kmap($items['##session_params']->values_get())
          );
          url::go('/user/'.$user->id);
        }
        break;
    }
  }

  ##########################
  ### form: registration ###
  ##########################

  static function on_validate_registration($form, $items) {
    switch ($form->clicked_button_name) {
      case 'register':
        if ($form->errors_count_get() == 0) {
        # test email
          if ((new instance('user', ['email' => strtolower($items['#email']->value_get())]))->select()) {
            $items['#email']->error_add(
              'User with this EMail was already registered!'
            );
            return;
          }
        # test nick
          if ((new instance('user', ['nick' => strtolower($items['#nick']->value_get())]))->select()) {
            $items['#nick']->error_add(
              'User with this Nick was already registered!'
            );
            return;
          }
        }
        break;
    }
  }

  static function on_submit_registration($form, $items) {
    switch ($form->clicked_button_name) {
      case 'register':
        $user = (new instance('user', [
          'email'         => strtolower($items['#email']->value_get()),
          'nick'          => strtolower($items['#nick']->value_get()),
          'password_hash' => core::hash_password_get($items['#password']->value_get())
        ]))->insert();
        if ($user) {
          session::insert($user->id,
            core::array_kmap($items['##session_params']->values_get())
          );
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

  static function on_submit_logout($form, $items) {
    switch ($form->clicked_button_name) {
      case 'logout':
        session::delete(user::current_get()->id);
        url::go('/');
        break;
      case 'cancel':
        url::go(url::back_url_get() ?: '/');
        break;
    }
  }

}}