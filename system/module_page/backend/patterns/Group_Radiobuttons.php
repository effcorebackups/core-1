<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

#[\AllowDynamicProperties]

class Group_Radiobuttons extends Control implements Control_complex {

    public $tag_name = 'x-group';
    public $attributes = [
        'data-type' => 'radiobuttons',
        'role'      => 'radiogroup'];
    public $title_attributes = [
        'data-group-title' => true];
    public $content_tag_name = 'x-group-content';
    public $content_attributes = [
        'data-group-content' => true];
    public $name_prefix = null; # unused inherited property
    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦
    public $field_class = '\\effcore\\Field_Radiobutton';
    public $field_tag_name = 'x-field';
    public $field_attributes = [
        'data-type' => 'radiobutton'];
    public $field_title_tag_name = 'label';
    public $field_title_position = 'bottom';
    public $element_attributes = [];
    public $required_any = false;
    public $items    = [];
    public $required = [];
    public $disabled = [];
    public $checked  = [];

    function __construct($items = null, $required = null, $disabled = null, $checked = null) {
        if ($items   ) $this->items    = $items;
        if ($required) $this->required = $required;
        if ($disabled) $this->disabled = $disabled;
        if ($checked ) $this->checked  = $checked;
        parent::__construct();
    }

    function build() {
        if (!$this->is_builded) {
            $fields = [];
            foreach ($this->items as $c_value => $c_info) {
                if (is_string($c_info)) $c_info = (object)['title' => $c_info];
                if (!isset($c_info->title                      )) $c_info->title = $c_value;
                if (!isset($c_info->description                )) $c_info->description = null;
                if (!isset($c_info->weight                     )) $c_info->weight = +0;
                if (!isset($c_info->attributes                 )) $c_info->attributes = [];
                if (!isset($c_info->element_attributes         )) $c_info->element_attributes = [];
                if (!isset($c_info->element_attributes['value'])) $c_info->element_attributes['value'] = $c_value;
                $c_field                     = new $this->field_class;
                $c_field->tag_name           =     $this->field_tag_name;
                $c_field->title_tag_name     =     $this->field_title_tag_name;
                $c_field->title_position     =     $this->field_title_position;
                $c_field->title              = $c_info->title;
                $c_field->description        = $c_info->description;
                $c_field->weight             = $c_info->weight;
                $c_field->attributes         = $c_info->attributes         + $this->attributes_select(  'field_attributes') + $c_field->attributes_select(        'attributes');
                $c_field->element_attributes = $c_info->element_attributes + $this->attributes_select('element_attributes') + $c_field->attributes_select('element_attributes');
                $c_field->build();
                $c_field->required_set(isset($this->required[$c_value]));
                $c_field-> checked_set(isset($this->checked [$c_value]));
                $c_field->disabled_set(isset($this->disabled[$c_value]));
                if (isset($c_info->group)) {
                    if (!isset($fields[$c_info->group]))
                               $fields[$c_info->group] = new Markup('x-sub-group', ['data-sub-group' => true, 'data-sub-group' => $c_info->group]);
                       $fields[$c_info->group]->child_insert($c_field, $c_value);
                } else $fields[$c_value] = $c_field;
            }
            $this->children_update($fields);
            $this->is_builded = true;
        }
    }

    function items_set($items = [], $with_rebuild = true) {
        $this->items = $items;
        if ($with_rebuild) {
            $this->is_builded = false;
            $this->build();
        }
    }

    function items_get() {
        return $this->items;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function name_get_complex($trim = true) {
        # try to find the name in 'element_attributes'
        $element_attributes_name = $this->attributes_select('element_attributes')['name'] ?? '';
        if ($trim) $element_attributes_name = Field::trim_name($element_attributes_name);
        if ($element_attributes_name) {
            return $element_attributes_name;
        }
        # search in first child (instance of field_class)
        else foreach ($this->children_select_recursive() as $c_child) {
            if (is_object($c_child) && $c_child instanceof $this->field_class) {
                return $c_child->name_get($trim);
            }
        }
    }

    function value_get() {
        foreach ($this->children_select_recursive() as $c_child) {
            if (is_object($c_child)                    &&
                $c_child instanceof $this->field_class &&
                $c_child->checked_get() === true) {
                return $c_child->value_get();
            }
        }
        return '';
    }

    function value_set($value) {
        $this->value_set_initial($value);
        foreach ($this->children_select_recursive() as $c_child) if (is_object($c_child) && $c_child instanceof $this->field_class) $c_child->checked_set(false);
        foreach ($this->children_select_recursive() as $c_child) if (is_object($c_child) && $c_child instanceof $this->field_class) {
            if ((string)$c_child->value_get() === (string)$value) {
                $c_child->checked_set(true);
                return true;
            }
        }
    }

    function disabled_get() {
        return count($this->items) +
               count($this->disabled);
    }

    function render_self() {
        if ($this->title && (bool)$this->title_is_visible !== true) return (new Markup($this->title_tag_name, $this->title_attributes + ['data-mark-required' => $this->required_any ? true : null, 'aria-hidden' => 'true'], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
        if ($this->title && (bool)$this->title_is_visible === true) return (new Markup($this->title_tag_name, $this->title_attributes + ['data-mark-required' => $this->required_any ? true : null                         ], is_string($this->title) ? new Text($this->title, [], $this->title_is_apply_translation, $this->title_is_apply_tokens) : $this->title))->render();
    }

    ###########################
    ### static declarations ###
    ###########################

    static function on_validate($group, $form, $npath) {
        return static::validate_required_any($group, $form, $npath);
    }

    static function validate_required_any($group, $form, $npath) {
        if ($group->required_any && count($group->items) !== count($group->disabled) && $group->value_get() === '') {
            $group->error_set_in();
            $form->error_set(
                'Group "%%_title" should contain at least one selected item!', ['title' => (new Text($group->title))->render() ]
            );
        } else {
            return true;
        }
    }

}
