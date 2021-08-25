<?php

  ##################################################################
  ### Copyright © 2017—2021 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore {
          class storage_nosql_files implements has_external_cache {

  public $name;

  function select($dpath, $expand_cache = false, $with_restore = true) {
    $parts = explode('/', $dpath);
    $catalog_name = array_shift($parts);
    static::init($catalog_name, $with_restore);
    if (isset(static::$data[$catalog_name])) {
      $c_pointer = static::$data[$catalog_name];
      foreach ($parts as $c_part) {
        $c_pointer = &core::arrobj_select_value($c_pointer, $c_part);
        if ($expand_cache && $c_pointer instanceof external_cache) {
          $c_pointer =
          $c_pointer->load_from_nosql_storage();
        }
      }
      return $c_pointer;
    }
  }

  function changes_insert($module_id, $action, $dpath, $value = null, $rebuild = true) {
  # insert new dynamic changes
    $changes_d = data::select('changes') ?: [];
    if (!isset($changes_d[$module_id]           )) $changes_d[$module_id] = new \stdClass;
    if (!isset($changes_d[$module_id]->{$action})) $changes_d[$module_id]->{$action} = [];
    $changes_d[$module_id]->{$action}[$dpath] = $value;
    $result = data::update('changes', $changes_d, '', ['build_date' => core::datetime_get()]);
  # prevent opcache work
    if ($result) {
      static::$changes_dynamic['changes'] = $changes_d;
      if ($rebuild) {
        $result&= static::cache_update();
      }
    }
    return $result;
  }

  function changes_delete($module_id, $action, $dpath, $rebuild = true) {
  # delete old dynamic changes
    $changes_d = data::select('changes') ?: [];
    if (isset($changes_d[$module_id]->{$action}[$dpath]))                                           unset($changes_d[$module_id]->{$action}[$dpath]);
    if (isset($changes_d[$module_id]->{$action}) && (array)$changes_d[$module_id]->{$action} == []) unset($changes_d[$module_id]->{$action}        );
    if (isset($changes_d[$module_id])            && (array)$changes_d[$module_id]            == []) unset($changes_d[$module_id]                   );
    $result = data::update('changes', $changes_d, '', ['build_date' => core::datetime_get()]);
  # prevent opcache work
    if ($result) {
      static::$changes_dynamic['changes'] = $changes_d;
      if ($rebuild) {
        $result&= static::cache_update();
      }
    }
    return $result;
  }

  function changes_delete_all($module_id, $rebuild = true) {
  # delete old dynamic changes for specified module
    $changes_d = data::select('changes') ?: [];
    unset($changes_d[$module_id]);
    $result = data::update('changes', $changes_d, '', ['build_date' => core::datetime_get()]);
  # prevent opcache work
    if ($result) {
      static::$changes_dynamic['changes'] = $changes_d;
      if ($rebuild) {
        $result&= static::cache_update();
      }
    }
    return $result;
  }

  ###########################
  ### static declarations ###
  ###########################

  static public $data = [];
  static public $changes_dynamic;

  static function not_external_properties_get() {
    return [
      'name' => 'name'
    ];
  }

  static function init($catalog_name, $with_restore = true) {
    if (!isset(static::$data[$catalog_name])) {
      console::log_insert('storage', 'init.', 'catalog "%%_catalog_name" in storage "%%_storage_name" will be initialized', 'ok', 0, ['catalog_name' => $catalog_name, 'storage_name' => 'files']);
      $cache = cache::select('data--'.$catalog_name);
      if     ($cache       ) static::$data[$catalog_name] = $cache;
      elseif ($with_restore) static::cache_update();
    }
  }

  static function cache_cleaning() {
    static::$data = [];
  }

  static function cache_update($modules_to_include = []) {
    $result = true;
  # init data and original data
    static::$data      = [];
            $data_orig = cache::select('data_original');
    if (!$data_orig) {
      $data_orig = static::data_find_and_parse($modules_to_include);
      $result&= cache::update('data_original', $data_orig, '', ['build_date' => core::datetime_get()]);
    }
  # init dynamic and static changes
    $changes_d = data::select('changes') ?: [];
    $changes_s =   $data_orig['changes'] ?? [];
  # apply all changes to original data and get the final data
    $data = core::deep_clone($data_orig);
    static::data_changes_apply($changes_d, $data);
    static::data_changes_apply($changes_s, $data);
    unset($data['changes']);
  # save cache
    foreach ($data as $c_catalog_name => $c_data) {
      static::$data[$c_catalog_name] = $c_data;
      $c_recursive_values = core::arrobj_select_values_recursive($c_data, true);
      foreach ($c_recursive_values as $c_dpath => $c_value) {
        if ($c_value instanceof has_external_cache) {
          $c_cache_id = 'data--'.$c_catalog_name.'-'.str_replace('/', '-', $c_dpath);
          $c_not_external_properties = array_intersect_key((array)$c_value, $c_value::not_external_properties_get());
          $result&= cache::update($c_cache_id, $c_value);
          $c_recursive_values[$c_dpath] = new external_cache(
            $c_cache_id,
            $c_not_external_properties
          );
        }
      }
      $result&= cache::update(
        'data--'.$c_catalog_name, $c_data
      );
    }
    return $result;
  }

  static function data_changes_apply($changes, &$data) {
    foreach ($changes as $module_id => $c_module_changes) {
      foreach ($c_module_changes as $c_action => $c_changes) {
        foreach ($c_changes as $c_dpath => $c_data) {
          $c_pointers   = @core::dpath_get_pointers($data, $c_dpath);
          $c_parnt_name = @array_keys($c_pointers)[count($c_pointers)-2];
          $c_child_name = @array_keys($c_pointers)[count($c_pointers)-1];
          $c_parnt      =            &$c_pointers[$c_parnt_name];
          $c_child      =            &$c_pointers[$c_child_name];
          switch ($c_action) {
            case 'insert': if ($c_child !== null) {foreach ($c_data as $c_key => $c_value) core::arrobj_insert_value($c_child, $c_key,        $c_value);} break; # supported types: array | object
            case 'update': if ($c_parnt !== null) {                                        core::arrobj_insert_value($c_parnt, $c_child_name, $c_data );} break; # supported types: array | object | string | numeric | bool | null
            case 'delete': if ($c_parnt !== null) {                                        core::arrobj_delete_child($c_parnt, $c_child_name          );} break;
          }
        }
      }
    }
  }

  static function data_find_and_parse_modules_and_bundles() {
    $parsed = [];
    $bundles_path = [];
    $modules_path = [];
    foreach (file::select_recursive(dir_system,  '%^.*/module\\.data$%') +
             file::select_recursive(dir_system,  '%^.*/bundle\\.data$%') +
             file::select_recursive(dir_modules, '%^.*/module\\.data$%') +
             file::select_recursive(dir_modules, '%^.*/bundle\\.data$%') as $c_file) {
      $c_data = static::text_to_data($c_file->load(), $c_file);
      $c_path_relative = $c_file->path_get_relative();
      $c_dirs_relative = $c_file->dirs_get_relative();
      $parsed[$c_path_relative] = new \stdClass;
      $parsed[$c_path_relative]->file = $c_file;
      $parsed[$c_path_relative]->data = $c_data;
      if ($c_file->name === 'bundle') $c_data->bundle->path = $bundles_path[$c_data->bundle->id] = $c_dirs_relative;
      if ($c_file->name === 'module') $c_data->module->path = $modules_path[$c_data->module->id] = $c_dirs_relative;
    }
    arsort($bundles_path);
    arsort($modules_path);
    $result = new \stdClass;
    $result->bundles_path = $bundles_path;
    $result->modules_path = $modules_path;
    $result->parsed       = $parsed;
    return $result;
  }

  static function data_find_and_parse($modules_to_include = []) {
    $result       = [];
    $files        = [];
    $preparse     = static::data_find_and_parse_modules_and_bundles();
    $parsed       = $preparse->parsed;
    $bundles_path = $preparse->bundles_path;
    $modules_path = $preparse->modules_path;
    $enabled      = module::get_enabled() + $modules_to_include;
    $is_no_boot   = $enabled === [];
  # if no modules in the boot (when installing)
    if ($enabled === []) {
      foreach ($parsed as $c_info) {
        if (!empty($c_info->data->module) &&
                   $c_info->data->module->enabled === 'yes') {
          $enabled[$c_info->data->module->id] = $c_info->data->module->path;
        }
      }
    }
  # get modules info
    $modules_info = [];
    foreach ($enabled as $c_id => $c_enabled_path) {
      $modules_info[$c_id] = $parsed[$c_enabled_path.'module.data']->data->module;
    }
  # collect *.data files
    arsort($enabled);
    foreach ($enabled as $c_enabled_path) {
      $c_files = file::select_recursive($c_enabled_path,  '%^.*\\.data$%');
      foreach ($c_files as $c_path_relative => $c_file) {
        $c_module_id = key(core::in_array_strpos($c_path_relative, $modules_path));
        if (isset($enabled[$c_module_id])) {
          if ($c_file->name === 'bundle') continue;
          if ($c_file->name === 'module') continue;
          if ($is_no_boot && $modules_info[$c_module_id] instanceof module_as_profile) continue;
          $files[$c_path_relative] = $c_file;
        }
      }
    }
  # parse each collected file
    foreach ($files as $c_path_relative => $c_file) {
      $c_data = static::text_to_data($c_file->load(), $c_file);
      $parsed[$c_path_relative] = new \stdClass;
      $parsed[$c_path_relative]->file = $c_file;
      $parsed[$c_path_relative]->data = $c_data;
    }
  # build the result
    foreach ($parsed as $c_path_relative => $c_file) {
      $c_module_id = key(core::in_array_strpos($c_path_relative, $modules_path));
      foreach ($c_file->data as $c_type => $c_data) {
        if ($c_type === 'bundle') $c_module_id = $c_data->id;
        if ($c_module_id) {
          if (is_object($c_data)) $result[$c_type][$c_module_id] = $c_data;
          elseif (is_array($c_data)) {
            if (!isset($result[$c_type][$c_module_id]))
                       $result[$c_type][$c_module_id] = [];
            $result[$c_type][$c_module_id] += $c_data;
          }
        }
      }
    }
    return $result;
  }

  static function data_to_text($data, $entity_name = '', $entity_prefix = '', $depth = 0) {
    $result = [];
    if (strlen($entity_name) && $depth === 0) $result[] =                              $entity_prefix.$entity_name;
    if (strlen($entity_name) && $depth !== 0) $result[] = str_repeat('  ', $depth - 1).$entity_prefix.$entity_name;
    foreach ($data as $c_key => $c_value) {
      if     (is_object($c_value))                          $result[] = static::data_to_text($c_value, $c_key.(strpos(get_class($c_value), 'effcore\\') === 0 ? '|'.substr(get_class($c_value), 8) : ''), is_array($data) ? '- ' : '  ', $depth + 1);
      elseif (is_array ($c_value) && count($c_value) !== 0) $result[] = static::data_to_text($c_value, $c_key,                                                                                            is_array($data) ? '- ' : '  ', $depth + 1);
      elseif (is_array ($c_value) && count($c_value) === 0) $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.'|_empty_array';
      elseif ($c_value === 'true')                          $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.'|_string_true';
      elseif ($c_value === 'false')                         $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.'|_string_false';
      elseif ($c_value === true)                            $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.': true';
      elseif ($c_value === false)                           $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.': false';
      elseif ($c_value === null)                            $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.': null';
      else                                                  $result[] = str_repeat('  ', $depth).(is_array($data) ? '- ' : '  ').$c_key.': '.$c_value;
    }
    return implode(nl, $result);
  }

  # ┌─────────────────────╥────────────────────────────────────────────────────────────────┐
  # │ valid strings       ║ interpretation                                                 │
  # ╞═════════════════════╬════════════════════════════════════════════════════════════════╡
  # │ root                ║                                                                │
  # │ - name: value       ║ root[name]  = value: null | string | float | integer | boolean │
  # │   name: value       ║ root->name  = value: null | string | float | integer | boolean │
  # │ - =: value          ║ root[value] = value: null | string | float | integer | boolean │
  # │   =: value          ║ root->value = value: null | string | float | integer | boolean │
  # │ - name              ║ root[name]  = new \stdClass | […]                              │
  # │   name              ║ root->name  = new \stdClass | […]                              │
  # │ - name|classname    ║ root[name]  = new classname                                    │
  # │   name|classname    ║ root->name  = new classname                                    │
  # │ - name|_empty_array ║ root[name]  = []                                               │
  # │   name|_empty_array ║ root->name  = []                                               │
  # └─────────────────────╨────────────────────────────────────────────────────────────────┘

  static function text_to_data($data, $file = null) {
    $result = new \stdClass;
    $p = [-1 => &$result];
    $post_cnst_objects = [];
    $post_init_objects = [];
    $post_pars_objects = [];
    $line_number = 0;
    $data = preg_replace('%'.cr.nl.'[>]+|'.cr.'[>]+|'.nl.'[>]+%S', '', $data); # convert 'string_1'.'\n'.'>>>>>>'.'string_2' to 'string_1'.     'string_2'
    $data = preg_replace('%'.cr.nl.'[/]+|'.cr.'[/]+|'.nl.'[/]+%S', a0, $data); # convert 'string_1'.'\n'.'//////'.'string_2' to 'string_1'.'\0'.'string_2'
    $c_line = strtok($data, cr.nl);
    while ($c_line !== false) {
      $line_number++;
      if (strlen(ltrim($c_line, ' ')) &&
                 ltrim($c_line, ' ')[0] !== '#') {
        $matches = [];
        preg_match('%^(?<indent>[ ]*)'.
                     '(?<prefix>- |)'.
                     '(?<name>[^\t].*?)'.
                     '(?<delimiter>(?<!\\\\): |(?<!\\\\)\\||$)'.
                     '(?<value>.*)%sS', str_replace(a0, nl, $c_line) /* convert 'text'.'\0'.'text' to 'text'.'\n'.'text' */, $matches);
        if (array_key_exists('name', $matches)) {
          $c_prefix    = $matches['prefix'];
          $c_depth     = intval(strlen($matches['indent'].$c_prefix) / 2);
          $c_name      = str_replace(['\\:', '\\|'], [':', '|'], $matches['name']);
          $c_delimiter = $matches['delimiter'];
          $c_value     = $matches['value'];
          if ($c_name === '=') $c_name = $c_value;
        # define each value
          if ($c_delimiter === ': ') {
            if (is_numeric($c_value)) $c_value = $c_value += 0;
            if ($c_value === 'true' ) $c_value = true;
            if ($c_value === 'false') $c_value = false;
            if ($c_value === 'null' ) $c_value = null;
          } else {
            if     ($c_value === '_empty_array' ) $c_value = [];
            elseif ($c_value === '_string_true' ) $c_value = 'true';
            elseif ($c_value === '_string_false') $c_value = 'false';
            else {
              $c_class_name = $c_value ? '\\effcore\\'.$c_value : 'stdClass';
              if ($c_class_name !== 'stdClass' && class_exists($c_class_name) === false) {
                message::insert(new text_multiline(['Class "%%_class" does not exist!', 'The class name has been changed to "stdClass".'], ['class' => $c_class_name]), 'error');
                $c_class_name = 'stdClass';
              }
              $c_reflection = new \ReflectionClass($c_class_name);
              $c_is_postconstructor = $c_reflection->implementsInterface('\\effcore\\has_postconstructor');
              $c_is_postinit        = $c_reflection->implementsInterface('\\effcore\\has_postinit');
              $c_is_postparse       = $c_reflection->implementsInterface('\\effcore\\has_postparse');
              if ($c_is_postconstructor)
                   $c_value = core::class_get_new_instance($c_class_name);
              else $c_value = core::class_get_new_instance($c_class_name, [], true);
              if ($c_is_postconstructor) $post_cnst_objects[] = $c_value;
              if ($c_is_postinit       ) $post_init_objects[] = $c_value;
              if ($c_is_postparse      ) $post_pars_objects[] = $c_value;
            }
          }
        # note: on line 2 'property' was recognized as an empty object, but after reading line 3, 'property' must be converted to an array
        # ┌──────┬──────────────────────┬────────────────────────────────────────────────────────────────────────────────────────────┐
        # │      │                      │                                real class in pattern-*.php                                 │
        # │ line │ definition in *.data ├──────────────────────────┬──────────────────────────────────────┬──────────────────────────┤
        # │      │                      │ read line #1             │ read line #2                         │ read line #3             │
        # ╞══════╪══════════════════════╪══════════════════════════╪══════════════════════════════════════╪══════════════════════════╡
        # │    1 │ object|classname     → $object = new classname; │ $object = new classname;             │ $object = new classname; │
        # │    2 │   property           │                          →   $object->property = new \stdClass; │ $object->property = [    │
        # │    3 │   - item: value      │                          │                                      →   'item' => 'value';     │
        # └──────┴──────────────────────┴──────────────────────────┴──────────────────────────────────────┴──────────────────────────┘
          $c_destination = &core::arrobj_select_value($p[$c_depth-1], $c_name);
          if (is_array($c_destination) && $c_value instanceof \stdClass && empty((array)$c_value)) {
            $p[$c_depth] = &$c_destination;
            $c_line = strtok(cr.nl);
            continue;
          }
        # insert new item to tree
          core::arrobj_insert_value($p[$c_depth-1], $c_name, $c_value);
          $p[$c_depth] = &$c_destination;
        # convert parent item to array
          if ($c_prefix === '- ' && !is_array($p[$c_depth-1])) {
            $p[$c_depth-1] = (array)$p[$c_depth-1];
          }
        } else {
          if ($file) message::insert(new text_multiline(['Function: %%_func', 'Wrong syntax in data at line: %%_line', 'File relative path: %%_path', 'Make sure there are no tabs indented.', 'Make sure your editor supports the settings from the ".editorconfig" file.', 'More information can be found in the file "readme/develop.md".'], ['func' => 'text_to_data', 'line' => $line_number, 'path' => $file->path_get_relative()]), 'error');
          else       message::insert(new text_multiline(['Function: %%_func', 'Wrong syntax in data at line: %%_line',                                'Make sure there are no tabs indented.', 'Make sure your editor supports the settings from the ".editorconfig" file.', 'More information can be found in the file "readme/develop.md".'], ['func' => 'text_to_data', 'line' => $line_number                                      ]), 'error');
        }
      }
      $c_line = strtok(cr.nl);
    }
  # call the interface dependent functions
    foreach ($post_cnst_objects as $c_object) $c_object->__construct();
    foreach ($post_init_objects as $c_object) $c_object->_postinit  ();
    foreach ($post_pars_objects as $c_object) $c_object->_postparse ();
  # return result
    return $result;
  }

}}