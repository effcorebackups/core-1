<?php

  ##################################################################
  ### Copyright © 2017—2020 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_captcha extends field_text {

  # about CAPTCHA ID:
  # ═════════════════════════════════════════════════════════════════════════
  # duplicates of captcha by IP - it's prevention from DDOS attacks -
  # user can overflow the storage if captcha_id will be a complex value
  # for example: IP + user_agent (in this case user can falsify user_agent
  # on each submit and this action will create a great variety of unique
  # captcha_id in the storage and will make it overflowed)
  # ─────────────────────────────────────────────────────────────────────────

  public $title = 'CAPTCHA';
  public $description = 'Write the characters from the picture.';
  public $attributes = ['data-type' => 'captcha'];
  public $element_attributes = [
    'type'         => 'text',
    'data-type'    => 'captcha',
    'name'         => 'captcha',
    'autocomplete' => 'off',
    'required'     => true];
# ─────────────────────────────────────────────────────────────────────
  public $length   = 6;
  public $attempts = 3;
  public $noise    = 1;

  function build() {
    if (!$this->is_builded) {
      parent::build();
      $element = $this->child_select('element');
      $element->attribute_insert('size',      $this->length);
      $element->attribute_insert('minlength', $this->length);
      $element->attribute_insert('maxlength', $this->length);
    # build canvas on form
      $captcha = $this->captcha_select();
      if (!$captcha) {
        $captcha = $this->captcha_generate();
        $captcha->insert();
      }
      $this->child_insert_first($captcha->canvas, 'canvas');
      $this->is_builded = true;
      if (!frontend::select('captcha_form'))
           frontend::insert('captcha_form', null, 'styles', ['file' => 'frontend/captcha.css', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all']], 'form_style', 'captcha');
    }
  }

  function captcha_select() {
    $captcha = (new instance('captcha', [
      'ip_hex' => core::ip_to_hex(core::server_get_addr_remote())
    ]))->select();
    if ($captcha) {
      $captcha->canvas = new canvas_svg(5 * $this->length, 15, 5);
      $captcha->canvas->matrix_set($captcha->canvas->hexstr_to_clmask($captcha->canvas_data));
      return $captcha;
    }
  }

  function captcha_generate() {
    $glyphs = static::glyphs_get();
    $characters = '';
    $canvas = new canvas_svg(5 * $this->length, 15, 5);
    $canvas->fill('#000000', 0, 0, null, null, $this->noise);
    for ($i = 0; $i < $this->length; $i++) {
      $c_glyph = array_rand($glyphs);
      $c_character = $glyphs[$c_glyph];
      $characters.= $c_character;
      $canvas->glyph_set($c_glyph,
        random_int(0, 2) - 1 + ($i * 5),
        random_int(1, 5)
      );
    }
    $captcha = new instance('captcha', [
      'ip_hex'      => core::ip_to_hex(core::server_get_addr_remote()),
      'characters'  => $characters,
      'attempts'    => $this->attempts,
      'canvas'      => $canvas,
      'canvas_data' => $canvas->clmask_to_hexstr()
    ]);
    return $captcha;
  }

  function captcha_validate($characters) {
    $captcha = (new instance('captcha', [
      'ip_hex' => core::ip_to_hex(core::server_get_addr_remote())
    ]))->select();
    if ($captcha) {
      if ($captcha->attempts > 0) {
        $captcha->attempts--;
        $captcha->update();
      } else {
        $captcha = $this->captcha_generate();
        $captcha->update();
        $this->child_update('canvas', $captcha->canvas);
      }
      if ($captcha->characters === $characters) {
        return true;
      }
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  static protected $glyphs;

  static function cache_cleaning() {
    static::$glyphs = null;
  }

  static function init() {
    if (!static::$glyphs) {
      foreach (storage::get('files')->select('captcha_characters') as $c_module_id => $c_characters) {
        foreach ($c_characters as $c_row_id => $c_character) {
          foreach ($c_character->glyphs as $c_group => $c_glyph) {
            if (isset(static::$glyphs[$c_group][$c_glyph])) console::log_insert_about_duplicate('glyph', $c_glyph, $c_module_id);
            static::$glyphs[$c_group][$c_glyph] = $c_character->character;
          }
        }
      }
    }
  }

  static function glyphs_get($group = 'default') {
    static::init();
    return static::$glyphs[$group] ?? [];
  }

  static function character_get_glyphs($character, $group = 'default') {
    $result = [];
    foreach (static::glyphs_get($group) as $c_glyph => $c_character) {
      if ($c_character == $character) {
        $result[] = $c_glyph;
      }
    }
    return $result;
  }

  static function get_code_by_id($id) {
    $captcha = (new instance('captcha', [
      'ip_hex' => $id
    ]))->select();
    if ($captcha) {
      return $captcha->characters;
    }
  }

  static function captcha_old_cleaning() {
    entity::get('captcha')->instances_delete(['conditions' => [
      'created_!f' => 'created',
      'operator'   => '<',
      'created_!v' => core::datetime_get('-1 hour')
    ]]);
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (!$field->captcha_validate($new_value)) {
      $field->error_set(
        'Field "%%_title" contains an incorrect characters from image!', ['title' => translation::get($field->title)]
      );
    } else {
      return true;
    }
  }

}}