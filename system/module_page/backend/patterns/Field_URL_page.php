<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Field_URL_page extends Field_URL {

    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'url',
        'required'  => true,
        'maxlength' => 2048
    ];

    public $should_be_included = ['path' => 'path'];
    public $should_be_excluded = [
        'protocol' => 'protocol',
        'domain'   => 'domain',
        'query'    => 'query',
        'anchor'   => 'anchor'];

    function render_description() {
        $this->description = static::description_prepare($this->description);
        $this->description['url-page-root'   ] = new Markup('p', ['data-id' => 'url-page-root'   ], new Text('Field value should be start with "%%_value".', ['value' => '/'        ]));
        $this->description['url-page-manage' ] = new Markup('p', ['data-id' => 'url-page-manage' ], new Text('Field value cannot be start with "%%_value".', ['value' => '/manage/' ]));
        $this->description['url-page-user'   ] = new Markup('p', ['data-id' => 'url-page-user'   ], new Text('Field value cannot be start with "%%_value".', ['value' => '/user/'   ]));
        $this->description['url-page-dynamic'] = new Markup('p', ['data-id' => 'url-page-dynamic'], new Text('Field value cannot be start with "%%_value".', ['value' => '/dynamic/']));
        $this->description['url-page-modules'] = new Markup('p', ['data-id' => 'url-page-modules'], new Text('Field value cannot be start with "%%_value".', ['value' => '/modules/']));
        $this->description['url-page-readme' ] = new Markup('p', ['data-id' => 'url-page-readme' ], new Text('Field value cannot be start with "%%_value".', ['value' => '/readme/' ]));
        $this->description['url-page-shell'  ] = new Markup('p', ['data-id' => 'url-page-shell'  ], new Text('Field value cannot be start with "%%_value".', ['value' => '/shell/'  ]));
        $this->description['url-page-system' ] = new Markup('p', ['data-id' => 'url-page-system' ], new Text('Field value cannot be start with "%%_value".', ['value' => '/system/' ]));
        return parent::render_description();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function validate_value($field, $form, $element, &$new_value) {
        if (parent::validate_value($field, $form, $element,  $new_value) === true) {
            if (strlen($new_value) && preg_match('%^[^/].*$%',         $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value should be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/'        ])); return;}
            if (strlen($new_value) && preg_match('%^/manage(/.*|)$%',  $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/manage/' ])); return;}
            if (strlen($new_value) && preg_match('%^/user(/.*|)$%',    $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/user/'   ])); return;}
            if (strlen($new_value) && preg_match('%^/dynamic(/.*|)$%', $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/dynamic/'])); return;}
            if (strlen($new_value) && preg_match('%^/modules(/.*|)$%', $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/modules/'])); return;}
            if (strlen($new_value) && preg_match('%^/readme(/.*|)$%',  $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/readme/' ])); return;}
            if (strlen($new_value) && preg_match('%^/shell(/.*|)$%',   $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/shell/'  ])); return;}
            if (strlen($new_value) && preg_match('%^/system(/.*|)$%',  $new_value)) {$field->error_set(new Text_multiline(['Field "%%_title" contains an error!', 'Field value cannot be start with "%%_value".'], ['title' => (new Text($field->title))->render(), 'value' => '/system/' ])); return;}
            return true;
        }
    }

}
