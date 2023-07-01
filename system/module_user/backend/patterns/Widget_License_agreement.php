<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Widget_License_agreement extends Control {

    public $tag_name = 'x-widget';
    public $attributes = ['data-type' => 'license_agreement'];
    public $main_title = 'License agreement';
    public $text_agree = 'I accept the terms of the license agreement.';

    function build() {
        if (!$this->is_builded) {
            $this->child_insert(static::widget_manage_get($this), 'manage');
            $this->is_builded = true;
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_get($widget) {
        $result = new Fieldset($widget->title);
        $result->title = $widget->main_title;
        $result->state = 'closed';
        # text of license agreement
        $language = Language::get(Language::code_get_current());
        $license_file = new File($language->license_path ?: DIR_ROOT.'license.md');
        $license_markup = new Markup('x-document', ['data-style' => 'license'], Markdown::markdown_to_markup($license_file->load()));
        # switcher 'agree to license agreement'
        $field_switcher_is_agree = new Field_Switcher($widget->text_agree);
        $field_switcher_is_agree->build();
        $field_switcher_is_agree->name_set('is_agree');
        $field_switcher_is_agree->required_set(true);
        # relate new controls with the widget
        $widget->controls['#is_agree'] = $field_switcher_is_agree;
        $result->child_insert($license_markup,          'license_markup');
        $result->child_insert($field_switcher_is_agree, 'field_switcher_is_agree');
        return $result;
    }

}
