<?php

  ##################################################################
  ### Copyright © 2017—2022 Maxim Rysevets. All rights reserved. ###
  ##################################################################

namespace effcore\modules\core {
          use \effcore\core;
          use \effcore\event;
          use \effcore\fieldset;
          use \effcore\group_checkboxes;
          use \effcore\markup;
          use \effcore\message;
          use \effcore\module;
          use \effcore\storage;
          use \effcore\text;
          use \effcore\update;
          abstract class events_form_modules_update_data {

  static function on_init($event, $form, $items) {
    $info = $form->child_select('info');
    $info->children_delete();
    $has_updates = false;
    $modules = module::get_all();
    core::array_sort_by_text_property($modules);
    foreach ($modules as $c_module) {
      $c_updates            = update::select_all        ($c_module->id);
      $c_update_last_number = update::select_last_number($c_module->id);
      if (count($c_updates)) {
        $c_fieldset = new fieldset($c_module->title);
        $c_fieldset->state = 'closed';
        $c_checkboxes = new group_checkboxes;
        $c_checkboxes->element_attributes['name'] = 'update_'.$c_module->id.'[]';
        $c_fieldset->child_insert($c_checkboxes, 'checkboxes');
        $info->child_insert($c_fieldset, $c_module->id);
        core::array_sort_by_property($c_updates, 'number');
        foreach ($c_updates as $c_update) {
          if ($c_update->number > $c_update_last_number === true) {$has_updates = true; $c_fieldset->state = 'opened';}
          if ($c_update->number > $c_update_last_number !== true) {$c_checkboxes->disabled[$c_update->number] = $c_update->number;}
          $c_checkboxes->field_insert(
            $c_update->number.': '.(new text($c_update->title))->render(),
            $c_update->description ?? null,
            $c_update->number
          );
        }
      }
    }
    if ($info->children_select_count() === 0) {
      $form->child_update('info', new markup('x-no-items', ['data-style' => 'table'], 'no updates'));
      $items['~apply']->disabled_set();
    }
    if ($has_updates === false) {
      $items['~apply']->disabled_set();
    }
  }

  static function on_submit($event, $form, $items) {
    switch ($form->clicked_button->value_get()) {
      case 'apply':
        $has_choice = false;
        $modules = module::get_all();
        core::array_sort_by_text_property($modules);
        foreach ($modules as $c_module) {
          $c_updates            = update::select_all        ($c_module->id);
          $c_update_last_number = update::select_last_number($c_module->id);
          if (count($c_updates)) {
            core::array_sort_by_property($c_updates, 'number', 'd');
            foreach ($c_updates as $c_update) {
              if ($c_update->number > $c_update_last_number) {
                if ($items['#update_'.$c_module->id.':'.$c_update->number]->checked_get()) {
                  $has_choice = true;
                  event::start('on_module_update_data_before', $c_module->id, ['update' => $c_update]);
                  $c_result = call_user_func($c_update->handler, $c_update);
                  event::start('on_module_update_data_after', $c_module->id, ['update' => $c_update]);
                  if ($c_result) {
                    update::insert_last_number($c_module->id, $c_update->number);
                           message::insert(new text('Data update #%%_number for module "%%_title" (%%_id) was applied.',     ['title' => (new text($c_module->title))->render(), 'id' => $c_module->id, 'number' => $c_update->number])         );
                  } else { message::insert(new text('Data update #%%_number for module "%%_title" (%%_id) was not applied!', ['title' => (new text($c_module->title))->render(), 'id' => $c_module->id, 'number' => $c_update->number]), 'error');
                    break;
                  }
                }
              }
            }
          }
        }
        if (!$has_choice) {
          message::insert(
            'No one item was selected!', 'warning'
          );
        }
        static::on_init(null, $form, $items);
        break;
    }
  }

}}