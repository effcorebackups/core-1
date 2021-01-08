<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\page {
          use \effcore\data;
          use \effcore\file;
          use \effcore\message;
          use \effcore\text_multiline;
          abstract class events_form_seo_sitemap {

  static function on_init($event, $form, $items) {
    $file = new file(data::directory.'sitemap.xml');
    if ($file->is_exist()) {
      $items['#content']->value_set(
        $file->load()
      );
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'save':
        $file = new file(data::directory.'sitemap.xml');
        $file->data_set($items['#content']->value_get());
        if ($file->save()) message::insert('The changes was saved.');
        else message::insert(new text_multiline(['The changes was not saved!', 'File "%%_file" cannot be written to disc!', 'File permissions (if the file exists) and directory permissions should be checked.'], ['file' => $file->path_get_relative()]), 'error');
        break;
    }
  }

}}