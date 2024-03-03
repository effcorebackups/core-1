<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Field_URL extends Field_Text {

    public $title = 'URL';
    public $attributes = [
        'data-type' => 'url'];
    public $element_attributes = [
        'type'      => 'text',
        'name'      => 'url',
        'required'  => true,
        'maxlength' => 2048
    ];

    public $is_trim_trailing_slash    = false; # '/'
    public $is_trim_trailing_question = false; # '?'
    public $is_trim_trailing_sharp    = false; # '#'
    public $is_allowed_unicode        = true;
    public $scope = ''; # ''|'relative'|'absolute'
    public $parts = [
        # protocol: +/-
        # domain  : +/-
        # path    : +/-
        # query   : +/-
        # anchor  : +/-
    ];

    function render_description() {
        $this->description = static::description_prepare($this->description);
        if ($this->is_allowed_unicode === true)                                  $this->description['url-unicode'       ] = new Markup('p', ['data-id' => 'url-unicode'       ], 'Field value can contain Unicode.'                 );
        if ($this->is_allowed_unicode !== true)                                  $this->description['url-not-unicode'   ] = new Markup('p', ['data-id' => 'url-not-unicode'   ], 'Field value cannot contain Unicode.'              );
        if ($this->scope === 'relative')                                         $this->description['url-scope-relative'] = new Markup('p', ['data-id' => 'url-scope-relative'], 'Field value should be a relative URL.'            );
        if ($this->scope === 'absolute')                                         $this->description['url-scope-absolute'] = new Markup('p', ['data-id' => 'url-scope-absolute'], 'Field value should be an absolute URL.'           );
        if (isset($this->parts['protocol']) && $this->parts['protocol'] === '-') $this->description['url-not-protocol'  ] = new Markup('p', ['data-id' => 'url-not-protocol'  ], 'Field value should not contain URL with protocol.');
        if (isset($this->parts['domain'  ]) && $this->parts['domain'  ] === '-') $this->description['url-not-domain'    ] = new Markup('p', ['data-id' => 'url-not-domain'    ], 'Field value should not contain URL with domain.'  );
        if (isset($this->parts['path'    ]) && $this->parts['path'    ] === '-') $this->description['url-not-path'      ] = new Markup('p', ['data-id' => 'url-not-path'      ], 'Field value should not contain URL with path.'    );
        if (isset($this->parts['query'   ]) && $this->parts['query'   ] === '-') $this->description['url-not-query'     ] = new Markup('p', ['data-id' => 'url-not-query'     ], 'Field value should not contain URL with query.'   );
        if (isset($this->parts['anchor'  ]) && $this->parts['anchor'  ] === '-') $this->description['url-not-anchor'    ] = new Markup('p', ['data-id' => 'url-not-anchor'    ], 'Field value should not contain URL with anchor.'  );
        if (isset($this->parts['protocol']) && $this->parts['protocol'] === '+') $this->description['url-protocol'      ] = new Markup('p', ['data-id' => 'url-protocol'      ], 'Field value should contain URL with protocol.'    );
        if (isset($this->parts['domain'  ]) && $this->parts['domain'  ] === '+') $this->description['url-domain'        ] = new Markup('p', ['data-id' => 'url-domain'        ], 'Field value should contain URL with domain.'      );
        if (isset($this->parts['path'    ]) && $this->parts['path'    ] === '+') $this->description['url-path'          ] = new Markup('p', ['data-id' => 'url-path'          ], 'Field value should contain URL with path.'        );
        if (isset($this->parts['query'   ]) && $this->parts['query'   ] === '+') $this->description['url-query'         ] = new Markup('p', ['data-id' => 'url-query'         ], 'Field value should contain URL with query.'       );
        if (isset($this->parts['anchor'  ]) && $this->parts['anchor'  ] === '+') $this->description['url-anchor'        ] = new Markup('p', ['data-id' => 'url-anchor'        ], 'Field value should contain URL with anchor.'      );
        return parent::render_description();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function on_validate($field, $form, $npath) {
        $element = $field->child_select('element');
        $name = $field->name_get();
        $type = $field->type_get();
        if ($name && $type) {
            if ($field->disabled_get()) return true;
            if ($field->readonly_get()) return true;
            $new_value = Request::value_get($name, static::current_number_generate($name), $form->source_get());
            if ($field->is_trim_trailing_slash && $new_value !== '/') $new_value = rtrim($new_value, '/');
            if ($field->is_trim_trailing_question                   ) $new_value = rtrim($new_value, '?');
            if ($field->is_trim_trailing_sharp                      ) $new_value = rtrim($new_value, '#');
            $old_value = $field->value_get_initial();
            $result = static::validate_required  ($field, $form, $element, $new_value) &&
                      static::validate_minlength ($field, $form, $element, $new_value) &&
                      static::validate_maxlength ($field, $form, $element, $new_value) &&
                      static::validate_value     ($field, $form, $element, $new_value) &&
                      static::validate_pattern   ($field, $form, $element, $new_value) && (!empty($field->is_validate_uniqueness) ?
                      static::validate_uniqueness($field,                  $new_value, $old_value) : true);
            $field->value_set($new_value);
            return $result;
        }
    }

    static function validate_value($field, $form, $element, &$new_value) {
        $url     = new URL($new_value, ['extra'      => $field->is_allowed_unicode ? URL::VALID_UNICODE_RANGE : '']);
        $url_raw = new URL($new_value, ['completion' => false]);
        if (strlen($new_value) && $url->has_error === true                                                                          ) {$field->error_set(         'Value of "%%_title" field is not a valid URL!'                  , ['title' => (new Text($field->title))->render() ] ); return;}
        if (strlen($new_value) && $field->scope === 'relative' && !URL::is_relative($new_value)                                     ) {$field->error_set(new Text('Value of "%%_title" field should be a relative URL!'            , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && $field->scope === 'absolute' && !URL::is_absolute($new_value)                                     ) {$field->error_set(new Text('Value of "%%_title" field should be an absolute URL!'           , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['protocol']) && $field->parts['protocol'] === '-' && $url_raw->protocol !== '') {$field->error_set(new Text('Value of "%%_title" field should not contain URL with protocol!', ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['domain'  ]) && $field->parts['domain'  ] === '-' && $url_raw->domain   !== '') {$field->error_set(new Text('Value of "%%_title" field should not contain URL with domain!'  , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['path'    ]) && $field->parts['path'    ] === '-' && $url_raw->path     !== '') {$field->error_set(new Text('Value of "%%_title" field should not contain URL with path!'    , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['anchor'  ]) && $field->parts['anchor'  ] === '-' && $url_raw->anchor   !== '') {$field->error_set(new Text('Value of "%%_title" field should not contain URL with anchor!'  , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['query'   ]) && $field->parts['query'   ] === '-' && $url_raw->query    !== '') {$field->error_set(new Text('Value of "%%_title" field should not contain URL with query!'   , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['domain'  ]) && $field->parts['domain'  ] === '+' && $url_raw->domain   === '') {$field->error_set(new Text('Value of "%%_title" field should contain URL with domain!'      , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['protocol']) && $field->parts['protocol'] === '+' && $url_raw->protocol === '') {$field->error_set(new Text('Value of "%%_title" field should contain URL with protocol!'    , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['path'    ]) && $field->parts['path'    ] === '+' && $url_raw->path     === '') {$field->error_set(new Text('Value of "%%_title" field should contain URL with path!'        , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['query'   ]) && $field->parts['query'   ] === '+' && $url_raw->query    === '') {$field->error_set(new Text('Value of "%%_title" field should contain URL with query!'       , ['title' => (new Text($field->title))->render() ])); return;}
        if (strlen($new_value) && isset($field->parts['anchor'  ]) && $field->parts['anchor'  ] === '+' && $url_raw->anchor   === '') {$field->error_set(new Text('Value of "%%_title" field should contain URL with anchor!'      , ['title' => (new Text($field->title))->render() ])); return;}
        return true;
    }

}
