<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

class Template_markup extends Template {

    function render() {
        if ($this->data instanceof Node_simple) {
            foreach (Core::arrobj_select_values_recursive($this->data) as $c_item) {
                if ($c_item instanceof Injection) {
                    if (array_key_exists($c_item->name_get(), $this->args)) {
                        $c_item->data_set(
                            $this->args[$c_item->name_get()]
                        );
                    }
                }
            }
        }
        return preg_replace_callback('%(?<spaces>[ ]{0,})'.
                            '\\%\\%_'.'(?<name>[a-z0-9_]{1,64})'.
                             '(?:\\('.'(?<args>.{1,1024}?)'.'(?<!\\\\)'.'\\)|)%S', function ($c_match) {
            return isset(            $c_match['name'])  &&
                   isset($this->args[$c_match['name']]) &&
                         $this->args[$c_match['name']] !== '' ? $c_match['spaces'].
                         $this->args[$c_match['name']] : '';
        }, $this->data->render());
    }

    ###########################
    ### static declarations ###
    ###########################

    static function attributes_render($data, $is_xml_attr_style = false) {
        if (PAGE_RETURN_FORMAT === 'html') return static::attributes_render_html($data, $is_xml_attr_style);
        if (PAGE_RETURN_FORMAT === 'json') return static::attributes_render_json($data, $is_xml_attr_style);
    }

    static function attributes_render_html($data, $is_xml_attr_style = false, $name_wrapper = '', $value_wrapper = '"', $relation = '=', $delimiter = ' ') {
        $func_render_item = function($name, $item) use ($is_xml_attr_style, $name_wrapper, $value_wrapper, $relation) {
            switch (gettype($item)) {
                case 'array':
                    if (count($item)) {
                        $c_nested_result = [];
                        foreach ($item as $c_nested_name => $c_nested_value) {
                            switch (gettype($c_nested_value)) {
                                case 'NULL'   : break;
                                case 'boolean': if ($c_nested_value) $c_nested_result[] = $c_nested_name;                                                                                                               break;
                                case 'integer':                      $c_nested_result[] =        Core::format_number($c_nested_value);                                                                                  break;
                                case 'double' :                      $c_nested_result[] =        Core::format_number($c_nested_value, Core::FPART_MAX_LEN);                                                             break;
                                case 'string' :                      $c_nested_result[] = str_replace('"', '&quot;', $c_nested_value);                                                                                  break;
                                case 'object' :                      $c_nested_result[] = str_replace('"', '&quot;', (method_exists($c_nested_value, 'render') ? $c_nested_value->render() : Core::LABEL_NO_RENDERER)); break;
                                default       :                      $c_nested_result[] = Core::LABEL_UNSUPPORTED_TYPE;                                                                                                 break;
                            }
                        }
                        return $name_wrapper.$name.
                               $name_wrapper.$relation.
                               $value_wrapper.implode(' ', array_filter($c_nested_result, 'strlen')).
                               $value_wrapper;
                    }
                    return;
                case 'NULL'   :                                            return;
                case 'boolean': if ($item === false)                       return;
                                if ($item && $is_xml_attr_style === false) return $name_wrapper.$name.$name_wrapper;
                                if ($item && $is_xml_attr_style !== false) return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.$name                                                                                                   .$value_wrapper;
                case 'integer':                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.Core::format_number($item)                                                                              .$value_wrapper;
                case 'double' :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.Core::format_number($item, Core::FPART_MAX_LEN)                                                         .$value_wrapper;
                case 'string' :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.str_replace('"', '&quot;', $item)                                                                       .$value_wrapper;
                case 'object' :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.str_replace('"', '&quot;', (method_exists($item, 'render') ? $item->render() : Core::LABEL_NO_RENDERER)).$value_wrapper;
                default       :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.Core::LABEL_UNSUPPORTED_TYPE                                                                            .$value_wrapper;
            }
        };
        $result = [];
        foreach ($data as $c_name => $c_value) {
            if ($c_value instanceof Injection) {
                if (is_array($c_value->data_get())) {
                    foreach ($c_value->data_get() as $c_injection_name => $c_injection_item) {
                        $c_result = $func_render_item(
                            $c_injection_name,
                            $c_injection_item
                        );
                        if ($c_result !== '' &&
                            $c_result !== null) {
                            $result[] = $c_result;
                        }
                    }
                } else {
                    $c_result = $c_value->data_get();
                    if ($c_result !== '' &&
                        $c_result !== null) {
                        $result[] = $c_result;
                    }
                }
            } else {
                $c_result = $func_render_item($c_name, $c_value);
                if ($c_result !== '' &&
                    $c_result !== null) {
                    $result[] = $c_result;
                }
            }
        }
        if ($delimiter) return implode($delimiter, $result);
        else            return                     $result;
    }

