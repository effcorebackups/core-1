<?php

##################################################################
### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use DateTime;
use DateTimeZone;
use ReflectionClass;
use stdClass;

abstract class core {

    const EMPTY_IP = '::';
    const DATE_PERIOD_H = 60 * 60;
    const DATE_PERIOD_D = 60 * 60 * 24;
    const DATE_PERIOD_W = 60 * 60 * 24 * 7;
    const DATE_PERIOD_M = 60 * 60 * 24 * 30;
    const FPART_MAX_LEN = 10;

    ####################
    ### boot modules ###
    ####################

    static function boot_select($type = 'enabled') {
        $boot = data::select('boot');
        return $boot->{'modules_'.$type} ?? [];
    }

    static function boot_insert($module_id, $module_path, $type) {
        $boot = data::select('boot') ?: new stdClass;
        $boot_buffer = [];
        if ($boot && isset($boot->{'modules_'.$type}))
            $boot_buffer = $boot->{'modules_'.$type};
        $boot_buffer[$module_id] = $module_path;
        asort($boot_buffer);
        $boot->{'modules_'.$type} = $boot_buffer;
        return data::update('boot', $boot, '', ['build_date' => static::datetime_get()]);
    }

    static function boot_delete($module_id, $type) {
        $boot = data::select('boot') ?: new stdClass;
        $boot_buffer = [];
        if ($boot && isset($boot->{'modules_'.$type}))
            $boot_buffer = $boot->{'modules_'.$type};
        unset($boot_buffer[$module_id]);
        $boot->{'modules_'.$type} = $boot_buffer;
        return data::update('boot', $boot, '', ['build_date' => static::datetime_get()]);
    }

    ###############################################
    ### functionality for class|trait|interface ###
    ###############################################

    static function structure_autoload($name) {
        $name = strtolower($name);
        if ($name === 'effcore\\cache'             ) {require_once(DIR_SYSTEM.'module_core/backend/cache.php'                         ); console::log_insert('file', 'insertion', 'system/module_core/backend/cache.php',                          'ok'); return;}
        if ($name === 'effcore\\console'           ) {require_once(DIR_SYSTEM.'module_core/backend/console.php'                       ); console::log_insert('file', 'insertion', 'system/module_core/backend/console.php',                        'ok'); return;}
        if ($name === 'effcore\\data'              ) {require_once(DIR_SYSTEM.'module_core/backend/data.php'                          ); console::log_insert('file', 'insertion', 'system/module_core/backend/data.php',                           'ok'); return;}
        if ($name === 'effcore\\dynamic'           ) {require_once(DIR_SYSTEM.'module_core/backend/dynamic.php'                       ); console::log_insert('file', 'insertion', 'system/module_core/backend/dynamic.php',                        'ok'); return;}
        if ($name === 'effcore\\file'              ) {require_once(DIR_SYSTEM.'module_core/backend/file.php'                          ); console::log_insert('file', 'insertion', 'system/module_core/backend/file.php',                           'ok'); return;}
        if ($name === 'effcore\\markup'            ) {require_once(DIR_SYSTEM.'module_page/backend/pattern--markup.php'               ); console::log_insert('file', 'insertion', 'system/module_page/backend/pattern--markup.php',                'ok'); return;}
        if ($name === 'effcore\\message'           ) {require_once(DIR_SYSTEM.'module_core/backend/pattern--message.php'              ); console::log_insert('file', 'insertion', 'system/module_core/backend/pattern--message.php',               'ok'); return;}
        if ($name === 'effcore\\module_as_profile' ) {require_once(DIR_SYSTEM.'module_core/backend/pattern--module_as_profile.php'    ); console::log_insert('file', 'insertion', 'system/module_core/backend/pattern--module_as_profile.php',     'ok'); return;}
        if ($name === 'effcore\\module_embedded'   ) {require_once(DIR_SYSTEM.'module_core/backend/pattern--module_embedded.php'      ); console::log_insert('file', 'insertion', 'system/module_core/backend/pattern--module_embedded.php',       'ok'); return;}
        if ($name === 'effcore\\module'            ) {require_once(DIR_SYSTEM.'module_core/backend/pattern--module.php'               ); console::log_insert('file', 'insertion', 'system/module_core/backend/pattern--module.php',                'ok'); return;}
        if ($name === 'effcore\\node_simple'       ) {require_once(DIR_SYSTEM.'module_page/backend/pattern--node_simple.php'          ); console::log_insert('file', 'insertion', 'system/module_page/backend/pattern--node_simple.php',           'ok'); return;}
        if ($name === 'effcore\\node'              ) {require_once(DIR_SYSTEM.'module_page/backend/pattern--node.php'                 ); console::log_insert('file', 'insertion', 'system/module_page/backend/pattern--node.php',                  'ok'); return;}
        if ($name === 'effcore\\storage_nosql_data') {require_once(DIR_SYSTEM.'module_storage/backend/pattern--storage_nosql_data.php'); console::log_insert('file', 'insertion', 'system/module_storage/backend/pattern--storage_nosql_data.php', 'ok'); return;}
        if ($name === 'effcore\\text_multiline'    ) {require_once(DIR_SYSTEM.'module_page/backend/pattern--text_multiline.php'       ); console::log_insert('file', 'insertion', 'system/module_page/backend/pattern--text_multiline.php',        'ok'); return;}
        if ($name === 'effcore\\text_simple'       ) {require_once(DIR_SYSTEM.'module_page/backend/pattern--text_simple.php'          ); console::log_insert('file', 'insertion', 'system/module_page/backend/pattern--text_simple.php',           'ok'); return;}
        if ($name === 'effcore\\text'              ) {require_once(DIR_SYSTEM.'module_page/backend/pattern--text.php'                 ); console::log_insert('file', 'insertion', 'system/module_page/backend/pattern--text.php',                  'ok'); return;}
        if ($name === 'effcore\\timer'             ) {require_once(DIR_SYSTEM.'module_core/backend/timer.php'                         ); console::log_insert('file', 'insertion', 'system/module_core/backend/timer.php',                          'ok'); return;}
        console::log_insert('autoload', 'search', $name, 'ok');
        if (isset(static::structures_select()[$name])) {
            $c_item_info = static::structures_select()[$name];
            $c_file = new file($c_item_info->file);
            $c_file->require();
        }
    }

    static function structures_cache_cleaning_after_on_install() {
        foreach (static::structures_select() as $c_full_name => $c_structure) {
            if (isset($c_structure->implements['effcore\\should_clear_cache_after_on_install'])) {
                $c_full_name::cache_cleaning();
            }
        }
    }

