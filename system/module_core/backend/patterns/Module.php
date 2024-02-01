<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Module extends Module_embedded {

    public $group = 'Other';
    public $enabled = 'no';

    function disable() {
        if (Core::boot_delete($this->id, 'enabled')) {
            Message::insert(
                new Text('Module "%%_title" (%%_id) was disabled.', ['title' => (new Text($this->title))->render(), 'id' => $this->id])
            );
        }
    }

    function uninstall() {

        # ─────────────────────────────────────────────────────────────────────
        # reverse the deployment process: delete files
        # ─────────────────────────────────────────────────────────────────────

        $copy = Storage::get('data')->select('copy');
        if (isset($copy[$this->id])) {
            foreach ($copy[$this->id] as $c_info) {
                $c_file = new File($c_info->to);
                if ($c_file->is_exists()) {
                    if (File::delete($c_file->path_get()))
                         Message::insert(new Text('File "%%_file" was deleted.'    , ['file' => $c_file->path_get_relative()]));
                    else Message::insert(new Text('File "%%_file" was not deleted!', ['file' => $c_file->path_get_relative()]), 'warning');
                }
            }
        }

        # ─────────────────────────────────────────────────────────────────────
        # reverse the deployment process: delete instances
        # ─────────────────────────────────────────────────────────────────────

        foreach (Instance::get_all_by_module($this->id) as $c_row_id => $c_instance) {
            $c_instance->entity_get()->storage_get()->foreign_keys_checks_set(false);
            if ($c_instance->delete())
                 Message::insert(new Text('Table row with Row ID = "%%_row_id" was deleted.'    , ['row_id' => $c_row_id])           );
            else Message::insert(new Text('Table row with Row ID = "%%_row_id" was not deleted!', ['row_id' => $c_row_id]), 'warning');
            $c_instance->entity_get()->storage_get()->foreign_keys_checks_set(true);
        }

        # ─────────────────────────────────────────────────────────────────────
        # reverse the deployment process: delete entities
        # ─────────────────────────────────────────────────────────────────────

        foreach (Entity::get_all_by_module($this->id) as $c_entity) {
            if ($c_entity->uninstall())
                 Message::insert(new Text('Table "%%_name" was uninstalled.'    , ['name' => $c_entity->table_name])           );
            else Message::insert(new Text('Table "%%_name" was not uninstalled!', ['name' => $c_entity->table_name]), 'warning');
        }

        # ─────────────────────────────────────────────────────────────────────
        # delete changes
        # ─────────────────────────────────────────────────────────────────────

        Storage::get('data')->changes_unregister_all(
            $this->id
        );

        # ─────────────────────────────────────────────────────────────────────
        # delete from boot
        # ─────────────────────────────────────────────────────────────────────

        if (Core::boot_delete($this->id, 'installed')) {
            Message::insert(
                new Text('Module data "%%_title" (%%_id) was removed.', ['title' => (new Text($this->title))->render(), 'id' => $this->id])
            );
        }
    }

}
