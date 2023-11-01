<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_URL_page extends Field_URL {

    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'url',
        'required'  => true,
        'maxlength' => 2048
    ];

    public $is_trim_trailing_slash    = true;
    public $is_trim_trailing_question = true;
    public $is_trim_trailing_sharp    = true;
    public $scope = 'relative';
    public $parts = [
        'path'     => '+',
        'query'    => '-',
        'anchor'   => '-',
    ];

    function render_description() {
        $this->description = static::description_prepare($this->description);
        $this->description['url-dot'         ] = new Markup('p', ['data-id' => 'url-dot'         ], new Text('Field value cannot contain "%%_value".',       ['value' => '.'        ]));
        $this->description['url-page-develop'] = new Markup('p', ['data-id' => 'url-page-develop'], new Text('Field value cannot be start with "%%_value".', ['value' => '/develop/']));
        $this->description['url-page-docs'   ] = new Markup('p', ['data-id' => 'url-page-docs'   ], new Text('Field value cannot be start with "%%_value".', ['value' => '/docs/'   ]));
        $this->description['url-page-dynamic'] = new Markup('p', ['data-id' => 'url-page-dynamic'], new Text('Field value cannot be start with "%%_value".', ['value' => '/dynamic/']));
        $this->description['url-page-manage' ] = new Markup('p', ['data-id' => 'url-page-manage' ], new Text('Field value cannot be start with "%%_value".', ['value' => '/manage/' ]));
        $this->description['url-page-modules'] = new Markup('p', ['data-id' => 'url-page-modules'], new Text('Field value cannot be start with "%%_value".', ['value' => '/modules/']));
        $this->description['url-page-shell'  ] = new Markup('p', ['data-id' => 'url-page-shell'  ], new Text('Field value cannot be start with "%%_value".', ['value' => '/shell/'  ]));
        $this->description['url-page-system' ] = new Markup('p', ['data-id' => 'url-page-system' ], new Text('Field value cannot be start with "%%_value".', ['value' => '/system/' ]));
        $this->description['url-page-user'   ] = new Markup('p', ['data-id' => 'url-page-user'   ], new Text('Field value cannot be start with "%%_value".', ['value' => '/user/'   ]));
        $this->description['url-page-install'] = new Markup('p', ['data-id' => 'url-page-install'], new Text('Field value cannot be start with "%%_value".', ['value' => '/install/']));
        return parent::render_description();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function validate_value($field, $form, $element, &$new_value) {
        if (parent::validate_value($field, $form, $element,  $new_value) === true) {
            if (strlen($new_value) && preg_match('%^.*[.].*$%',        $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot contain "%%_value"!',       ['title' => (new Text($field->title))->render(), 'value' => '.'        ])); return;}
            if (strlen($new_value) && preg_match('%^/develop(/.*|)$%', $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/develop/'])); return;}
            if (strlen($new_value) && preg_match('%^/docs(/.*|)$%',    $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/docs/'   ])); return;}
            if (strlen($new_value) && preg_match('%^/dynamic(/.*|)$%', $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/dynamic/'])); return;}
            if (strlen($new_value) && preg_match('%^/manage(/.*|)$%',  $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/manage/' ])); return;}
            if (strlen($new_value) && preg_match('%^/modules(/.*|)$%', $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/modules/'])); return;}
            if (strlen($new_value) && preg_match('%^/shell(/.*|)$%',   $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/shell/'  ])); return;}
            if (strlen($new_value) && preg_match('%^/system(/.*|)$%',  $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/system/' ])); return;}
            if (strlen($new_value) && preg_match('%^/user(/.*|)$%',    $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/user/'   ])); return;}
            if (strlen($new_value) && preg_match('%^/install(/.*|)$%', $new_value)) {$field->error_set(new Text('Value of "%%_title" field cannot be start with "%%_value"!', ['title' => (new Text($field->title))->render(), 'value' => '/install/'])); return;}
            return true;
        }
    }

}