    static function structures_select($modules_to_include = []) {
        $result = cache::select('structures');
        if ($result) return $result;
        else {
            $result       = [];
            $files        = [];
            $preparse     = storage_nosql_data::data_find_and_parse_modules_and_bundles();
            $modules_path = $preparse->modules_path;
            $enabled      = static::boot_select('enabled') + $modules_to_include; # === module::get_enabled_by_boot() + $modules_to_include
            # if no modules in the boot (when installing)
            if ($enabled === []) {
                foreach ($preparse->parsed as $c_info) {
                    if (!empty($c_info->data->module) &&
                               $c_info->data->module->enabled === 'yes') {
                        $enabled[$c_info->data->module->id] = $c_info->data->module->path;
                    }
                }
            }
            arsort($enabled);
            # collect *.php files
            foreach ($enabled as $c_enabled_path) {
                $c_files = file::select_recursive($c_enabled_path, '%^.*\\.php$%');
                foreach ($c_files as $c_path_relative => $c_file) {
                    $c_module_id = key(static::array_search__any_array_item_in_value($c_path_relative, $modules_path));
                    if (isset($enabled[$c_module_id])) {
                        $files[$c_path_relative] = $c_file;
                    }
                }
            }
            # parse each collected file
            foreach ($files as $c_file) {
                $c_matches = [];
                preg_match_all('%(?:namespace (?<namespace>[a-zA-Z0-9_\\\\]+)\\s*[{;]\\s*(?<use>.*?|)|)\\s*'.
                                             '(?<modifier>abstract|final|)\\s*'.
                                             '(?<type>class|trait|interface)\\s+'.
                                             '(?<name>[a-zA-Z0-9_]+)\\s*'.
                                  '(?:extends (?<extends>[a-zA-Z0-9_\\\\]+)|)\\s*'.
                               '(?:implements (?<implements>[a-zA-Z0-9_,\\s\\\\]+)|)\\s*{%sS', $c_file->load(), $c_matches, PREG_SET_ORDER);
                foreach ($c_matches as $c_match) {
                    if (!empty($c_match['name'])) {
                        $c_item = new stdClass;
                        # define modifier (abstract|final)
                        if (!empty($c_match['modifier'])) {
                            $c_item->modifier = $c_match['modifier'];
                        }
                        # define namespace, name, type = class|trait|interface
                        $c_item->namespace = !empty($c_match['namespace']) ? $c_match['namespace'] : '';
                        $c_item->name = $c_match['name'];
                        $c_item->type = $c_match['type'];
                        # define parent class
                        if (!empty($c_match['extends'])) {
                            if ($c_match['extends'][0] === '\\')
                                $c_item->extends = ltrim($c_match['extends'], '\\');
                            else $c_item->extends = ltrim($c_item->namespace.'\\'.$c_match['extends'], '\\');
                        }
                        # define implements interfaces
                        if (!empty($c_match['implements'])) {
                            foreach (explode(',', $c_match['implements']) as $c_implement) {
                                $c_implement = trim($c_implement);
                                if ($c_implement[0] === '\\')
                                    $c_implement = ltrim($c_implement, '\\');
                                else $c_implement = ltrim($c_item->namespace.'\\'.$c_implement, '\\');
                                $c_item->implements[$c_implement] = $c_implement;
                            }
                        }
                        # define file path
                        $c_item->file = $c_file->path_get_relative();
                        # insert to result pool
                        if (!$c_item->namespace)
                             $result[strtolower(                        $c_item->name)] = $c_item;
                        else $result[strtolower($c_item->namespace.'\\'.$c_item->name)] = $c_item;
                    }
                }
            }
            ksort($result);
            cache::update('structures', $result, '', ['build_date' => static::datetime_get()]);
            return $result;
        }
    }

    static function structure_is_exists($name) {
        $name = trim(strtolower($name), '\\');
        if (isset(static::structures_select()[$name])) {
            return true;
        }
    }

    static function structure_is_local($name) {
        $parts = static::structure_get_parts($name);
        return $parts[0] === 'effcore';
    }

    static function structure_get_parts($name) {
        return explode('\\', trim($name, '\\'));
    }

    static function structure_get_part_name($name) {
        $parts = static::structure_get_parts($name);
        return end($parts);
    }

