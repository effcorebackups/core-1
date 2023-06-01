<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\dynamic;
use effcore\file;
use effcore\message;
use effcore\module;
use effcore\storage;
use effcore\text_multiline;

abstract class events_form_seo_meta {

    static function on_init($event, $form, $items) {
        $settings = module::settings_get('page');
        $items['#is_apply_tokens']->checked_set($settings->apply_tokens_for_meta);
        $file = new file(dynamic::DIR_FILES.'meta.html');
        if ($file->is_exists()) {
            $items['#content']->value_set(
                $file->load()
            );
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $file = new file(dynamic::DIR_FILES.'meta.html');
                $new_value = $items['#content']->value_get();
                if (strlen($new_value) !== 0) {
                    $file->data_set($new_value);
                    if ($file->save())
                         message::insert(new text_multiline(['File "%%_file" was written to disc.'                                                                                          ], ['file' => $file->path_get_relative()])         );
                    else message::insert(new text_multiline(['File "%%_file" was not written to disc!', 'File permissions (if the file exists) and directory permissions should be checked.'], ['file' => $file->path_get_relative()]), 'error');
                }
                if (strlen($new_value) === 0 && $file->is_exists()) {
                    if (@unlink($file->path_get()))
                         message::insert(new text_multiline(['File "%%_file" was deleted.'                                                ], ['file' => $file->path_get_relative()])         );
                    else message::insert(new text_multiline(['File "%%_file" was not deleted!', 'Directory permissions should be checked.'], ['file' => $file->path_get_relative()]), 'error');
                }
                $result = storage::get('data')->changes_insert('page', 'update', 'settings/page/apply_tokens_for_meta', $items['#is_apply_tokens']->checked_get());
                if ($result) message::insert('Changes was saved.'             );
                else         message::insert('Changes was not saved!', 'error');
                break;
        }
    }

}