    static function attributes_render_json($data, $is_xml_attr_style = false, $name_wrapper = '"', $value_wrapper = '"', $relation = ':', $delimiter = ',', $group_start = '{', $group_end = '}') {
        $func_render_item = function($name, $item) use ($is_xml_attr_style, $name_wrapper, $value_wrapper, $relation) {
            switch (gettype($item)) {
                case 'array':
                    if (count($item)) {
                        $c_nested_result = [];
                        foreach ($item as $c_nested_name => $c_nested_value) {
                            switch (gettype($c_nested_value)) {
                                case 'NULL'   : break;
                                case 'boolean': if ($c_nested_value) $c_nested_result[] = $c_nested_name;                                                                                                                           break;
                                case 'integer':                      $c_nested_result[] =     Core::format_number($c_nested_value);                                                                                                 break;
                                case 'double' :                      $c_nested_result[] =     Core::format_number($c_nested_value, Core::FPART_MAX_LEN);                                                                            break;
                                case 'string' :                      $c_nested_result[] = str_replace(['"', NL], ['\\"', '\\n'], $c_nested_value);                                                                                  break;
                                case 'object' :                      $c_nested_result[] = str_replace(['"', NL], ['\\"', '\\n'], (method_exists($c_nested_value, 'render') ? $c_nested_value->render() : Core::LABEL_NO_RENDERER)); break;
                                default       :                      $c_nested_result[] = Core::LABEL_UNSUPPORTED_TYPE;                                                                                                             break;
                            }
                        }
                        return $name_wrapper.$name.
                               $name_wrapper.$relation.
                               $value_wrapper.implode(' ', array_filter($c_nested_result, 'strlen')).
                               $value_wrapper;
                    }
                    return;
                case 'NULL'   :                                            return;
                case 'boolean': if ($item === false)                       return;
                                if ($item && $is_xml_attr_style === false) return $name_wrapper.$name.$name_wrapper.$relation.'true';
                                if ($item && $is_xml_attr_style !== false) return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.$name                                                                                                               .$value_wrapper;
                case 'integer':                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.Core::format_number($item)                                                                                          .$value_wrapper;
                case 'double' :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.Core::format_number($item, Core::FPART_MAX_LEN)                                                                     .$value_wrapper;
                case 'string' :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.str_replace(['"', NL], ['\\"', '\\n'], $item)                                                                       .$value_wrapper;
                case 'object' :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.str_replace(['"', NL], ['\\"', '\\n'], (method_exists($item, 'render') ? $item->render() : Core::LABEL_NO_RENDERER)).$value_wrapper;
                default       :                                            return $name_wrapper.$name.$name_wrapper.$relation.$value_wrapper.Core::LABEL_UNSUPPORTED_TYPE                                                                                        .$value_wrapper;
            }
        };
        $result = [];
        foreach ($data as $c_name => $c_value) {
            if ($c_value instanceof Injection) {
                if (is_array($c_value->data_get())) {
                    foreach ($c_value->data_get() as $c_injection_name => $c_injection_item) {
                        $c_result = $func_render_item(
                            $c_injection_name,
                            $c_injection_item
                        );
                        if ($c_result !== '' &&
                            $c_result !== null) {
                            $result[] = $c_result;
                        }
                    }
                } else {
                    $c_result = $c_value->data_get();
                    if ($c_result !== '' &&
                        $c_result !== null) {
                        $result[] = $c_result;
                    }
                }
            } else {
                $c_result = $func_render_item($c_name, $c_value);
                if ($c_result !== '' &&
                    $c_result !== null) {
                    $result[] = $c_result;
                }
            }
        }
        if ($delimiter) return $group_start.implode($delimiter, $result).$group_end;
        else            return $group_start.                    $result .$group_end;
    }

}
