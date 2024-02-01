<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\page;

use effcore\Dynamic;
use effcore\File;
use effcore\Message;
use effcore\Text_multiline;

abstract class Events_Form_Global_CSS {

    static function on_init($event, $form, $items) {
        $file = new File(Dynamic::DIR_FILES.'global.cssd');
        if ($file->is_exists()) {
            $items['#content']->value_set(
                $file->load() ?: ''
            );
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $file = new File(Dynamic::DIR_FILES.'global.cssd');
                $old_value = $file->load() ?: '';
                $new_value = $items['#content']->value_get();
                $result = true;
                if ($new_value !== $old_value) {
                    if (strlen($new_value) !== 0) {
                        $file->data_set($new_value);
                        $result = $file->save();
                        if ($result)                   Message::insert(new Text_multiline(['File "%%_file" was written to disc.'                                             ], ['file' => $file->path_get_relative()])         );
                        else { if ($file->is_exists()) Message::insert(new Text_multiline(['File "%%_file" was not written to disc!', 'File permissions are too strict!'     ], ['file' => $file->path_get_relative()]), 'error');
                               else                    Message::insert(new Text_multiline(['File "%%_file" was not written to disc!', 'Directory permissions are too strict!'], ['file' => $file->path_get_relative()]), 'error');
                        }
                    }
                    if (strlen($new_value) === 0 && $file->is_exists()) {
                        $result = File::delete($file->path_get());
                        if ($result) Message::insert(new Text_multiline(['File "%%_file" was deleted.'                                             ], ['file' => $file->path_get_relative()])         );
                        else         Message::insert(new Text_multiline(['File "%%_file" was not deleted!', 'Directory permissions are too strict!'], ['file' => $file->path_get_relative()]), 'error');
                    }
                }
                if ($new_value !== $old_value &&  $result) Message::insert('Changes was saved.'               );
                if ($new_value !== $old_value && !$result) Message::insert('Changes was not saved!', 'error'  );
                if ($new_value === $old_value            ) Message::insert('Changes was not saved!', 'warning');
                break;
        }
    }

}
