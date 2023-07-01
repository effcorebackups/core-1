<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_Captcha extends Field_Text {

    public $title = 'CAPTCHA';
    public $attributes = ['data-type' => 'captcha'];
    public $element_attributes = [
        'type'         => 'text',
        'name'         => 'captcha',
        'autocomplete' => 'off',
        'required'     => true];
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $attempts_cur;
    public $attempts_max = 3;
    public $noise = 1;

    function build() {
        if (!$this->is_builded) {
            parent::build();
            $captcha = Captcha::select();
            if ($captcha) {
                $this->attempts_cur = $captcha->attempts;
                $canvas = Captcha::canvas_restore(
                    $captcha->canvas_width,
                    $captcha->canvas_height,
                    Core::bin_to_binstr($captcha->canvas_data)
                );
            } else {
                $this->attempts_cur = $this->attempts_max;
                $result = Captcha::canvas_generate_new($this->noise);
                $canvas = $result->canvas;
                Captcha::insert(
                    $this->attempts_max,
                    $result->characters,
                    $result->canvas->w,
                    $result->canvas->h,
                    Core::binstr_to_bin($result->canvas->color_mask_get())
                );
            }
            $this->child_insert_first($canvas, 'canvas');
            $settings_length = Module::settings_get('captcha')->captcha_length;
            $this->     size_set($settings_length);
            $this->minlength_set($settings_length);
            $this->maxlength_set($settings_length);
            if (!Frontend::select('form_all__captcha'))
                 Frontend::insert('form_all__captcha', null, 'styles', ['path' => 'frontend/captcha.css', 'attributes' => ['rel' => 'stylesheet', 'media' => 'all'], 'weight' => -300], 'form_style', 'captcha');
            $this->is_builded = true;
        }
    }

    function captcha_validate($characters) {
        $captcha = Captcha::select();
        if ($captcha) {
            $result = (string)$captcha->characters ===
                      (string)$characters;
            $captcha->attempts--;
            if ($captcha->attempts > 0) $captcha->update();
            if ($captcha->attempts < 1) $captcha->delete();
            $this->is_builded = false;
            $this->build();
            return $result;
        }
    }

    function render_description() {
        $this->description = static::description_prepare($this->description);
        if (!isset($this->description['default']))
                   $this->description['default'] = new Markup('p', ['data-id' => 'default'], 'Write the characters from the picture.');
        $this->description['attempts'] = new Markup('p', ['data-id' => 'attempts'], new Text('Number of attempts: %%_attempts', ['attempts' => $this->attempts_cur === null ? 'n/a' : $this->attempts_cur]));
        return parent::render_description();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function validate_value($field, $form, $element, &$new_value) {
        if (!$field->captcha_validate($new_value)) {
            $field->error_set(
                'Field "%%_title" contains an incorrect characters from picture!', ['title' => (new Text($field->title))->render() ]
            );
        } else {
            return true;
        }
    }

}
