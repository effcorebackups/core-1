<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\data;
          use \effcore\file;
          use \effcore\message;
          use \effcore\text_multiline;
          abstract class events_form_seo_robots {

  static function on_init($event, $form, $items) {
    $file = new file(data::directory.'robots.txt');
    if ($file->is_exist()) {
      $items['#content']->value_set(
        $file->load()
      );
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $file = new file(data::directory.'robots.txt');
        $new_value = $items['#content']->value_get();
        if (strlen($new_value)) {
          $file->data_set($new_value);
          if ($file->save())
               message::insert('The changes was saved.');
          else message::insert(new text_multiline([
            'The changes was not saved!',
            'File "%%_file" was not written to disc!',
            'File permissions (if the file exists) and directory permissions should be checked.'], [
            'file' => $file->path_get_relative()]), 'error'
          );
        } else {
          if (@unlink($file->path_get()))
               message::insert('The changes was saved.');
          else message::insert(new text_multiline([
            'The changes was not saved!',
            'File "%%_file" was not deleted!',
            'Directory permissions should be checked.'], [
            'file' => $file->path_get_relative()]), 'error'
          );
        }
        break;
    }
  }

}}