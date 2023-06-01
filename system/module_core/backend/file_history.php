<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class file_history {

    public $name;
    public $type;
    public $file;
    public $mime;
    public $tmp_path; # file in PHP    'tmp'   directory
    public $pre_path; # file in system 'tmp'   directory (dynamic/tmp/*)
    public $fin_path; # file in system 'files' directory (dynamic/files/*)
    public $error = 0;
    public $size;

    function get_current_path($is_relative = false) {
        if     (!empty($this->tmp_path)) return                                                                   $this->tmp_path;
        elseif (!empty($this->pre_path)) return $is_relative ? (new file($this->pre_path))->path_get_relative() : $this->pre_path;
        elseif (!empty($this->fin_path)) return $is_relative ? (new file($this->fin_path))->path_get_relative() : $this->fin_path;
    }

    function set_current_path($path) {
        if     (!empty($this->tmp_path)) $this->tmp_path = $path;
        elseif (!empty($this->pre_path)) $this->pre_path = $path;
        elseif (!empty($this->fin_path)) $this->fin_path = $path;
    }

    function get_current_state() {
        if     (!empty($this->tmp_path)) return 'tmp';
        elseif (!empty($this->pre_path)) return 'pre';
        elseif (!empty($this->fin_path)) return 'fin';
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function init_from_tmp($name, $type, $size, $path, $error) {
        $file = new file($name);
        $this->name     = $file->name_get();
        $this->type     = $file->type_get();
        $this->file     = $file->file_get();
        $this->mime     = core::validate_mime_type($type) ? $type : '';
        $this->size     = $size;
        $this->tmp_path = $path;
        $this->error    = $error;
    }

    function init_from_fin($path_relative) {
        $file = new file(DIR_ROOT.$path_relative);
        if ($file->is_exists()) {
            $this->name     = $file->name_get();
            $this->type     = $file->type_get();
            $this->file     = $file->file_get();
            $this->mime     = $file->mime_get();
            $this->size     = $file->size_get();
            $this->fin_path = $file->path_get();
            $this->error    = 0;
            return true;
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function move_tmp_to_pre($dst_path) {
        if ($this->get_current_state() === 'tmp') {
            $src_file = new file($this->tmp_path);
            $dst_file = new file($dst_path);
            if ($src_file->move_uploaded($dst_file->dirs_get(), $dst_file->file_get())) {
                $this->pre_path = $dst_file->path_get();
                unset($this->tmp_path);
                return true;
            } else {
                message::insert(new text_multiline(['File was not copied from "%%_from" to "%%_to"!', 'Directory permissions should be checked.'], ['from' => $src_file->path_get(), 'to' => $dst_file->path_get_relative()]), 'error');
                console::log_insert('file', 'copy', 'file was not copied from "%%_from" to "%%_to"', 'error', 0,                                   ['from' => $src_file->path_get(), 'to' => $dst_file->path_get_relative()]);
            }
        }
    }

    function move_pre_to_fin($dst_path, $fixed_name = null, $fixed_type = null, $is_save_original_data = false) {
        if ($this->get_current_state() === 'pre') {
            $src_file = new file($this->pre_path);
            $dst_file = new file(token::apply($dst_path));
            if ($fixed_name           ) $dst_file->name_set(token::apply($fixed_name));
            if ($fixed_type           ) $dst_file->type_set(token::apply($fixed_type));
            if ($dst_file->is_exists()) $dst_file->name_set($dst_file->name_get().'-'.core::random_part_get());
            if ($src_file->move($dst_file->dirs_get(), $dst_file->file_get())) {
                if ($is_save_original_data === false) $this->name = $dst_file->name_get();
                if ($is_save_original_data === false) $this->type = $dst_file->type_get();
                if ($is_save_original_data === false) $this->file = $dst_file->file_get();
                $this->fin_path = $dst_file->path_get();
                unset($this->pre_path);
                return true;
            } else {
                message::insert(new text_multiline(['File was not copied from "%%_from" to "%%_to"!', 'Directory permissions should be checked.'], ['from' => $src_file->path_get_relative(), 'to' => $dst_file->path_get_relative()]), 'error');
                console::log_insert('file', 'copy', 'file was not copied from "%%_from" to "%%_to"', 'error', 0,                                   ['from' => $src_file->path_get_relative(), 'to' => $dst_file->path_get_relative()]);
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function delete_tmp() {
        # PHP deletes such files immediately after processing the POST request
        return true;
    }

    function delete_pre() {
        if ($this->get_current_state() === 'pre') {
            $result = @unlink($this->pre_path);
            if ($result) {
                unset($this->pre_path);
                return true;
            } else {
                message::insert(new text_multiline(['File "%%_file" was not deleted!', 'Directory permissions should be checked.'], ['file' => (new file($this->pre_path))->path_get_relative()]), 'error');
                console::log_insert('file', 'copy', 'file "%%_file" was not deleted', 'error', 0,                                   ['file' => (new file($this->pre_path))->path_get_relative()]);
            }
        }
    }

    function delete_fin() {
        if ($this->get_current_state() === 'fin') {
            $result = @unlink($this->fin_path);
            if ($result) {
                unset($this->fin_path);
                return true;
            } else {
                message::insert(new text_multiline(['File "%%_file" was not deleted!', 'Directory permissions should be checked.'], ['file' => (new file($this->fin_path))->path_get_relative()]), 'error');
                console::log_insert('file', 'copy', 'file "%%_file" was not deleted', 'error', 0,                                   ['file' => (new file($this->fin_path))->path_get_relative()]);
            }
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function sanitize_tmp($characters_allowed = 'a-zA-Z0-9_\\-\\.', $max_length_name = 227, $max_length_type = 10) {
        # note: if the type "unknown" is not present in the "types_allowed" in the field settings, you will get a message: Field "Title" does not support uploading a file of this type!
        $this->name = core::sanitize_file_part($this->name, $characters_allowed, $max_length_name);
        $this->type = core::sanitize_file_part($this->type, $characters_allowed, $max_length_type);
        if (!strlen($this->name)) $this->name = core::random_part_get();
        if (!strlen($this->type)) $this->type = 'unknown';
        $this->file = $this->name.'.'.$this->type;
        # special case for IIS, Apache, NGINX
        if ($this->file === 'web.config' || $this->type === 'htaccess' || $this->type === 'nginx') {
            $this->name = core::random_part_get();
            $this->type = 'unknown';
            $this->file = $this->name.'.'.$this->type;
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function container_picture_make($thumbnails) {
        $file_src = new file($this->get_current_path());
        $file_dst = new file($file_src->dirs_get().
                             $file_src->name_get().'.picture');
        $result = media::container_make($file_src->path_get(), 'container://'.$file_dst->path_get(), [
            'thumbnails' => $thumbnails,
            'original' => [
                'type' => $this->type,
                'mime' => $this->mime,
                'size' => $this->size
        ]]);
        if ($result) {
            @unlink($file_src->path_get());
            $this->type = 'picture';
            $this->file = $this->name.'.picture';
            $this->mime = $file_dst->mime_get();
            $this->size = $file_dst->size_get();
            $this->set_current_path($file_dst->path_get());
            return true;
        }
    }

    function container_video_make($poster_thumbnails, $poster_path = null) {
        $file_src = new file($this->get_current_path());
        $file_dst = new file($file_src->dirs_get().
                             $file_src->name_get().'.video');
        $result = media::container_make($file_src->path_get(), 'container://'.$file_dst->path_get(), [
            'poster_thumbnails' => $poster_thumbnails,
            'original' => [
                'type' => $this->type,
                'mime' => $this->mime,
                'size' => $this->size
        ]]);
        if ($result) {
            @unlink($file_src->path_get());
            $this->type = 'video';
            $this->file = $this->name.'.video';
            $this->mime = $file_dst->mime_get();
            $this->size = $file_dst->size_get();
            $this->set_current_path($file_dst->path_get());
            if ($poster_path)
                media::container_file_insert('container://'.$file_dst->path_get(), $poster_path, 'poster');
            return true;
        }
    }

    function container_audio_make($cover_thumbnails, $cover_path = null) {
        $file_src = new file($this->get_current_path());
        $file_dst = new file($file_src->dirs_get().
                             $file_src->name_get().'.audio');
        $result = media::container_make($file_src->path_get(), 'container://'.$file_dst->path_get(), [
            'cover_thumbnails' => $cover_thumbnails,
            'original' => [
                'type' => $this->type,
                'mime' => $this->mime,
                'size' => $this->size
        ]]);
        if ($result) {
            @unlink($file_src->path_get());
            $this->type = 'audio';
            $this->file = $this->name.'.audio';
            $this->mime = $file_dst->mime_get();
            $this->size = $file_dst->size_get();
            $this->set_current_path($file_dst->path_get());
            if ($cover_path)
                media::container_file_insert('container://'.$file_dst->path_get(), $cover_path, 'cover');
            return true;
        }
    }

}
