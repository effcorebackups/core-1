<?php

  ######################################################################
  ### Copyright © 20NN—20NN Author/Rightholder. All rights reserved. ###
  ######################################################################

namespace effcore\modules\profile_classic {
          use \effcore\instance;
          use \effcore\page;
          use \effcore\url;
          abstract class events_form_login {

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'login':
        if (!url::back_url_get() && page::get_current()->id === 'login_ru') {
          $user = (new instance('user', [
            'email' => $items['#email']->value_get()
          ]))->select();
          if ($user && hash_equals($user->password_hash, $items['#password']->value_get())) {
            url::back_url_set('back', '/ru/user/'.$user->nickname);
          }
        }
        break;
    }
  }

}}