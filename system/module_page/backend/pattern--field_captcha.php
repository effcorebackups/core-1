<?php

  ##################################################################
  ### Copyright © 2017—2018 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class field_captcha extends field_text {

  public $title = 'Captcha';
  public $description = 'Write the characters from the picture.';
  public $attributes = ['data-type' => 'captcha'];
  public $element_attributes_default = [
    'type'         => 'text',
    'name'         => 'captcha',
    'required'     => 'required',
    'autocomplete' => 'off'
  ];
# ─────────────────────────────────────────────────────────────────────
  public $length   = 6;
  public $attempts = 3;
  public $noise    = 1;

  function build() {
    parent::build();
    $element = $this->child_select('element');
    $element->attribute_insert('size',      $this->length);
    $element->attribute_insert('minlength', $this->length);
    $element->attribute_insert('maxlength', $this->length);
    $element->weight = 100;
  # build canvas on form
    $captcha = $this->captcha_load();
    if (!$captcha) {
      $captcha = $this->captcha_generate();
      $captcha->insert();
    }
    $this->child_insert($captcha->canvas, 'canvas');
  }

  function captcha_check($characters) {
    $captcha = (new instance('captcha', [
      'ip_address' => static::id_get()
    ]))->select();
    if ($captcha) {
      if ($captcha->attempts > 0) {
        $captcha->attempts--;
        $captcha->update();
      } else {
        $captcha = $this->captcha_generate();
        $captcha->update();
        $this->child_change('canvas', $captcha->canvas);
      }
      if ($captcha->characters === $characters) {
        return true;
      }
    }
  }

  function captcha_generate() {
    $glyphs = static::glyphs_get();
    $characters = '';
    $canvas = new canvas_svg(5 * $this->length, 15, 5);
    $canvas->fill('#000000', 0, 0, null, null, $this->noise);
    for ($i = 0; $i < $this->length; $i++) {
      $c_glyph = array_rand($glyphs);
      $characters.= $glyphs[$c_glyph];
      $canvas->glyph_set($c_glyph, rand(0, 2) - 1 + ($i * 5), rand(1, 5));
    }
    $captcha = new instance('captcha', [
      'ip_address'  => static::id_get(),
      'characters'  => $characters,
      'attempts'    => $this->attempts,
      'canvas'      => $canvas,
      'canvas_data' => $canvas->clmask_to_hexstr()
    ]);
    return $captcha;
  }

  function captcha_load() {
    $captcha = (new instance('captcha', [
      'ip_address' => static::id_get()
    ]))->select();
    if ($captcha) {
      $captcha->canvas = new canvas_svg(5 * $this->length, 15, 5);
      $captcha->canvas->matrix_set(
        $captcha->canvas->hexstr_to_clmask(
          $captcha->canvas_data
        )
      );
      return $captcha;
    }
  }

  ###########################
  ### static declarations ###
  ###########################

  # note:
  # ═════════════════════════════════════════════════════════════════════════
  # 1. function id_get:
  #    duplicates of captcha by IP - it's prevention from DDOS attacks -
  #    user can overflow the storage if captcha_id will be a complex value
  #    for example: IP + user_agent (in this case user can falsify user_agent
  #    on each submit and this action will create a great variety of unique
  #    captcha_id in the storage and will make it overflowed)
  # ─────────────────────────────────────────────────────────────────────────

  static protected $glyphs;

  static function id_get() {
    return $_SERVER['REMOTE_ADDR'];
  }

  static function init() {
    foreach (storage::get('files')->select('captcha_characters') as $c_module_id => $c_characters) {
      foreach ($c_characters as $c_row_id => $c_character) {
        foreach ($c_character->glyphs as $c_glyph) {
          if (isset(static::$glyphs[$c_glyph])) console::log_about_duplicate_add('glyph', $c_glyph);
          static::$glyphs[$c_glyph] = $c_character->character;
        }
      }
    }
  }

  static function glyphs_get() {
    if   (!static::$glyphs) static::init();
    return static::$glyphs;
  }

  static function captcha_cleaning() {
    $storage = $s = storage::get(entity::get('captcha')->storage_id_get());
    $storage->query('DELETE', 'FROM', $s->tables('captcha'), 'WHERE', $s->condition('created', core::datetime_get('-1 hour'), '<'));
  }

  static function validate_value($field, $form, $element, &$new_value) {
    if (!$field->captcha_check($new_value)) {
      $field->error_add(
        translation::get('Field "%%_title" contains an incorrect characters from image!', ['title' => translation::get($field->title)])
      );
    } else {
      return true;
    }
  }

}}