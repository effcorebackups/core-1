<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use stdClass;

#[\AllowDynamicProperties]

class Widget_Selection_fields extends Widget_Items {

    public $title = 'Fields';
    public $title__not_selected__widget_insert = '- select -';
    public $item_title = 'Field';
    public $attributes = [
        'data-type' => 'items-entity_fields',
        'data-with-settings' => true];
    public $name_complex = 'widget_selection_fields';
    public $_instance;

    protected $value_join;
    protected $value_texts;
    protected $value_markup;
    protected $value_handlers;
    protected $value_code;

    function value_get($options = []) { # @return: array | serialize(array)
        $result = [];
        foreach ($this->items_get() as $c_row_id => $c_item) {
            if ($c_item->type === 'main') {
                $result['main'][$c_row_id] = new stdClass;
                $result['main'][$c_row_id]->title                = $c_item->title;
                $result['main'][$c_row_id]->entity_field_name    = $c_item->entity_field_name;
                $result['main'][$c_row_id]->format               = $c_item->format ?: null;
                $result['main'][$c_row_id]->is_apply_translation = $c_item->is_apply_translation;
                $result['main'][$c_row_id]->is_apply_tokens      = $c_item->is_apply_tokens;
                $result['main'][$c_row_id]->is_not_visible       = $c_item->is_not_visible;
                $result['main'][$c_row_id]->weight               = $c_item->weight;
            }
            if ($c_item->type === 'handler') {
                $result['handlers'][$c_row_id] = new stdClass;
                $result['handlers'][$c_row_id]->title                = $c_item->title;
                $result['handlers'][$c_row_id]->handler              = $c_item->handler;
                $result['handlers'][$c_row_id]->format               = $c_item->format ?: null;
                $result['handlers'][$c_row_id]->is_apply_translation = $c_item->is_apply_translation;
                $result['handlers'][$c_row_id]->is_apply_tokens      = $c_item->is_apply_tokens;
                $result['handlers'][$c_row_id]->is_not_visible       = $c_item->is_not_visible;
                $result['handlers'][$c_row_id]->weight               = $c_item->weight;
            }
        }
        if ($this->value_join  ) $result['join'  ] = $this->value_join;
        if ($this->value_texts ) $result['texts' ] = $this->value_texts;
        if ($this->value_markup) $result['markup'] = $this->value_markup;
        if ($this->value_code  ) $result['code'  ] = $this->value_code;
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
            $items = [];
            if (!empty($value['main']) && is_array($value['main'])) {
                foreach ($value['main'] as $c_row_id => $c_value) {
                    $items[$c_row_id] = new stdClass;
                    $items[$c_row_id]->type                 = 'main';
                    $items[$c_row_id]->title                = $c_value->title ?? '';
                    $items[$c_row_id]->entity_name          = $this->_instance->main_entity_name;
                    $items[$c_row_id]->entity_field_name    = $c_value->entity_field_name;
                    $items[$c_row_id]->format               = $c_value->format ?? null;
                    $items[$c_row_id]->is_apply_translation = $c_value->is_apply_translation ?? false;
                    $items[$c_row_id]->is_apply_tokens      = $c_value->is_apply_tokens ?? false;
                    $items[$c_row_id]->is_not_visible       = $c_value->is_not_visible ?? false;
                    $items[$c_row_id]->weight               = $c_value->weight ?? 0;
                }
            }
            if (!empty($value['handlers']) && is_array($value['handlers'])) {
                foreach ($value['handlers'] as $c_row_id => $c_value) {
                    $items[$c_row_id] = new stdClass;
                    $items[$c_row_id]->type                 = 'handler';
                    $items[$c_row_id]->title                = $c_value->title ?? '';
                    $items[$c_row_id]->handler              = $c_value->handler ?? null;
                    $items[$c_row_id]->format               = $c_value->format ?? null;
                    $items[$c_row_id]->is_apply_translation = $c_value->is_apply_translation ?? false;
                    $items[$c_row_id]->is_apply_tokens      = $c_value->is_apply_tokens ?? false;
                    $items[$c_row_id]->is_not_visible       = $c_value->is_not_visible ?? false;
                    $items[$c_row_id]->weight               = $c_value->weight ?? 0;
                }
            }
            if (!empty($value['join'  ]) && is_array($value['join'  ])) $this->value_join   = $value['join'  ];
            if (!empty($value['texts' ]) && is_array($value['texts' ])) $this->value_texts  = $value['texts' ];
            if (!empty($value['markup']) && is_array($value['markup'])) $this->value_markup = $value['markup'];
            if (!empty($value['code'  ]) && is_array($value['code'  ])) $this->value_code   = $value['code'  ];
            $this->items_set($items, !empty($options['once']));
        }
    }

    ###########################
    ### static declarations ###
    ###########################

    static function widget_manage_get($widget, $item, $c_row_id) {
        $result = parent::widget_manage_get($widget, $item, $c_row_id);
        # main markup
        if ($item->type === 'main') {
            $entity = Entity::get($item->entity_name);
            $entity_field = $entity ? $entity->field_get($item->entity_field_name) : null;
            $title_markup = isset($entity_field->title) ? [$entity->title, ': ', $entity_field->title] : 'LOST FIELD';
            $info_markup = new Markup('x-info' , [], [
                'title' => new Markup('x-title', [], $title_markup),
                'id'    => new Markup('x-id'   , [], 'row_id: '.$c_row_id) ]);
            # insert widget for settings
            $widget_settings = new Widget_Selection_field_settings($widget, $item, $c_row_id);
            $widget_settings->build();
            # grouping of previous elements in widget 'manage'
            $result->child_select('body')->child_insert($info_markup    , 'info');
            $result->child_select('foot')->child_insert($widget_settings, 'widget_settings');
        }
        # handler markup
        if ($item->type === 'handler') {
            $title_markup = ['H: ', str_replace(['\\', '::'], ' | ', trim($item->handler, '\\'))];
            $info_markup = new Markup('x-info' , [], [
                'title' => new Markup('x-title', [], $title_markup),
                'id'    => new Markup('x-id'   , [], 'row_id: '.$c_row_id) ]);
            # insert widget for settings
            $widget_settings = new Widget_Selection_field_settings($widget, $item, $c_row_id);
            $widget_settings->build();
            # grouping of previous elements in widget 'manage'
            $result->child_select('body')->child_insert($info_markup    , 'info');
            $result->child_select('foot')->child_insert($widget_settings, 'settings');
        }
        return $result;
    }

    static function widget_insert_get($widget) {
        $result = new Markup('x-widget', ['data-type' => 'insert']);
        # control with type of new item
        $field_select_selection_field = new Field_Select_selection_field('New field');
        $field_select_selection_field->cform = $widget->cform;
        $field_select_selection_field->title__not_selected = $widget->title__not_selected__widget_insert;
        $field_select_selection_field->build($widget->_instance->main_entity_name);
        $field_select_selection_field->name_set($widget->name_get_complex().'__insert');
        $field_select_selection_field->required_set(false);
        # button for insertion of the new item
        $button_insert = new Button(null, ['data-style' => 'insert', 'title' => new Text('insert')]);
        $button_insert->break_on_validate = true;
        $button_insert->build();
        $button_insert->value_set($widget->name_get_complex().'__insert');
        $button_insert->_type = 'insert';
        # relate new controls with the widget
        $widget->controls['#insert'] = $field_select_selection_field;
        $widget->controls['~insert'] = $button_insert;
        $result->child_insert($field_select_selection_field, 'field_select_selection_field');
        $result->child_insert($button_insert               , 'button_insert');
        return $result;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function on_button_click_insert($widget, $form, $npath, $button) {
        $widget->controls['#insert']->required_set(true);
        $result_validation = Field_Select::on_validate($widget->controls['#insert'], $form, $npath);
        $widget->controls['#insert']->required_set(false);
        if ($result_validation) {
            $field_info = $widget->controls['#insert']->value_get_parsed();
            if ($field_info !== null) {
                if ($field_info->type === 'main') {
                    if ($field_info->entity_name === $widget->_instance->main_entity_name) {
                        $min_weight = +0;
                        $items = $widget->items_get();
                        foreach ($items as $c_row_id => $c_item)
                            $min_weight = min($min_weight, $c_item->weight);
                        $new_item = new stdClass;
                        $new_item->weight = count($items) ? $min_weight - +5 : +0;
                        $new_item->type              = 'main';
                        $new_item->entity_name       = $field_info->entity_name;
                        $new_item->entity_field_name = $field_info->entity_field_name;
                        $entity = Entity::get($new_item->entity_name);
                        if ($entity && isset($entity->fields[$new_item->entity_field_name])) {
                            $new_item->title = $entity->fields[$new_item->entity_field_name]->title;
                        }
                        $new_row_id = $field_info->entity_field_name;
                        if ($new_row_id === 'attributes') $new_row_id = 'this_attributes';
                        if (array_key_exists($new_row_id, $items)) {
                            $new_row_id.= Core::generate_numerical_suffix(
                                $new_row_id, array_keys($items)
                            );
                        }
                        $items[$new_row_id] = $new_item;
                        $widget->items_set($items);
                        $widget->controls['#insert']->value_set('');
                        Message::insert(new Text_multiline([
                            'Item of type "%%_type" was appended.',
                            'Do not forget to save the changes!'], [
                            'type' => (new Text($widget->item_title))->render() ]));
                        return true;
                    }
                }
                if ($field_info->type === 'handler') {
                    $handler = Selection::get_handler($field_info->handler_row_id);
                    $min_weight = +0;
                    $items = $widget->items_get();
                    foreach ($items as $c_row_id => $c_item)
                        $min_weight = min($min_weight, $c_item->weight);
                    $new_item = new stdClass;
                    $new_item->type                 = 'handler';
                    $new_item->title                = $handler->title ?? '';
                    $new_item->handler              = $handler->handler;
                    $new_item->format               = $handler->format ?? 'raw';
                    $new_item->is_apply_translation = $handler->is_apply_translation ?? false;
                    $new_item->is_apply_tokens      = $handler->is_apply_tokens ?? false;
                    $new_item->is_not_visible       = $handler->is_not_visible ?? false;
                    $new_item->weight = count($items) ? $min_weight - +5 : +0;
                    $new_row_id = $field_info->handler_row_id;
                    if (array_key_exists($new_row_id, $items)) {
                        $new_row_id.= Core::generate_numerical_suffix(
                            $new_row_id, array_keys($items)
                        );
                    }
                    $items[$new_row_id] = $new_item;
                    $widget->items_set($items);
                    $widget->controls['#insert']->value_set('');
                    Message::insert(new Text_multiline([
                        'Item of type "%%_type" was appended.',
                        'Do not forget to save the changes!'], [
                        'type' => (new Text($widget->item_title))->render() ]));
                    return true;
                }
            }
        }
    }

}
