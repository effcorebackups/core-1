<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
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
                $file->load()
            );
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'save':
                $file = new File(Dynamic::DIR_FILES.'global.cssd');
                $new_value = $items['#content']->value_get();
                if (strlen($new_value) !== 0) {
                    $file->data_set($new_value);
                    if ($file->save())
                         Message::insert(new Text_multiline(['File "%%_file" was written to disc.'                                                                                          ], ['file' => $file->path_get_relative()])         );
                    else Message::insert(new Text_multiline(['File "%%_file" was not written to disc!', 'File permissions (if the file exists) and directory permissions should be checked.'], ['file' => $file->path_get_relative()]), 'error');
                }
                if (strlen($new_value) === 0 && $file->is_exists()) {
                    if (@unlink($file->path_get()))
                         Message::insert(new Text_multiline(['File "%%_file" was deleted.'                                                ], ['file' => $file->path_get_relative()])         );
                    else Message::insert(new Text_multiline(['File "%%_file" was not deleted!', 'Directory permissions should be checked.'], ['file' => $file->path_get_relative()]), 'error');
                }
                break;
        }
    }

}
