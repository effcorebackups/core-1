<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use effcore\Core;
use effcore\Event;
use effcore\Fieldset;
use effcore\Group_Checkboxes;
use effcore\Markup;
use effcore\Message;
use effcore\Module;
use effcore\Text;
use effcore\Update;

abstract class Events_Form_Modules_Update_data {

    static function on_build($event, $form) {
        $form->child_select('info')->children_delete();
        $form->_has_updates = false;
        $modules = Module::get_all();
        Core::array_sort_by_string($modules);
        $fieldset_number = 0;
        foreach ($modules as $c_module) {
            $c_updates            = Update::select_all        ($c_module->id);
            $c_update_last_number = Update::select_last_number($c_module->id);
            if (count($c_updates)) {
                $c_fieldset = new Fieldset($c_module->title);
                $c_fieldset->number = $fieldset_number++;
                $c_fieldset->state = 'closed';
                $c_checkboxes = new Group_Checkboxes;
                $c_checkboxes->element_attributes['name'] = 'update_'.$c_module->id.'[]';
                $c_fieldset->child_insert($c_checkboxes, 'checkboxes');
                $form->child_select('info')->child_insert($c_fieldset, $c_module->id);
                Core::array_sort_by_number($c_updates, 'number', Core::SORT_ASC);
                $c_checkboxes_items = [];
                foreach ($c_updates as $c_update) {
                    if ($c_update->number > $c_update_last_number === true) {$form->_has_updates = true; $c_fieldset->state = 'opened';}
                    if ($c_update->number > $c_update_last_number !== true) {$c_checkboxes->disabled[$c_update->number] = $c_update->number;}
                    if (!empty($c_update->description))
                         $c_checkboxes_items[$c_update->number] = (object)['title' => $c_update->number.': '.(new Text($c_update->title))->render(), 'description' => $c_update->description];
                    else $c_checkboxes_items[$c_update->number] = (object)['title' => $c_update->number.': '.(new Text($c_update->title))->render()]; }
                $c_checkboxes->items_set($c_checkboxes_items);
            }
        }
        if ($form->child_select('info')->children_select_count() === 0) {
            $form->child_update('info',
                new Markup('x-no-items', ['data-style' => 'table'], 'No updates.')
            );
        }
    }

    static function on_init($event, $form, $items) {
        $items['~apply']->disabled_set($form->_has_updates === false);
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->value_get()) {
            case 'apply':
                $has_choice = false;
                $modules = Module::get_all();
                Core::array_sort_by_string($modules);
                foreach ($modules as $c_module) {
                    $c_updates            = Update::select_all        ($c_module->id);
                    $c_update_last_number = Update::select_last_number($c_module->id);
                    if (count($c_updates)) {
                        Core::array_sort_by_number($c_updates, 'number', Core::SORT_DSC);
                        foreach ($c_updates as $c_update) {
                            if ($c_update->number > $c_update_last_number) {
                                if ($items['#update_'.$c_module->id.':'.$c_update->number]->checked_get()) {
                                    $has_choice = true;
                                    Event::start('on_module_update_data_before', $c_module->id, ['update' => $c_update]);
                                    $c_result = call_user_func($c_update->handler, $c_update);
                                    Event::start('on_module_update_data_after', $c_module->id, ['update' => $c_update]);
                                    if ($c_result) {
                                        Update::insert_last_number($c_module->id, $c_update->number);
                                             Message::insert(new Text('Data update #%%_number for module "%%_title" (%%_id) was applied.',     ['title' => (new Text($c_module->title))->render(), 'id' => $c_module->id, 'number' => $c_update->number])         );
                                    } else { Message::insert(new Text('Data update #%%_number for module "%%_title" (%%_id) was not applied!', ['title' => (new Text($c_module->title))->render(), 'id' => $c_module->id, 'number' => $c_update->number]), 'error');
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
                if (!$has_choice) {
                    Message::insert(
                        'No one item was selected!', 'warning'
                    );
                } else {
                    static::on_build(null, $form);
                    static::on_init (null, $form, $form->items_update());
                }
                break;
        }
    }

}
