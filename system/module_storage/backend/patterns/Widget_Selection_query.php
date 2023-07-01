<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Widget_Selection_query extends Control implements Control_complex {

    public $tag_name = 'x-widget';
    public $title_tag_name = 'label';
    public $title_attributes = ['data-widget-title' => true];
    public $content_tag_name = 'x-widget-content';
    public $content_attributes = ['data-widget-content' => true, 'data-nested-content' => true];
    public $name_complex = 'query_settings';
    public $attributes = [
        'data-type' => 'query-settings',
        'role'      => 'group'];
    public $_instance;

    function build() {
        if (!$this->is_builded) {
            $this->child_insert(static::widget_manage_conditions_get($this), 'widget_manage_conditions');
            $this->child_insert(static::widget_manage_order_get     ($this), 'widget_manage_order');
            $this->child_insert(static::widget_manage_limit_get     ($this), 'widget_manage_limit');
            $this->is_builded = true;
        }
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    function value_get($options = []) { # @return: array | serialize(array)
        $result = [];
        # prepare conditions
        if ($this->_instance->data['is_custom_conditions'] ?? false) {
            $conditions = $this->controls['#conditions_data']->value_data_get()->conditions ?? null;
               $result['conditions'] = is_array($conditions) ? $conditions : [];
        } else $result['conditions'] = $this->controls['*widget_query_conditions']->value_get_prepared();
        # prepare order
        $result['order'] = $this->controls['*widget_query_order']->value_get_prepared();
        # prepare limit
        $result['limit'] = $this->controls['#limit']->value_get();
        # prepare final result
        if (!empty($options['return_serialized']))
             return serialize($result);
        else return           $result;
    }

    function value_set($value, $options = []) {
        $this->value_set_initial($value);
        if (Core::data_is_serialized($value)) $value = unserialize($value);
        if ($value === null) $value = [];
        if ($value ===  '' ) $value = [];
        if (is_array($value)) {
            # prepare conditions
            if (!empty($value['conditions'])) {
                if ($this->_instance->data['is_custom_conditions'] ?? false)
                     $this->controls['#conditions_data']->value_data_set($value['conditions'], 'conditions');
                else $this->controls['*widget_query_conditions']->value_set_prepared($value['conditions'], $options);
            }
            # prepare order
            if (!empty($value['order'])) {
                $this->controls['*widget_query_order']->value_set_prepared($value['order'], $options);
            }
            # prepare limit
            if (!empty($value['limit'])) {
                $this->controls['#limit']->value_set($value['limit']);
            }
        }
    }

    function name_get_complex() {
        return $this->name_complex;
    }

    function disabled_get() {
        return false;
    }

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_conditions_get($widget) {
        $is_custom_conditions = $widget->_instance->data['is_custom_conditions'] ?? false;
        $result = $is_custom_conditions ? new Fieldset('Conditions') : new Node;
        # controls for conditions
        if ($is_custom_conditions) {
            $field_textarea_data_conditions = new Field_Textarea_data;
            $field_textarea_data_conditions->cform = $widget->cform;
            $field_textarea_data_conditions->attributes['data-role'] = 'data-conditions';
            $field_textarea_data_conditions->title = null;
            $field_textarea_data_conditions->element_attributes['rows'] = 10;
            $field_textarea_data_conditions->build();
            $field_textarea_data_conditions->name_set($widget->name_get_complex().'__conditions_data');
            $field_textarea_data_conditions->required_set(false);
            $field_textarea_data_conditions->minlength_set(null);
            $field_textarea_data_conditions->maxlength_set(10000);
            $field_textarea_data_conditions->value_set('conditions');
            $field_textarea_data_conditions->disabled_set();
            # relate new controls with the widget
            $widget->controls['#conditions_data'] = $field_textarea_data_conditions;
            $result->child_insert($field_textarea_data_conditions, 'field_textarea_data_conditions');
        } else {
            # widget for conditions
            $widget_query_conditions = new Widget_Selection_query_conditions;
            $widget_query_conditions->cform = $widget->cform;
            $widget_query_conditions->_instance = $widget->_instance;
            $widget_query_conditions->build();
            # relate new controls with the widget
            $widget->controls['*widget_query_conditions'] = $widget_query_conditions;
            $result->child_insert($widget_query_conditions, 'widget_manage_conditions');
        }
        return $result;
    }

    static function widget_manage_order_get($widget) {
        $result = new Node;
        # widget for order
        $widget_query_order = new Widget_Selection_query_order;
        $widget_query_order->cform = $widget->cform;
        $widget_query_order->_instance = $widget->_instance;
        $widget_query_order->build();
        # relate new controls with the widget
        $widget->controls['*widget_query_order'] = $widget_query_order;
        $result->child_insert($widget_query_order, 'widget_manage_order');
        return $result;
    }

    static function widget_manage_limit_get($widget) {
        $result = new Node;
        # control for limit
        $field_number_limit = new Field_Number;
        $field_number_limit->cform = $widget->cform;
        $field_number_limit->attributes['data-role'] = 'limit';
        $field_number_limit->title = 'Limit';
        $field_number_limit->build();
        $field_number_limit->name_set($widget->name_get_complex().'__limit');
        $field_number_limit->min_set(1);
        $field_number_limit->max_set(10000);
        $field_number_limit->value_set(50);
        # relate new controls with the widget
        $widget->controls['#limit'] = $field_number_limit;
        $result->child_insert($field_number_limit, 'field_number_limit');
        return $result;
    }

}
