<?php

##################################################################
### Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore;

use DateTime;
use DateTimeZone;
use ReflectionClass;
use stdClass;

abstract class Core {

    const EMPTY_IP = '::';
    const DATE_PERIOD_H = 60 * 60;
    const DATE_PERIOD_D = 60 * 60 * 24;
    const DATE_PERIOD_W = 60 * 60 * 24 * 7;
    const DATE_PERIOD_M = 60 * 60 * 24 * 30;
    const FPART_MAX_LEN = 10;

    const SORT_ASC = 0;
    const SORT_DSC = 1;

    const LABEL_UNSUPPORTED_TYPE = '__UNSUPPORTED_TYPE__';
    const LABEL_NO_RENDERER      = '__NO_RENDERER__';

    ###################
    ### environment ###
    ###################

    static function system_build_number_get() {
        return Storage::get('data')->select('bundle/system/build');
    }

    static function is_CLI() {
        return PHP_SAPI === 'cli';
    }

    static function is_Win() {
        return DIRECTORY_SEPARATOR === '\\';
    }

    static function is_IIS() {
        return !empty($_SERVER['IIS_WasUrlRewritten']);
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

    ####################
    ### boot modules ###
    ####################

    static function boot_select($type = 'enabled') {
        $boot = Data::select('boot');
        return $boot->{'modules_'.$type} ?? [];
    }

    static function boot_insert($module_id, $module_path, $type) {
        $boot = Data::select('boot') ?: new stdClass;
        $boot_buffer = [];
        if ($boot && isset($boot->{'modules_'.$type}))
            $boot_buffer = $boot->{'modules_'.$type};
        $boot_buffer[$module_id] = $module_path;
        asort($boot_buffer);
        $boot->{'modules_'.$type} = $boot_buffer;
        return Data::update('boot', $boot, '', ['build_date' => static::datetime_get()]);
    }

    static function boot_delete($module_id, $type) {
        $boot = Data::select('boot') ?: new stdClass;
        $boot_buffer = [];
        if ($boot && isset($boot->{'modules_'.$type}))
            $boot_buffer = $boot->{'modules_'.$type};
        unset($boot_buffer[$module_id]);
        $boot->{'modules_'.$type} = $boot_buffer;
        return Data::update('boot', $boot, '', ['build_date' => static::datetime_get()]);
    }

    ###############################################
    ### functionality for class|trait|interface ###
    ###############################################

    static function structure_autoload($name) {
        $name = mb_strtolower($name);
        if ($name === 'effcore\\timer'             ) {require_once(DIR_SYSTEM.'module_core/backend/Timer.php'                     ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/Timer.php'                     , 'ok'); return;}
        if ($name === 'effcore\\storage_data'      ) {require_once(DIR_SYSTEM.'module_storage/backend/patterns/Storage_Data.php'  ); Console::log_simple_insert('file', 'insertion', 'system/module_storage/backend/patterns/Storage_Data.php'  , 'ok'); return;}
        if ($name === 'effcore\\module'            ) {require_once(DIR_SYSTEM.'module_core/backend/patterns/Module.php'           ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/patterns/Module.php'           , 'ok'); return;}
        if ($name === 'effcore\\module_embedded'   ) {require_once(DIR_SYSTEM.'module_core/backend/patterns/Module_embedded.php'  ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/patterns/Module_embedded.php'  , 'ok'); return;}
        if ($name === 'effcore\\module_as_profile' ) {require_once(DIR_SYSTEM.'module_core/backend/patterns/Module_as_profile.php'); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/patterns/Module_as_profile.php', 'ok'); return;}
        if ($name === 'effcore\\security'          ) {require_once(DIR_SYSTEM.'module_core/backend/Security.php'                  ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/Security.php'                  , 'ok'); return;}
        if ($name === 'effcore\\data'              ) {require_once(DIR_SYSTEM.'module_core/backend/Data.php'                      ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/Data.php'                      , 'ok'); return;}
        if ($name === 'effcore\\dynamic'           ) {require_once(DIR_SYSTEM.'module_core/backend/Dynamic.php'                   ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/Dynamic.php'                   , 'ok'); return;}
        if ($name === 'effcore\\file'              ) {require_once(DIR_SYSTEM.'module_core/backend/File.php'                      ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/File.php'                      , 'ok'); return;}
        if ($name === 'effcore\\directory'         ) {require_once(DIR_SYSTEM.'module_core/backend/Directory.php'                 ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/Directory.php'                 , 'ok'); return;}
        if ($name === 'effcore\\cache'             ) {require_once(DIR_SYSTEM.'module_core/backend/Cache.php'                     ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/Cache.php'                     , 'ok'); return;}
        if ($name === 'effcore\\message'           ) {require_once(DIR_SYSTEM.'module_core/backend/patterns/Message.php'          ); Console::log_simple_insert('file', 'insertion', 'system/module_core/backend/patterns/Message.php'          , 'ok'); return;}
        if ($name === 'effcore\\markup'            ) {require_once(DIR_SYSTEM.'module_page/backend/patterns/Markup.php'           ); Console::log_simple_insert('file', 'insertion', 'system/module_page/backend/patterns/Markup.php'           , 'ok'); return;}
        if ($name === 'effcore\\node'              ) {require_once(DIR_SYSTEM.'module_page/backend/patterns/Node.php'             ); Console::log_simple_insert('file', 'insertion', 'system/module_page/backend/patterns/Node.php'             , 'ok'); return;}
        if ($name === 'effcore\\node_simple'       ) {require_once(DIR_SYSTEM.'module_page/backend/patterns/Node_simple.php'      ); Console::log_simple_insert('file', 'insertion', 'system/module_page/backend/patterns/Node_simple.php'      , 'ok'); return;}
        if ($name === 'effcore\\text_multiline'    ) {require_once(DIR_SYSTEM.'module_page/backend/patterns/Text_multiline.php'   ); Console::log_simple_insert('file', 'insertion', 'system/module_page/backend/patterns/Text_multiline.php'   , 'ok'); return;}
        if ($name === 'effcore\\text_simple'       ) {require_once(DIR_SYSTEM.'module_page/backend/patterns/Text_simple.php'      ); Console::log_simple_insert('file', 'insertion', 'system/module_page/backend/patterns/Text_simple.php'      , 'ok'); return;}
        if ($name === 'effcore\\text'              ) {require_once(DIR_SYSTEM.'module_page/backend/patterns/Text.php'             ); Console::log_simple_insert('file', 'insertion', 'system/module_page/backend/patterns/Text.php'             , 'ok'); return;}
        $c_item_info = static::structures_select()[$name] ?? null;
        if ($c_item_info !== null) Console::log_insert('autoload', 'search', $name, 'ok');
        if ($c_item_info === null) Console::log_insert('autoload', 'search', $name, 'error');
        if ($c_item_info) {
            $c_file = new File(DIR_ROOT.$c_item_info->file);
            $c_file->require();
        }
    }

    static function structures_cache_cleaning_after_install() {
        foreach (static::structures_select() as $c_full_name => $c_structure) {
            if (isset($c_structure->implements['effcore\\cache_cleaning_after_install'])) {
                $c_full_name::cache_cleaning();
            }
        }
    }

    static function structures_select($modules_to_include = []) {
        $result = Cache::select('structures');
        if ($result) return $result;
        else {
            $result       = [];
            $files        = [];
            $preparse     = Storage_Data::data_find_and_parse_modules_and_bundles();
            $modules_path = $preparse->modules_path;
            $enabled      = static::boot_select('enabled') + $modules_to_include; # === Module::get_enabled_by_boot() + $modules_to_include
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
                $c_files = Directory::items_select(DIR_ROOT.$c_enabled_path, '%^.*\\.php$%');
                foreach ($c_files as $c_file) {
                    $c_path_relative = $c_file->path_get_relative();
                    $c_module_id = key(static::array_search__array_item_in_value($c_path_relative, $modules_path));
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
                             $result[mb_strtolower(                        $c_item->name)] = $c_item;
                        else $result[mb_strtolower($c_item->namespace.'\\'.$c_item->name)] = $c_item;
                    }
                }
            }
            ksort($result);
            Cache::update('structures', $result, '', ['build_date' => static::datetime_get()]);
            return $result;
        }
    }

    static function structure_is_exists($name) {
        $name = trim(mb_strtolower($name), '\\');
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
        return str_contains($string, '::');
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
                     $result = $prefix.' = Core::class_get_new_instance(\''.addcslashes('\\'.$class_name, "'\\").'\');'.NL;
                else $result = $prefix.' = new \\'.$class_name.';'.NL;
                foreach ($data as $c_key => $c_value) {
                    if (array_key_exists($c_key, $defaults) && $defaults[$c_key] === $c_value) continue;
                    if (Security::validate_property_name($c_key))
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

    static function data_serialize($data, $is_optimized = true, $is_ksort = false) {
        $result = '';
        switch (gettype($data)) {
            case 'string' : return 's:'.strlen($data).':'.     '"'.$data.'"'                                                   .';';
            case 'boolean': return 'b:'.                          ($data ? '1' : '0')                                          .';';
            case 'integer': return 'i:'.                           $data                                                       .';';
            case 'double' : return 'd:'.($is_optimized === false ? $data : static::format_number($data, static::FPART_MAX_LEN)).';';
            case 'NULL'   : return 'N'                                                                                         .';';
            case 'array'  :
                $result_children = [];
                if ($is_ksort) ksort($data, SORT_STRING);
                foreach ($data as $c_key => $c_val) {
                    $result_children[] = static::data_serialize($c_key, $is_optimized, $is_ksort);
                    $result_children[] = static::data_serialize($c_val, $is_optimized, $is_ksort); }
                $result = 'a:'.count($data).':{'.implode('', $result_children).'}';
                break;
            case 'object':
                $class_name = get_class($data);
                $reflection = new ReflectionClass($class_name);
                $defaults = $reflection->getDefaultProperties();
                $properties = [];
                foreach ($data as $c_key => $c_val)
                    $properties  [$c_key] = $c_val;
                if ($is_ksort) ksort($properties, SORT_STRING);
                $result_children = [];
                foreach ($properties as $c_key => $c_val) {
                    if ($is_optimized && array_key_exists($c_key, $defaults) && $defaults[$c_key] === $c_val) continue;
                    $result_children[] = static::data_serialize($c_key, $is_optimized, $is_ksort);
                    $result_children[] = static::data_serialize($c_val, $is_optimized, $is_ksort); }
                $result = 'O:'.strlen($class_name).
                                 ':"'.$class_name.'":'.(int)(count($result_children) / 2).':{'.
                                                       implode('', $result_children).'}';
                break;
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

    #################################################################
    ### functionality for dpath (data path) and npath (node path) ###
    #################################################################

    static function dpath_get_pointers(&$data, $dpath, $is_unique_keys = false) {
        $result = [];
        $null = null;
        $c_pointer = &$data;
        foreach (explode('/', $dpath) as $c_name) {
            if ( $c_pointer !== null && static::arrobj_is_exists_value($c_pointer, $c_name) )
                 $c_pointer = &static::arrobj_select_value($c_pointer, $c_name);
            else $c_pointer = &$null;
            if ($is_unique_keys && array_key_exists($c_name, $result))
                 $c_key = $c_name.static::generate_numerical_suffix($c_name, array_keys($result));
            else $c_key = $c_name;
            $result[$c_key] = &$c_pointer;
        }
        return $result;
    }

    static function npath_get_pointers(&$node, $npath, $is_unique_keys = false) {
        $result = [];
        $null = null;
        $c_pointer = &$node;
        foreach (explode('/', $npath) as $c_name) {
            if ( $c_pointer !== null && array_key_exists($c_name, $c_pointer->children) )
                 $c_pointer = &$c_pointer->children[$c_name];
            else $c_pointer = &$null;
            if ($is_unique_keys) $result[       ] = &$c_pointer;
            else                 $result[$c_name] = &$c_pointer;
        }
        return $result;
    }

    static function path_get_depth($path) {
        return count_chars($path, 1)[ord('/')] ?? 0;
    }

    #############################################
    ### functionality for mix of array|object ###
    #############################################

    static function arrobj_is_exists_value($data, $name) {
        if (is_array ($data)) return array_key_exists($name, $data);
        if (is_object($data)) return  property_exists($data, $name);
    }

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

    static function array_sort(&$array, $order = self::SORT_DSC, $translated = true) {
        uasort($array, function ($a, $b) use ($order, $translated) {
            if ($order === static::SORT_ASC && $translated === false) return                    $b  <=>                    $a;
            if ($order === static::SORT_DSC && $translated === false) return                    $a  <=>                    $b;
            if ($order === static::SORT_ASC && $translated !== false) return Translation::apply($b) <=> Translation::apply($a);
            if ($order === static::SORT_DSC && $translated !== false) return Translation::apply($a) <=> Translation::apply($b);
        });
        return $array;
    }

    static function array_sort_by_string(&$array, $key = 'title', $order = self::SORT_DSC, $translated = true) {
        uasort($array, function ($a, $b) use ($key, $order, $translated) {
            if ($order === static::SORT_ASC && $translated === false) return                     (is_object($b) ? $b->{$key} : $b[$key])   <=>                     (is_object($a) ? $a->{$key} : $a[$key])  ;
            if ($order === static::SORT_DSC && $translated === false) return                     (is_object($a) ? $a->{$key} : $a[$key])   <=>                     (is_object($b) ? $b->{$key} : $b[$key])  ;
            if ($order === static::SORT_ASC && $translated !== false) return Translation::apply( (is_object($b) ? $b->{$key} : $b[$key]) ) <=> Translation::apply( (is_object($a) ? $a->{$key} : $a[$key]) );
            if ($order === static::SORT_DSC && $translated !== false) return Translation::apply( (is_object($a) ? $a->{$key} : $a[$key]) ) <=> Translation::apply( (is_object($b) ? $b->{$key} : $b[$key]) );
        });
        return $array;
    }

    static function array_sort_by_number(&$array, $key = 'weight', $order = self::SORT_ASC) {
        if (count($array) > 1) {
            $buffer = [];
            $real_weights = [];
            foreach ($array as $c_row_id => &$c_item) {           $c_weight = 0;
                if (is_array ($c_item) && isset($c_item  [$key])) $c_weight = $c_item  [$key];
                if (is_object($c_item) && isset($c_item->{$key})) $c_weight = $c_item->{$key};
                if (!is_numeric($c_weight)) {
                    trigger_error('Weight is not numeric in Core::array_sort_by_number().', E_USER_WARNING);
                    $c_weight = 0; }
                if ($order === static::SORT_ASC) $real_weights[$c_weight] = array_key_exists($c_weight, $real_weights) ? $real_weights[$c_weight] - .0001 : $c_weight;
                if ($order === static::SORT_DSC) $real_weights[$c_weight] = array_key_exists($c_weight, $real_weights) ? $real_weights[$c_weight] + .0001 : $c_weight;
                $buffer[$c_row_id] = [
                    'weight' => $real_weights[$c_weight],
                    'object' => &$c_item
                ];
            }
            uasort($buffer, function ($a, $b) use ($order) {
                if ($order === static::SORT_ASC) return $b['weight'] <=> $a['weight'];
                if ($order === static::SORT_DSC) return $a['weight'] <=> $b['weight'];
            });
            $array = [];
            foreach ($buffer as $c_row_id => &$c_buf_item) {
                $array[$c_row_id] = &$c_buf_item['object'];
            }
        }
        return $array;
    }

    static function array_keys_map($array) {
        return array_combine($array, $array);
    }

    static function in_array($value, $array) {
        foreach ($array as $c_value)
            if ((string)$value === (string)$c_value)
                return true;
        return false;
    }

    static function array_search__value_in_array_item($value, $array) {
        $result = [];
        foreach ($array as $c_key => $c_value)
            if (str_starts_with((string)$c_value, (string)$value))
                $result[$c_key] = $c_value;
        return $result;
    }

    static function array_search__array_item_in_value($value, $array) {
        $result = [];
        foreach ($array as $c_key => $c_value)
            if (str_starts_with((string)$value, (string)$c_value))
                $result[$c_key] = $c_value;
        return $result;
    }

    ########################
    ### bytes conversion ###
    ########################

    static function is_abbreviated_bytes($number) {
        $character = substr($number, -1);
        return static::in_array($character, ['B', 'K', 'M', 'G', 'T']);
    }

    static function abbreviated_to_bytes($abbreviated) {
        $powers = array_flip(['B', 'K', 'M', 'G', 'T']);
        $character = strtoupper(substr($abbreviated, -1));
        $value = (int)substr($abbreviated, 0, -1);
        return $value * 1024 ** $powers[$character];
    }

    static function bytes_to_abbreviated($bytes, $is_IEC = false) {
        if ($bytes && fmod($bytes, 1024 ** 4) === .0) return ($bytes / 1024 ** 4).($is_IEC ? 'TiB' : 'T');
        if ($bytes && fmod($bytes, 1024 ** 3) === .0) return ($bytes / 1024 ** 3).($is_IEC ? 'GiB' : 'G');
        if ($bytes && fmod($bytes, 1024 ** 2) === .0) return ($bytes / 1024 ** 2).($is_IEC ? 'MiB' : 'M');
        if ($bytes && fmod($bytes, 1024 ** 1) === .0) return ($bytes / 1024 ** 1).($is_IEC ? 'KiB' : 'K');
        else return $bytes.'B';
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

    static function timezone_get_server()   {return                                  'UTC';}
    static function timezone_get_client()   {return User::get_current()->timezone ?? 'UTC';}
    static function timezone_set_client($timezone) {User::get_current()->timezone = $timezone;}

    static function timezone_get_offset_s($name = 'UTC') {$value = (new DateTimeZone($name))->getOffset(new DateTime); return $value;}
    static function timezone_get_offset_m($name = 'UTC') {$value = (new DateTimeZone($name))->getOffset(new DateTime); return $value ? $value / 60      : 0;}
    static function timezone_get_offset_h($name = 'UTC') {$value = (new DateTimeZone($name))->getOffset(new DateTime); return $value ? $value / 60 / 60 : 0;}
    static function timezone_get_offset_string_time($name = 'UTC') {return (new DateTime('now', new DateTimeZone($name)))->format('P');}

    static function T_datetime_to_datetime($datetime) {$date = DateTime::createFromFormat('Y-m-d\\TH:i:s', $datetime, new DateTimeZone('UTC') ); if ($date) return $date->format('Y-m-d H:i:s'  );}
    static function datetime_to_T_datetime($datetime) {$date = DateTime::createFromFormat('Y-m-d H:i:s'  , $datetime, new DateTimeZone('UTC') ); if ($date) return $date->format('Y-m-d\\TH:i:s');}

    static function       date_get($offset = '', $format = 'Y-m-d'        ) {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}
    static function       time_get($offset = '', $format =       'H:i:s'  ) {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}
    static function   datetime_get($offset = '', $format = 'Y-m-d H:i:s'  ) {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}
    static function T_datetime_get($offset = '', $format = 'Y-m-d\\TH:i:s') {return (new DateTime('now', new DateTimeZone('UTC')))->modify( $offset ?: '+0' )->format( $format );}

    ####################################
    ### functionality for formatting ###
    ####################################

    static function format_number($number, $precision = 0, $dec_point = '.', $thousands = '', $no_zeros = true) {
        $result = $precision > 0 ? # disable the rounding effect
             substr(number_format($number, $precision + 1, $dec_point, $thousands), 0, -1) :
                    number_format($number, $precision    , $dec_point, $thousands);
        if ($no_zeros && str_contains($result, $dec_point)) {
            $result = rtrim($result, '0');
            $result = rtrim($result, $dec_point);
        }
        return $result;
    }

    static function format_bytes($bytes, $is_IEC = true) {
        if ($bytes && fmod($bytes, 1024 ** 4) === .0) return static::format_number($bytes / 1024 ** 4).' '.($is_IEC ? 'TiB' : 'T');
        if ($bytes && fmod($bytes, 1024 ** 3) === .0) return static::format_number($bytes / 1024 ** 3).' '.($is_IEC ? 'GiB' : 'G');
        if ($bytes && fmod($bytes, 1024 ** 2) === .0) return static::format_number($bytes / 1024 ** 2).' '.($is_IEC ? 'MiB' : 'M');
        if ($bytes && fmod($bytes, 1024 ** 1) === .0) return static::format_number($bytes / 1024 ** 1).' '.($is_IEC ? 'KiB' : 'K');
        else                                          return static::format_number($bytes            ).' '.(                  'B');
    }

    static function format_persent($number, $precision = 2) {return static::format_number(floatval($number), $precision).'%';}
    static function format_msecond($number, $precision = 6) {return static::format_number(floatval($number), $precision, '.', '', false);}
    static function format_version($number)                 {return static::format_number(floatval($number), 3, '.', '', false);}

    static function format_logic($value) {
        return $value ? 'yes' : 'no';
    }

    ########################
    ### generate strings ###
    ########################

    static function generate_random_bytes($length = 8, $characters = '0123456789') {
        $result = '';
        for ($i = 0; $i < $length; $i++)
            $result.= $characters[random_int(0, strlen($characters) - 1)];
        return $result;
    }

    static function generate_random_part() {
        $hex_time = str_pad(dechex(time()),                        8, '0', STR_PAD_LEFT);
        $hex_rand = str_pad(dechex(random_int(0, PHP_INT_32_MAX)), 8, '0', STR_PAD_LEFT);
        return $hex_time.$hex_rand;
    }

    static function generate_numerical_suffix($name, $used_names) {
        $numbers = [];
        foreach ($used_names as $c_name) {
            if (str_starts_with($c_name, $name)) {
                $suffix = substr($c_name, strlen($name));
                if (Security::validate_str_int($suffix)) {
                    $numbers[]= (int)$suffix; }}}
        if (count($numbers) !== 0) return (string)(max($numbers) + 1);
        if (count($numbers) === 0) return '2';
    }

    #########################
    ### numeric functions ###
    #########################

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

    ########################
    ### shared functions ###
    ########################

    static function html_entity_encode($value) {
        # ENT_COMPAT: will convert double-quotes and leave single-quotes alone
        return htmlspecialchars((string)$value, ENT_COMPAT|ENT_SUBSTITUTE|ENT_HTML5, 'UTF-8');
    }

    static function html_entity_encode_total($value, $is_hex = false) {
        return preg_replace_callback('%(?<char>.)%uS', function ($c_match) use ($is_hex) {
            $c_attempt = htmlspecialchars($c_match['char'], ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML5, 'UTF-8');
            if ($c_attempt === $c_match['char'] && $is_hex === true) return '&#x'.dechex(mb_ord($c_match['char'])).';';
            if ($c_attempt === $c_match['char'] && $is_hex !== true) return '&#'.        mb_ord($c_match['char']) .';';
            return $c_attempt;
        }, $value);
    }

    static function html_entity_decode_total($value) {
        $value = html_entity_decode($value, ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML5, 'UTF-8');
        return preg_replace_callback('%[&][#][x](?<hex_value>[0]{0,1024}[0-9a-f]{1,5})[;]{0,1}|'.
                                      '[&][#]'.'(?<dec_value>[0]{0,1024}[0-9'.']{1,6})[;]{0,1}%iS', function ($c_match) {
            if (!empty($c_match['hex_value'])) return mb_chr(hexdec(ltrim($c_match['hex_value'], '0')), 'UTF-8');
            if (!empty($c_match['dec_value'])) return mb_chr(       ltrim($c_match['dec_value'], '0'),  'UTF-8');
            return '';
        }, $value);
        return $value;
    }

    static function to_rendered($value) {
        return is_object($value) && method_exists($value, 'render') ?
                         $value->render() :
                         $value;
    }

    static function to_null_if_empty($value) {
        return $value ?: null;
    }

    static function to_url_from_path($value) {
        return is_string($value) && strlen($value) && $value[0] !== '/' ? '/'.$value : $value;
    }

    static function to_current_lang($value) {
        $result = Translation::filter($value, Language::code_get_current());
        if ($result) return implode(NL, $result);
        else         return '';
    }

    static function to_markdown($value) {
        return new Node([], Markdown::markdown_to_markup($value));
    }

}
