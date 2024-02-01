<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

abstract class Captcha {

    # ──────────────────────────────────────────────────────────────────────
    # about CAPTCHA ID:
    # ══════════════════════════════════════════════════════════════════════
    # duplicates of captcha by IP - it is prevention from DDOS attacks -
    # user can overflow the storage if captcha_id will be a complex value
    # for example: IP + user_agent (in this case user can falsify user_agent
    # on each submit and this action will create a great variety of unique
    # captcha_id in the storage and will make it overflowed)
    # ──────────────────────────────────────────────────────────────────────

    const GLYPHS_DEFAULT = [
        'ch#' => 'ch#',
        'ch0' => 'ch0',
        'ch1' => 'ch1',
        'ch2' => 'ch2',
        'ch3' => 'ch3',
        'ch4' => 'ch4',
        'ch6' => 'ch6',
        'ch7' => 'ch7',
        'ch8' => 'ch8'
    ];

    static function settings_get() {
        $settings = Module::settings_get('captcha');
        $result = new stdClass;
        $result->length = $settings->length;
        $result->glyphs = [];
        foreach (Glyph::get_all() as $c_row_id => $c_item) {
            if (isset($settings->glyphs[$c_row_id])) {
                $result->glyphs[$c_row_id] = $c_row_id;
            }
        }
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function select_by_id($id) {
        return (new Instance('captcha', [
            'ip_hex' => $id
        ]))->select();
    }

    static function select() {
        return (new Instance('captcha', [
            'ip_hex' => Core::ip_to_hex(Request::addr_remote_get())
        ]))->select();
    }

    static function insert($attempts, $characters, $width, $height, $data) {
        return (new Instance('captcha', [
            'ip_hex'        => Core::ip_to_hex(Request::addr_remote_get()),
            'attempts'      => $attempts,
            'characters'    => $characters,
            'canvas_width'  => $width,
            'canvas_height' => $height,
            'canvas_data'   => $data
        ]))->insert();
    }

    static function delete_all() {
        Entity::get('captcha')->instances_delete();
    }

    static function cleaning() {
        Entity::get('captcha')->instances_delete([
            'where' => [
                'created_!f'       => 'created',
                'created_operator' => '<',
                'created_!v'       => time() - (60 * 5)
        ]]);
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function canvas_restore($width, $height, $binstr) {
        $canvas = new Canvas_SVG($width, $height, 5);
        $canvas->color_mask_set($binstr);
        return $canvas;
    }

    static function canvas_generate_new($noise = 1) {
        $result = new stdClass;
        $result->characters = '';
        $result->canvas = null;
        $settings = Module::settings_get('captcha');
        $glyphs = Glyph::get_all();
        $row_ids_settings = $settings->glyphs;
        $row_ids_all = Core::array_keys_map(array_keys($glyphs));
        $row_ids_available = array_intersect($row_ids_settings, $row_ids_all);
        $row_ids_random = [];
        # the case when uses glyphs from third-party module and this module is disabled
        if (!count($row_ids_available)) {
            $row_ids_available = static::GLYPHS_DEFAULT;
            Message::insert('Module "CAPTCHA" uses glyphs by default!', 'warning');
        }
        # get random items
        for ($i = 0; $i < $settings->length; $i++) {
            $row_ids_random[$i] = array_rand($row_ids_available);
        }
        # calculate canvas dimensions
        $canvas_w = 0;
        $canvas_h = 0;
        for ($i = 0; $i < $settings->length; $i++) {
            $c_sizes = Glyph::get_sizes($glyphs[$row_ids_random[$i]]->glyph);
            $canvas_w +=     $c_sizes->width;
            $canvas_h  = max($c_sizes->height, $canvas_h);
        }
        # generate canvas
        $c_width_offset = 0;
        $canvas = new Canvas_SVG($canvas_w + 2, $canvas_h + 2, 5);
        $canvas->fill('#000000', 0, 0, null, null, $noise);
        for ($i = 0; $i < $settings->length; $i++) {
            $c_sizes = Glyph::get_sizes($glyphs[$row_ids_random[$i]]->glyph);
            $canvas->glyph_set($glyphs[$row_ids_random[$i]]->glyph, $c_width_offset + 1 + random_int(-1, 1), 1 + random_int(-2, 2));
            $result->characters.= $glyphs[$row_ids_random[$i]]->character;
            $c_width_offset += $c_sizes->width;
        }
        # return result
        $result->canvas = $canvas;
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function changes_store($length, $glyphs) {
        $result = true;
        if ($length !== null) $result&= Storage::get('data')->changes_register  ('captcha', 'update', 'settings/captcha/length', $length, false);
        if ($glyphs !== null) $result&= Storage::get('data')->changes_register  ('captcha', 'update', 'settings/captcha/glyphs', $glyphs, false);
        if ($length === null) $result&= Storage::get('data')->changes_unregister('captcha', 'update', 'settings/captcha/length',          false);
        if ($glyphs === null) $result&= Storage::get('data')->changes_unregister('captcha', 'update', 'settings/captcha/glyphs',          false);
        $result&= Storage_Data::cache_update();
        return $result;
    }

}