    static function class_get_new_instance($name, $args = [], $use_constructor = false) {
        $reflection = new ReflectionClass($name);
        return $use_constructor ? $reflection->newInstanceArgs($args) :
                                  $reflection->newInstanceWithoutConstructor();
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function is_handler($string) {
        return strpos($string, '::') !== false;
    }

    static function handler_exists($handler) {
        return method_exists(static::handler_get_class($handler), static::handler_get_method($handler));
    }

    static function handler_get_class($handler) {
        $parts = explode('::', $handler);
        return $parts[0] ?? null;
    }

    static function handler_get_method($handler) {
        $parts = explode('::', $handler);
        return $parts[1] ?? null;
    }

    ##############################
    ### functionality for data ###
    ##############################

    static function gettype($data, $full = true) {
        $type = strtolower(gettype($data));
        if ($type === 'object' && $full === true) {
            $class_name = '\\'.get_class($data);
            if (static::structure_is_local($class_name))
                 $type.= ':'.substr($class_name, strlen('\\effcore\\'));
            else $type.= ':'.       $class_name; }
        return $type;
    }

    static function data_to_attributes($data, $is_xml_style = false, $join_part = ' ', $name_wrapper = '', $value_wrapper = '"') {
        $result = [];
        foreach ((array)$data as $c_name => $c_value) {
            if ($is_xml_style && $c_value === true) $c_value = $c_name;
            switch (gettype($c_value)) {
                case 'array'  :
                    if (count($c_value)) {
                        $c_nested_result = [];
                        foreach ($c_value as $c_nested_key => $c_nested_value) {
                            switch (gettype($c_nested_value)) {
                                case 'integer': $c_nested_result[] =      static::format_number($c_nested_value);                        break;
                                case 'double' : $c_nested_result[] =      static::format_number($c_nested_value, static::FPART_MAX_LEN); break;
                                case 'string' : $c_nested_result[] = str_replace('"', '&quot;', $c_nested_value);                        break;
                                default       : $c_nested_result[] = '__UNSUPPORTED_TYPE__';                                             break; }}
                        $result[] = $name_wrapper.$c_name.$name_wrapper.'='.$value_wrapper.implode(' ', array_filter($c_nested_result, 'strlen')).$value_wrapper; }
                    break;
                case 'NULL'   :                                                                                                                                                                                                           break;
                case 'boolean': if ($c_value) $result[] = $name_wrapper.$c_name.$name_wrapper;                                                                                                                                            break;
                case 'integer':               $result[] = $name_wrapper.$c_name.$name_wrapper.'='.$value_wrapper.                                          static::format_number($c_value)                               .$value_wrapper; break;
                case 'double' :               $result[] = $name_wrapper.$c_name.$name_wrapper.'='.$value_wrapper.                                          static::format_number($c_value, static::FPART_MAX_LEN)        .$value_wrapper; break;
                case 'string' :               $result[] = $name_wrapper.$c_name.$name_wrapper.'='.$value_wrapper.str_replace('"', '&quot;',                                      $c_value                               ).$value_wrapper; break;
                case 'object' :               $result[] = $name_wrapper.$c_name.$name_wrapper.'='.$value_wrapper.str_replace('"', '&quot;', (method_exists($c_value, 'render') ? $c_value->render() : '__NO_RENDERER__')).$value_wrapper; break;
                default       :               $result[] = $name_wrapper.$c_name.$name_wrapper.'='.$value_wrapper.'__UNSUPPORTED_TYPE__'                                                                                  .$value_wrapper; break;
            }
        }
        if ($join_part) return implode($join_part, $result);
        else            return                     $result;
    }

    static function data_to_code($data, $prefix = '', $array_defaults = null) {
        $result = '';
        switch (gettype($data)) {
            case 'array':
                if (count($data)) {
                    foreach ($data as $c_key => $c_value) {
                        if (is_array($array_defaults) && array_key_exists($c_key,
                                     $array_defaults) &&
                                     $array_defaults[$c_key] === $c_value) continue;
                        $result.= static::data_to_code($c_value, $prefix.(is_numeric($c_key) ?
                                                           '['.static::format_number($c_key, static::FPART_MAX_LEN).']' :
                                                           '[\''.        addcslashes($c_key, "'\\").              '\']'));
                    }
                } else {
                    $result.= $prefix.' = [];'.NL;
                }
                break;
            case 'object':
                $class_name = get_class($data);
                $reflection = new ReflectionClass($class_name);
                $defaults           = $reflection->getDefaultProperties();
                $is_postconstructor = $reflection->implementsInterface('\\effcore\\has_postconstructor');
                $is_postinit        = $reflection->implementsInterface('\\effcore\\has_postinit'       );
                if ($is_postconstructor)
                     $result = $prefix.' = core::class_get_new_instance(\''.addcslashes('\\'.$class_name, "'\\").'\');'.NL;
                else $result = $prefix.' = new \\'.$class_name.';'.NL;
                foreach ($data as $c_key => $c_value) {
                    if (array_key_exists($c_key, $defaults) && $defaults[$c_key] === $c_value) continue;
                    if (static::validate_property_name($c_key))
                         $result.= static::data_to_code($c_value, $prefix.'->'.                 $c_key,              $defaults[$c_key] ?? null);
                    else $result.= static::data_to_code($c_value, $prefix.'->'."{'".addcslashes($c_key, "'\\")."'}", $defaults[$c_key] ?? null);
                }
                if ($is_postconstructor) $result.= $prefix.'->__construct();'.NL;
                if ($is_postinit)        $result.= $prefix.  '->_postinit();'.NL;
                break;
            case 'string' : $result.= $prefix.' = '.'\''.addcslashes($data, "'\\").'\''                .';'.NL; break;
            case 'boolean': $result.= $prefix.' = '.($data ? 'true' : 'false')                         .';'.NL; break;
            case 'integer': $result.= $prefix.' = '.static::format_number($data)                       .';'.NL; break;
            case 'double' : $result.= $prefix.' = '.static::format_number($data, static::FPART_MAX_LEN).';'.NL; break;
            case 'NULL'   : $result.= $prefix.' = null'                                                .';'.NL; break;
            default       : $result.= $prefix.' = '.(string)$data                                      .';'.NL;
        }
        return $result;
    }

    static function data_stringify($data) {
        switch (gettype($data)) {
            case 'string' : return '\''.addcslashes($data, "'\\").'\'';
            case 'integer': return static::format_number($data);
            case 'double' : return static::format_number($data, static::FPART_MAX_LEN);
            case 'boolean': return $data ? 'true' : 'false';
            case 'NULL'   : return 'null';
            case 'object' :
            case 'array'  :
                $expressions = [];
                foreach ($data as $c_key => $c_value) {
                    $expressions[] = static::data_stringify($c_key).' => '.
                                     static::data_stringify($c_value);
                }
                return gettype($data) === 'object' ?
                    '(object)['.implode(', ', $expressions).']' :
                            '['.implode(', ', $expressions).']';
            default: return (string)$data;
        }
    }

    static function data_is_serialized($data) {
        if ($data === 'b:0;') return true;
        if (is_string($data) && @unserialize($data, ['allowed_classes' => ['stdClass']]) !== false) return true;
        return false;
    }

    static function data_serialize($data, $is_optimized = true) {
        $result = '';
        switch (gettype($data)) {
            case 'string' : return 's:'.strlen($data).':'.     '"'.$data.'"'                                                   .';';
            case 'boolean': return 'b:'.                          ($data ? '1' : '0')                                          .';';
            case 'integer': return 'i:'.                           $data                                                       .';';
            case 'double' : return 'd:'.($is_optimized === false ? $data : static::format_number($data, static::FPART_MAX_LEN)).';';
            case 'NULL'   : return 'N'                                                                                         .';';
            case 'array'  :
                $result_children = [];
                foreach ($data as $c_key => $c_val) {
                    $result_children[] = static::data_serialize($c_key, $is_optimized);
                    $result_children[] = static::data_serialize($c_val, $is_optimized); }
                $result = 'a:'.count($data).':{'.implode('', $result_children).'}';
                break;
            case 'object':
                $class_name = get_class($data);
                $reflection = new ReflectionClass($class_name);
                $defaults = $reflection->getDefaultProperties();
                $result_children = [];
                foreach ($data as $c_key => $c_val) {
                    if ($is_optimized && array_key_exists($c_key, $defaults) && $defaults[$c_key] === $c_val) continue;
                    $result_children[] = static::data_serialize($c_key, $is_optimized);
                    $result_children[] = static::data_serialize($c_val, $is_optimized); }
                $result = 'O:'.strlen($class_name).
                                 ':"'.$class_name.'":'.(int)(count($result_children) / 2).':{'.
                                                       implode('', $result_children).'}';
                break;
            default:
        }
        return $result;
    }

    static function deep_clone($data, $class_remaping = []) {
        $string = serialize($data);
        foreach ($class_remaping as $c_old_name => $c_new_name) {
            $c_old_name = 'O:'.strlen($c_old_name).':"'.$c_old_name.'"';
            $c_new_name = 'O:'.strlen($c_new_name).':"'.$c_new_name.'"';
            $string = str_replace($c_old_name, $c_new_name, $string);
        }
        return unserialize($string);
    }

    #####################################
    ### functionality for binary data ###
    #####################################

    static function binstr_to_bin($binstr) {
        $result = '';
        foreach (str_split($binstr, 8) as $c_chunk) {
            $c_rbyte = 0;
            $c_chunk = str_pad($c_chunk, 8, '0');
            if ($c_chunk[0] === '1') $c_rbyte |= 0b10000000;
            if ($c_chunk[1] === '1') $c_rbyte |= 0b01000000;
            if ($c_chunk[2] === '1') $c_rbyte |= 0b00100000;
            if ($c_chunk[3] === '1') $c_rbyte |= 0b00010000;
            if ($c_chunk[4] === '1') $c_rbyte |= 0b00001000;
            if ($c_chunk[5] === '1') $c_rbyte |= 0b00000100;
            if ($c_chunk[6] === '1') $c_rbyte |= 0b00000010;
            if ($c_chunk[7] === '1') $c_rbyte |= 0b00000001;
            $result.= chr($c_rbyte); }
        return $result;
    }

    static function bin_to_binstr($bin) {
        $result = '';
        for ($i = 0; $i < strlen($bin); $i++) {
            $c_rbyte = ord($bin[$i]);
            $c_chunk = $c_rbyte & 0b10000000 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b01000000 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b00100000 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b00010000 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b00001000 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b00000100 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b00000010 ? '1' : '0';
            $c_chunk.= $c_rbyte & 0b00000001 ? '1' : '0';
            $result.= $c_chunk; }
        return $result;
    }

    #################################################################
    ### functionality for dpath (data path) and npath (node path) ###
    #################################################################

    static function dpath_get_pointers(&$data, $dpath, $is_unique_keys = false) {
        $result = [];
        $c_pointer = $data;
        foreach (explode('/', $dpath) as $c_part) {
            $c_pointer = &static::arrobj_select_value($c_pointer, $c_part);
            if ($is_unique_keys) $result[       ] = &$c_pointer;
            else                 $result[$c_part] = &$c_pointer;
        }
        return $result;
    }

    static function npath_get_pointers(&$node, $npath, $is_unique_keys = false) {
        $result = [];
        $c_pointer = $node;
        foreach (explode('/', $npath) as $c_part) {
            $c_pointer = &$c_pointer->children[$c_part];
            if ($is_unique_keys) $result[       ] = &$c_pointer;
            else                 $result[$c_part] = &$c_pointer;
        }
        return $result;
    }

    static function path_get_depth($path) {
        return count_chars($path, 1)[ord('/')] ?? 0;
    }

    #############################################
    ### functionality for mix of array|object ###
    #############################################

    static function &arrobj_select_value(&$data, $name) {
        if (is_array ($data)) return $data  [$name];
        if (is_object($data)) return $data->{$name};
    }

    static function arrobj_insert_value(&$data, $name, $value) {
        if (is_array ($data)) $data  [$name] = $value;
        if (is_object($data)) $data->{$name} = $value;
    }

    static function arrobj_delete_child(&$data, $name) {
        if (is_array ($data)) unset($data  [$name]);
        if (is_object($data)) unset($data->{$name});
    }

    static function arrobj_select_values_recursive(&$data, $is_parent_at_last = false, $dpath = '') {
        $result = [];
        foreach ($data as $c_key => &$c_value) {
            $c_dpath = $dpath ? $dpath.'/'.$c_key : $c_key;
            if ($is_parent_at_last === false)              $result[$c_dpath] = &$c_value;
            if (is_array($c_value) || is_object($c_value)) $result += static::arrobj_select_values_recursive($c_value, $is_parent_at_last, $c_dpath);
            if ($is_parent_at_last !== false)              $result[$c_dpath] = &$c_value;
        }
        return $result;
    }

    ################################
    ### functionality for arrays ###
    ################################

    static function array_values_select_recursive(&$array, $dpath = '') {
        $result = [];
        foreach ($array as $c_key => &$c_value) {
            $c_dpath = $dpath ? $dpath.'/'.$c_key : $c_key;
            if (is_array($c_value) === true) $result += static::array_values_select_recursive($c_value, $c_dpath);
            if (is_array($c_value) !== true) $result[$c_dpath] = &$c_value;
        }
        return $result;
    }

    static function array_rotate($data) {
        $result = [];
        foreach ($data as $c_row) {                  # convert │1│2│ to │1│3│
            for ($i = 0; $i < count($c_row); $i++) { #         │3│4│    │2│4│
                $result[$i][] = $c_row[$i];
            }
        }
        return $result;
    }

    # ┌───────────────────────────────────┐  ┌─────────┐  ┌────────┐  ┌───────┐  ┌──────┐  ┌─────┐  ┌────┐  ┌───┐  ┌──┐  ┌─┐
    # │ weight scale by element direction │  │123456789│  │12345678│  │1234567│  │123456│  │12345│  │1234│  │123│  │12│  │1│
    # ╞═══════════════════════════════════╡  │         │  │9       │  │89     │  │789   │  │6789 │  │5678│  │456│  │34│  │2│
    # │                 │ +100            │  │         │  │        │  │       │  │      │  │     │  │9   │  │789│  │56│  │3│
    # │                 │                 │  │         │  │        │  │       │  │      │  │     │  │    │  │   │  │78│  │4│
    # │                 │                 │  │         │─▶│        │─▶│       │─▶│      │─▶│     │─▶│    │─▶│   │─▶│9 │─▶│5│
    # │ ────────────────┼───────────────▶ │  │         │  │        │  │       │  │      │  │     │  │    │  │   │  │  │  │6│
    # │ +100            │ 0          -100 │  │         │  │        │  │       │  │      │  │     │  │    │  │   │  │  │  │7│
    # │                 │                 │  │         │  │        │  │       │  │      │  │     │  │    │  │   │  │  │  │8│
    # │                 ▼ -100            │  │         │  │        │  │       │  │      │  │     │  │    │  │   │  │  │  │9│
    # └───────────────────────────────────┘  └─────────┘  └────────┘  └───────┘  └──────┘  └─────┘  └────┘  └───┘  └──┘  └─┘

    static function array_sort(&$array, $order = 'd', $translated = true) {
        uasort($array, function ($a, $b) use ($order, $translated) {
            if ($order === 'a' && $translated === false) return                    $b  <=>                    $a;
            if ($order === 'd' && $translated === false) return                    $a  <=>                    $b;
            if ($order === 'a' && $translated !== false) return translation::apply($b) <=> translation::apply($a);
            if ($order === 'd' && $translated !== false) return translation::apply($a) <=> translation::apply($b);
        });
        return $array;
    }

    static function array_sort_by_string(&$array, $key = 'title', $order = 'd', $translated = true) {
        uasort($array, function ($a, $b) use ($key, $order, $translated) {
            if ($order === 'a' && $translated === false) return                     (is_object($b) ? $b->{$key} : $b[$key])   <=>                     (is_object($a) ? $a->{$key} : $a[$key])  ;
            if ($order === 'd' && $translated === false) return                     (is_object($a) ? $a->{$key} : $a[$key])   <=>                     (is_object($b) ? $b->{$key} : $b[$key])  ;
            if ($order === 'a' && $translated !== false) return translation::apply( (is_object($b) ? $b->{$key} : $b[$key]) ) <=> translation::apply( (is_object($a) ? $a->{$key} : $a[$key]) );
            if ($order === 'd' && $translated !== false) return translation::apply( (is_object($a) ? $a->{$key} : $a[$key]) ) <=> translation::apply( (is_object($b) ? $b->{$key} : $b[$key]) );
        });
        return $array;
    }

    static function array_sort_by_number(&$array, $key = 'weight', $order = 'a') {
        $increments = []; # note: with preservation of order for equal values
        foreach ($array as &$c_item) {
            $c_value = is_object($c_item) ? $c_item->{$key} : $c_item[$key];
            if ($order === 'a') $increments[$c_value] = array_key_exists($c_value, $increments) ? $increments[$c_value] - .0001 : 0;
            if ($order === 'd') $increments[$c_value] = array_key_exists($c_value, $increments) ? $increments[$c_value] + .0001 : 0;
            if (is_object($c_item)) $c_item->_synthetic_weight   = $c_value + $increments[$c_value];
            else                    $c_item['_synthetic_weight'] = $c_value + $increments[$c_value];
        }
        uasort($array, function ($a, $b) use ($order) {
            if ($order === 'a') return (is_object($b) ? $b->_synthetic_weight : $b['_synthetic_weight']) <=> (is_object($a) ? $a->_synthetic_weight : $a['_synthetic_weight']);
            if ($order === 'd') return (is_object($a) ? $a->_synthetic_weight : $a['_synthetic_weight']) <=> (is_object($b) ? $b->_synthetic_weight : $b['_synthetic_weight']);
        });
        foreach ($array as &$c_item) {
            if (is_object($c_item)) unset($c_item->_synthetic_weight);
            else                    unset($c_item['_synthetic_weight']);
        }
        return $array;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function array_keys_map($array) {
        return array_combine($array, $array);
    }

    static function array_key_first($array) { # alternative for built-in 'array_key_first' in PHP v.7.3+
        $keys = array_keys($array);
        return count($keys) ?
               reset($keys) : null;
    }

    static function array_key_last($array) { # alternative for built-in 'array_key_last' in PHP v.7.3+
        $keys = array_keys($array);
        return count($keys) ?
                 end($keys) : null;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function array_search__any_array_item_is_equal_value($value, $array) {
        foreach ($array as $c_value)
            if ((string)$value === (string)$c_value)
                return true;
    }

    static function array_search__value_in_any_array_item($value, $array) {
        $result = [];
        foreach ($array as $c_key => $c_value)
            if (strpos((string)$c_value, (string)$value) === 0)
                $result[$c_key] = $c_value;
        return $result;
    }

    static function array_search__any_array_item_in_value($value, $array) {
        $result = [];
        foreach ($array as $c_key => $c_value)
            if (strpos((string)$value, (string)$c_value) === 0)
                $result[$c_key] = $c_value;
        return $result;
    }

    ########################
    ### bytes conversion ###
    ########################

    static function is_abbreviated_bytes($number) {
        $character = substr($number, -1);
        return in_array($character, ['B', 'K', 'M', 'G', 'T']);
    }

    static function abbreviated_to_bytes($abbreviated) {
        $powers = array_flip(['B', 'K', 'M', 'G', 'T']);
        $character = strtoupper(substr($abbreviated, -1));
        $value = (int)substr($abbreviated, 0, -1);
        return $value * 1024 ** $powers[$character];
    }

    static function bytes_to_abbreviated($bytes, $is_iec = false) {
        if ($bytes && fmod($bytes, 1024 ** 4) === .0) return ($bytes / 1024 ** 4).($is_iec ? 'TiB' : 'T');
        if ($bytes && fmod($bytes, 1024 ** 3) === .0) return ($bytes / 1024 ** 3).($is_iec ? 'GiB' : 'G');
        if ($bytes && fmod($bytes, 1024 ** 2) === .0) return ($bytes / 1024 ** 2).($is_iec ? 'MiB' : 'M');
        if ($bytes && fmod($bytes, 1024 ** 1) === .0) return ($bytes / 1024 ** 1).($is_iec ? 'KiB' : 'K');
        else return $bytes.'B';
    }

    ############################
    ### functionality for IP ###
    ############################

    static function ip_to_hex($ip, $is_v6 = true, $is_reversed = true) {
        $ip_hex = '';
        $inaddr = inet_pton($ip);
        foreach (str_split($inaddr, 1) as $c_char)
            $ip_hex.= str_pad(dechex(ord($c_char)), 2, '0', STR_PAD_LEFT);
        if ($is_v6)       $ip_hex = str_pad($ip_hex, 32, '0', STR_PAD_LEFT);
        if ($is_reversed) $ip_hex = strrev ($ip_hex);
        return $ip_hex;
    }

    static function hex_to_ip($ip_hex) {
        $inaddr = '';
        foreach (str_split($ip_hex, 2) as $c_part)
            $inaddr.= chr(hexdec($c_part));
        return inet_ntop($inaddr);
    }

    ###################################
    ### functionality for date|time ###
    ###################################

    # examples of using:
    # ┌───────────────────────────────────────────────────────┬─────────────┬─────────────────────┐
    # │ +14:00 — Pacific/Kiritimati                           │ to format   │ result              │
    # ╞═══════════════════════════════════════════════════════╪═════════════╪═════════════════════╡
    # │           locale::format_date ('2030-02-01')          │ d.m.Y       │ 01.02.2030          │
    # │           locale::format_time ('01:02:03')            │ H:i:s       │ 15:02:03            │
    # │       locale::format_datetime ('2030-02-01 01:02:03') │ d.m.Y H:i:s │ 01.02.2030 15:02:03 │
    # │       locale::format_timestmp (0)                     │ d.m.Y H:i:s │ 01.01.1970 14:00:00 │
    # ├───────────────────────────────────────────────────────┼─────────────┼─────────────────────┤
    # │       locale::date_utc_to_loc ('2030-02-01')          │ Y-m-d       │ 2030-02-01          │
    # │       locale::time_utc_to_loc ('01:02:03')            │ H:i:s       │ 15:02:03            │
    # │   locale::datetime_utc_to_loc ('2030-02-01 01:02:03') │ Y-m-d H:i:s │ 2030-02-01 15:02:03 │
    # ├───────────────────────────────────────────────────────┼─────────────┼─────────────────────┤
    # │       locale::date_loc_to_utc ('2030-02-01')          │ Y-m-d       │ 2030-02-01          │
    # │       locale::time_loc_to_utc ('15:02:03')            │ H:i:s       │ 01:02:03            │
    # │   locale::datetime_loc_to_utc ('2030-02-01 15:02:03') │ Y-m-d H:i:s │ 2030-02-01 01:02:03 │
    # └───────────────────────────────────────────────────────┴─────────────┴─────────────────────┘
    #
    # ┌───────────────────────────────────────────────────────┬─────────────┬─────────────────────┐
    # │ -11:00 — Pacific/Pago_Pago                            │ to format   │ result              │
    # ╞═══════════════════════════════════════════════════════╪═════════════╪═════════════════════╡
    # │           locale::format_date ('2030-02-01')          │ d.m.Y       │ 01.02.2030          │
    # │           locale::format_time ('01:02:03')            │ H:i:s       │ 14:02:03            │
    # │       locale::format_datetime ('2030-02-01 01:02:03') │ d.m.Y H:i:s │ 31.01.2030 14:02:03 │
    # │       locale::format_timestmp (0)                     │ d.m.Y H:i:s │ 31.12.1969 13:00:00 │
    # ├───────────────────────────────────────────────────────┼─────────────┼─────────────────────┤
    # │       locale::date_utc_to_loc ('2030-02-01')          │ Y-m-d       │ 2030-02-01          │
    # │       locale::time_utc_to_loc ('01:02:03')            │ H:i:s       │ 14:02:03            │
    # │   locale::datetime_utc_to_loc ('2030-02-01 01:02:03') │ Y-m-d H:i:s │ 2030-01-31 14:02:03 │
    # ├───────────────────────────────────────────────────────┼─────────────┼─────────────────────┤
    # │       locale::date_loc_to_utc ('2030-02-01')          │ Y-m-d       │ 2030-02-01          │
    # │       locale::time_loc_to_utc ('14:02:03')            │ H:i:s       │ 01:02:03            │
    # │   locale::datetime_loc_to_utc ('2030-01-31 14:02:03') │ Y-m-d H:i:s │ 2030-02-01 01:02:03 │
    # └───────────────────────────────────────────────────────┴─────────────┴─────────────────────┘
    #
    # ┌───────────────────────────────────────────────────────┬─────────────┬─────────────────────┐
    # │                                                       │ to format   │ result              │
    # ╞═══════════════════════════════════════════════════════╪═════════════╪═════════════════════╡
    # │   core::T_datetime_to_datetime('2030-02-01T01:02:03') │ Y-m-d H:i:s │ 2030-02-01 01:02:03 │
    # │   core::datetime_to_T_datetime('2030-02-01 01:02:03') │ Y-m-dTH:i:s │ 2030-02-01T01:02:03 │
    # └───────────────────────────────────────────────────────┴─────────────┴─────────────────────┘
    #
    # note: each function 'locale::*_format' uses local date/time format settings
    # that have been set on the page '/manage/locale'

    static function timezone_get_client() {return user::get_current()->timezone ?? 'UTC';}
    static function timezone_get_offset_sec($name = 'UTC') {return (new DateTimeZone($name))->getOffset(new DateTime);}
    static function timezone_get_offset_tme($name = 'UTC') {return (new DateTime('now', new DateTimeZone($name)))->format('P');}

    static function T_datetime_to_datetime($datetime) {$date = DateTime::createFromFormat('Y-m-d\\TH:i:s', $datetime, new DateTimeZone('UTC') ); if ($date) return $date->format('Y-m-d H:i:s'  );}
    static function datetime_to_T_datetime($datetime) {$date = DateTime::createFromFormat('Y-m-d H:i:s',   $datetime, new DateTimeZone('UTC') ); if ($date) return $date->format('Y-m-d\\TH:i:s');}

    static function date_get           ($offset = '', $format = 'Y-m-d'        ) {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}
    static function time_get           ($offset = '', $format =       'H:i:s'  ) {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}
    static function datetime_get       ($offset = '', $format = 'Y-m-d H:i:s'  ) {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}
    static function T_datetime_get     ($offset = '', $format = 'Y-m-d\\TH:i:s') {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}

    static function validate_date      ($value) {$result = DateTime::createFromFormat('Y-m-d',         $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format('Y-m-d'        )) === strlen(field_date    ::INPUT_MAX_DATE    );}
    static function validate_time      ($value) {$result = DateTime::createFromFormat(      'H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format(      'H:i:s'  )) === strlen(field_time    ::INPUT_MAX_TIME    );}
    static function validate_datetime  ($value) {$result = DateTime::createFromFormat('Y-m-d H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format('Y-m-d H:i:s'  )) === strlen(field_datetime::INPUT_MAX_DATETIME);}
    static function validate_T_datetime($value) {$result = DateTime::createFromFormat('Y-m-d\\TH:i:s', $value, new DateTimeZone('UTC')); return $result instanceof DateTime && strlen($result->format('Y-m-d\\TH:i:s')) === strlen(field_datetime::INPUT_MAX_DATETIME);}

    static function sanitize_date      ($value) {$result = DateTime::createFromFormat('Y-m-d',         $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format('Y-m-d'        ) : null;}
    static function sanitize_time      ($value) {$result = DateTime::createFromFormat(      'H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format(      'H:i:s'  ) : null;}
    static function sanitize_datetime  ($value) {$result = DateTime::createFromFormat('Y-m-d H:i:s',   $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format('Y-m-d H:i:s'  ) : null;}
    static function sanitize_T_datetime($value) {$result = DateTime::createFromFormat('Y-m-d\\TH:i:s', $value, new DateTimeZone('UTC')); return $result instanceof DateTime ? $result->format('Y-m-d\\TH:i:s') : null;}

    ##############
    ### format ###
    ##############

    static function format_number($number, $precision = 0, $dec_point = '.', $thousands = '', $no_zeros = true) {
        $result = $precision > 0 ? # disable the rounding effect
             substr(number_format($number, $precision + 1, $dec_point, $thousands), 0, -1) :
                    number_format($number, $precision,     $dec_point, $thousands);
        if ($no_zeros && strpos($result, $dec_point) !== false) {
            $result = rtrim($result, '0');
            $result = rtrim($result, $dec_point);
        }
        return $result;
    }

    static function format_bytes($bytes, $is_iec = true) {
        if ($bytes && fmod($bytes, 1024 ** 4) === .0) return static::format_number($bytes / 1024 ** 4).' '.($is_iec ? 'TiB' : 'T');
        if ($bytes && fmod($bytes, 1024 ** 3) === .0) return static::format_number($bytes / 1024 ** 3).' '.($is_iec ? 'GiB' : 'G');
        if ($bytes && fmod($bytes, 1024 ** 2) === .0) return static::format_number($bytes / 1024 ** 2).' '.($is_iec ? 'MiB' : 'M');
        if ($bytes && fmod($bytes, 1024 ** 1) === .0) return static::format_number($bytes / 1024 ** 1).' '.($is_iec ? 'KiB' : 'K');
        else                                          return static::format_number($bytes            ).' '.(                  'B');
    }

    static function format_persent($number, $precision = 2) {return static::format_number(floatval($number), $precision).'%';}
    static function format_msecond($number, $precision = 6) {return static::format_number(floatval($number), $precision);}
    static function format_version($number)                 {return static::format_number(floatval($number), 3, '.', '', false);}

    static function format_logic($value) {
        return $value ? 'yes' : 'no';
    }

    ###############
    ### filters ###
    ###############

    # number validation matrix: number(n) → is_valid(0|1|2)
    # ┌───────────╥──────────┬───────────┬───────────┬────────────┬───────────┬────────────┬────────────┬─────────────┬────────────┬─────────────┐
    # │           ║          ┊ with '-'  │           ┊ with '-'   │           ┊ with '-'   │            ┊ with '-'    │            ┊ with '-'    │
    # ╞═══════════╬══════════┊═══════════╪═══════════┊════════════╪═══════════┊════════════╪════════════┊═════════════╪════════════┊═════════════╡
    # │           ║ ''   → 0 ┊ '-'   → 0 │ '0'   → 1 ┊ '-0'   → 0 │ '1'   → 1 ┊ '-1'   → 1 │ '01'   → 0 ┊ '-01'   → 0 │ '10'   → 1 ┊ '-10'   → 1 │
    # │ with '.'  ║ '.'  → 0 ┊ '-.'  → 0 │ '0.'  → 0 ┊ '-0.'  → 0 │ '1.'  → 0 ┊ '-1.'  → 0 │ '01.'  → 0 ┊ '-01.'  → 0 │ '10.'  → 0 ┊ '-10.'  → 0 │
    # │ with '.0' ║ '.0' → 0 ┊ '-.0' → 0 │ '0.0' → 1 ┊ '-0.0' → 2 │ '1.0' → 1 ┊ '-1.0' → 1 │ '01.0' → 0 ┊ '-01.0' → 0 │ '10.0' → 1 ┊ '-10.0' → 1 │
    # └───────────╨──────────┴───────────┴───────────┴────────────┴───────────┴────────────┴────────────┴─────────────┴────────────┴─────────────┘

    static function validate_number($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
            '%^(?<integer>[-]{0,1}[1-9][0-9]*|0)$|'.
             '^(?<float_s>[-]{0,1}[0-9]'.   '[.][0-9]{1,})$|'.
             '^(?<float_l>[-]{0,1}[1-9][0-9]+[.][0-9]{1,})$%'
        ]]);
    }

    static function validate_hex_color($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' =>
            '%^#(?<R>[a-f0-9]{2})'.
               '(?<G>[a-f0-9]{2})'.
               '(?<B>[a-f0-9]{2})$%'
        ]]);
    }

    static function validate_email($value) {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    static function validate_hash($value, $length = 32) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-f0-9]{'.$length.'}$%']]); # 32 - md5 | 40 - sha1 | …
    }

    static function validate_id($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^['.field_id_text::CHARACTERS_ALLOWED.']+$%']]);
    }

    static function validate_property_name($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-zA-Z_][a-zA-Z0-9_]*$%']]);
    }

    static function validate_ip_v4($value) {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    static function validate_ip_v6($value) {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    static function validate_mime_type($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[a-z]{1,20}/[a-zA-Z0-9\\+\\-\\.]{1,100}$%']]);
    }

    static function validate_nickname($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^['.field_nickname::CHARACTERS_ALLOWED.']{4,32}$%']]);
    }

    static function validate_tel($value) {
        return filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '%^[+][0-9]{1,14}$%']]);
    }

    static function validate_realpath($value) {
        $value = realpath($value);
        if ($value !== false && static::php_is_on_win())
            $value = str_replace('\\', '/', $value);
        return $value;
    }

    static function validate_url($value, $flags = null) {
        return filter_var($value, FILTER_VALIDATE_URL, $flags);
    }

    static function validate_range($min, $max, $step, $value) {
        if (bccomp(           $value, $min, 20) /* $value  <  $min */ ===  -1) return false;
        if (bccomp(           $value, $max, 20) /* $value  >  $max */ ===  +1) return false;
        if (bccomp(           $value, $min, 20) /* $value === $min */ ===   0) return true;
        if (bccomp(           $value, $max, 20) /* $value === $max */ ===   0) return true;
        if (rtrim(bcdiv(bcsub($value, $min, 20), $step, 20), '0')[-1] === '.') return true;
        return false;
    }

    # ◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦◦

    static function sanitize_id($value, $corrector = '-') {
        return preg_replace('%[^a-z0-9_\\-]%S', $corrector, strtolower($value));
    }

    static function sanitize_css_units($value) {
        return preg_replace('%[^a-zA-Z0-9\\-\\.\\#\\%]%S', '-', $value); # eg: -1px, 2.3em, #a1b2c3, DarkMagenta, 100%
    }

    static function sanitize_url($value) {
        # remove all characters except: ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_,;:.!?+-*/='"`^~(){}[]<>|\$#@%&
        return filter_var($value, FILTER_SANITIZE_URL);
    }

    static function sanitize_file_part($value, $characters_allowed = 'a-zA-Z0-9_\\-\\.', $max_length = 220, $prefix = '') {
        $value = trim($value, '.');
        $value = preg_replace_callback('%(?<char>[^'.$characters_allowed.'])%uS', function ($c_match) use ($prefix) {
            if (       $c_match['char']  === ' ') return '-';
            if (strlen($c_match['char']) ===  1 ) return $prefix.dechex(ord($c_match['char'][0]));
            if (strlen($c_match['char']) ===  2 ) return $prefix.dechex(ord($c_match['char'][0])).$prefix.dechex(ord($c_match['char'][1]));
            if (strlen($c_match['char']) ===  3 ) return $prefix.dechex(ord($c_match['char'][0])).$prefix.dechex(ord($c_match['char'][1])).$prefix.dechex(ord($c_match['char'][2]));
            if (strlen($c_match['char']) ===  4 ) return $prefix.dechex(ord($c_match['char'][0])).$prefix.dechex(ord($c_match['char'][1])).$prefix.dechex(ord($c_match['char'][2])).$prefix.dechex(ord($c_match['char'][3]));
        }, $value);
        return substr($value, 0, $max_length);
    }

    ##############################
    ### functionality for hash ###
    ##############################

    # hash performance (5 million iterations):
    # ┌────────────────────────╥─────────────┬────────┬─────────────────────────┐
    # │ function               ║ time (sec.) │ is hex │ has 32-bit sign problem │
    # ╞════════════════════════╬═════════════╪════════╪═════════════════════════╡
    # │ crc32(…)               ║ 0.093       │ no     │ yes                     │
    # │ md5(…)                 ║ 0.320       │ yes    │ no                      │
    # │ sha1(…)                ║ 0.335       │ yes    │ no                      │
    # │ hash('md2', …)         ║ 4.773       │ yes    │ no                      │
    # │ hash('md4', …)         ║ 0.329       │ yes    │ no                      │
    # │ hash('md5', …)         ║ 0.374       │ yes    │ no                      │
    # │ hash('sha1', …)        ║ 0.390       │ yes    │ no                      │
    # │ hash('sha256', …)      ║ 0.671       │ yes    │ no                      │
    # │ hash('sha512/256', …)  ║ 0.852       │ yes    │ no                      │
    # │ hash('sha512', …)      ║ 0.879       │ yes    │ no                      │
    # │ hash('sha3-224', …)    ║ 4.512       │ yes    │ no                      │
    # │ hash('sha3-256', …)    ║ 4.680       │ yes    │ no                      │
    # │ hash('sha3-512', …)    ║ 5.100       │ yes    │ no                      │
    # │ hash('ripemd128', …)   ║ 0.572       │ yes    │ no                      │
    # │ hash('ripemd320', …)   ║ 0.728       │ yes    │ no                      │
    # │ hash('whirlpool', …)   ║ 1.278       │ yes    │ no                      │
    # │ hash('tiger128,3', …)  ║ 0.391       │ yes    │ no                      │
    # │ hash('tiger192,4', …)  ║ 0.443       │ yes    │ no                      │
    # │ hash('snefru', …)      ║ 2.707       │ yes    │ no                      │
    # │ hash('snefru256', …)   ║ 2.716       │ yes    │ no                      │
    # │ hash('gost', …)        ║ 1.970       │ yes    │ no                      │
    # │ hash('gost-crypto', …) ║ 2.153       │ yes    │ no                      │
    # │ hash('adler32', …)     ║ 0.204       │ yes    │ no                      │
    # │ hash('crc32', …)       ║ 0.198       │ yes    │ no                      │
    # │ hash('crc32b', …)      ║ 0.200       │ yes    │ no                      │
    # │ hash('fnv132', …)      ║ 0.195       │ yes    │ no                      │
    # │ hash('fnv1a32', …)     ║ 0.201       │ yes    │ no                      │
    # │ hash('fnv164', …)      ║ 0.203       │ yes    │ no                      │
    # │ hash('fnv1a64', …)     ║ 0.209       │ yes    │ no                      │
    # │ hash('joaat', …)       ║ 0.200       │ yes    │ no                      │
    # │ hash('haval128,3', …)  ║ 0.747       │ yes    │ no                      │
    # │ hash('haval256,5', …)  ║ 1.134       │ yes    │ no                      │
    # └────────────────────────╨─────────────┴────────┴─────────────────────────┘

    static function hash_get($data) {
        if (gettype($data) === 'string')
             return md5($data);
        else return md5(serialize($data));
    }

    static function hash_get_mini($data, $length = 8) {
        return substr(static::hash_get($data), 0, $length);
    }

    static function random_bytes_generate($length = 8, $characters = '0123456789') {
        $result = '';
        for ($i = 0; $i < $length; $i++)
            $result.= $characters[random_int(0, strlen($characters) - 1)];
        return $result;
    }

    static function random_part_get() {
        $hex_time = str_pad(dechex(time()),                        8, '0', STR_PAD_LEFT);
        $hex_rand = str_pad(dechex(random_int(0, PHP_INT_32_MAX)), 8, '0', STR_PAD_LEFT);
        return $hex_time.$hex_rand;
    }

    static function number_part_get($name, $keys) {
        $used_numbers = [];
        foreach ($keys as $c_name) {
            if (strpos($c_name, $name) === 0) {
                $suffix = substr($c_name, strlen($name));
                if ((string)$suffix === (string)(int)$suffix) {
                    $used_numbers[]= (int)$suffix; }}}
        if (count($used_numbers) !== 0) return (string)(max($used_numbers) + 1);
        if (count($used_numbers) === 0) return '2';
    }

    #######################
    ### PHP environment ###
    #######################

    static function php_is_on_win() {
        return DIRECTORY_SEPARATOR === '\\';
    }

    static function php_max_execution_time_get()  {return ini_get('max_execution_time');}
    static function php_max_file_uploads_get()    {return ini_get('max_file_uploads');}
    static function php_max_input_time_get()      {return ini_get('max_input_time');}
    static function php_memory_limit_get()        {return ini_get('memory_limit');}
    static function php_post_max_size_get()       {return ini_get('post_max_size');}
    static function php_upload_max_filesize_get() {return ini_get('upload_max_filesize');}

    static function php_memory_limit_bytes_get() {
        $value = static::php_memory_limit_get();
        if (static::is_abbreviated_bytes($value))
             return static::abbreviated_to_bytes($value);
        else return (int)$value;
    }

    static function php_upload_max_filesize_bytes_get() {
        $value = static::php_upload_max_filesize_get();
        if (static::is_abbreviated_bytes($value))
             return static::abbreviated_to_bytes($value);
        else return (int)$value;
    }

    static function php_post_max_size_bytes_get() {
        $value = static::php_post_max_size_get();
        if (static::is_abbreviated_bytes($value))
             return static::abbreviated_to_bytes($value);
        else return (int)$value;
    }

    ############
    ### cron ###
    ############

    static function is_cron_run($period) {
        $settings = module::settings_get('core');
        return !empty($settings->cron_last_run_date) &&
                      $settings->cron_last_run_date > static::datetime_get('-'.$period.' second');
    }

    static function cron_run_register() {
        return storage::get('data')->changes_insert('core', 'update', 'settings/core/cron_last_run_date', static::datetime_get());
    }

    ########################
    ### shared functions ###
    ########################

    static function fractional_part_length_get($value, $no_zeros = true) {
        # case for strings (examples: '', '100', '0', '0.00100') but NOT exponential (examples: '1.23e-6')
        if (is_string($value) && !strpbrk($value, 'eE')) {
            $fpart = strrchr($value, '.');
            if ($fpart !== false && $no_zeros === true) return strlen(rtrim($fpart, '0')) - 1;
            if ($fpart !== false && $no_zeros !== true) return strlen(      $fpart      ) - 1;
            return 0;
        }
        # case for integer and float (examples: 100, 0, 0.00100) but NOT exponential (examples: 1.23e-6)
        if (is_int($value) || is_float($value)) {
            $fpart = ltrim(bcsub($value, (string)(int)$value, 40), '0');
            if ($no_zeros === true) return strlen(rtrim($fpart, '0')) - 1;
            if ($no_zeros !== true) return strlen(      $fpart      ) - 1;
            return 0;
        }
    }

    static function exponencial_string_normalize($value) {
        if (is_string($value) && is_numeric($value))
            if ($value !== (string)(int)$value && $value[0] !== '0' && strpbrk($value, 'eE'))
                return static::format_number($value, static::FPART_MAX_LEN);
        return $value;
    }

    static function strtolower_en($value) {
        return preg_replace_callback('%(?<char>[A-Z])%S', function ($c_match) {
            return strtolower($c_match['char']);
        }, $value);
    }

    static function return_rendered($value) {
        return is_object($value) && method_exists($value, 'render') ?
                         $value-> render() :
                         $value;
    }

    static function return_null_if_empty($value) {
        return $value ?: null;
    }

    static function return_htmlspecialchars_encoded($value) {
        return htmlspecialchars($value, ENT_COMPAT|ENT_HTML5, 'UTF-8');
    }

}
