<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\core;

use const effcore\BR;
use const effcore\DIR_ROOT;
use effcore\Button;
use effcore\Core;
use effcore\Event;
use effcore\Fieldset;
use effcore\Markup;
use effcore\Module;
use effcore\Text_multiline;
use effcore\Text;

abstract class Events_Form_Modules_Update_files {

    static function on_init($event, $form, $items) {
        $info = $form->child_select('info');
        $info->children_delete();
        $bundles = Module::bundle_get_all();
        Core::array_sort_by_string($bundles);
        foreach ($bundles as $c_bundle) {
            if (isset($c_bundle->repo_update_handler_in_module) && Module::is_enabled(
                      $c_bundle->repo_update_handler_in_module)) {
                $c_repo_settings_path = Core::validate_realpath(DIR_ROOT.
                    $c_bundle->path.
                    $c_bundle->repo_directory.'/.'.
                    $c_bundle->repo_type);
                $c_button_update = new Button('update', ['title' => new Text('update')]);
                $c_button_update->build();
                $c_button_update->value_set('update_'.$c_bundle->id);
                $c_button_update->disabled_set($c_repo_settings_path === false);
                $c_button_update->_type = 'update';
                $c_button_update->_id = $c_bundle->id;
                $c_button_repo_restore = new Button('restore repository', ['title' => new Text('restore repository')]);
                $c_button_repo_restore->build();
                $c_button_repo_restore->disabled_set(empty($c_bundle->repo_can_restore));
                $c_button_repo_restore->value_set('repo_restore_'.$c_bundle->id);
                $c_button_repo_restore->_type = 'repo_restore';
                $c_button_repo_restore->_id = $c_bundle->id;
                $c_report = new Markup('x-document', ['data-style' => 'report'], new Text('The report will be created after submitting the form.'));
                $c_fieldset = new Fieldset($c_bundle->title);
                $c_fieldset->child_insert($c_report, 'report');
                $c_fieldset->child_insert($c_button_update, 'button_update');
                $c_fieldset->child_insert($c_button_repo_restore, 'button_repo_restore');
                $info->child_insert($c_fieldset, $c_bundle->id);
            }
        }
        if ($info->children_select_count() === 0) {
            $form->child_update('info', new Markup('x-no-items', ['data-style' => 'table'], 'No updates.'));
        }
    }

    static function on_submit($event, $form, $items) {
        switch ($form->clicked_button->_type) {
            case 'update':
                static::on_init(null, $form, $items);
                $bundle_id = $form->clicked_button->_id;
                $result = Event::start('on_update_files', $bundle_id, ['bundle_id' => $bundle_id]);
                $report = $items['info']->child_select($bundle_id)->child_select('report');
                $report->children_delete();
                foreach ($result as $c_handler => $c_results) {
                    $report->child_insert(new Markup('p', [], 'Call '.$c_handler));
                    foreach ($c_results as $c_result) {
                        if (is_null ($c_result)) $report->child_insert(new Markup('p', [], 'null'));
                        if (is_array($c_result)) $report->child_insert(new Markup('p', [], new Text_multiline($c_result, [], BR, false, false)));
                    }
                }
                break;
            case 'repo_restore':
                static::on_init(null, $form, $items);
                $bundle_id = $form->clicked_button->_id;
                $result = Event::start('on_repo_restore', $bundle_id, ['bundle_id' => $bundle_id]);
                $report = $items['info']->child_select($bundle_id)->child_select('report');
                $report->children_delete();
                foreach ($result as $c_handler => $c_results) {
                    $report->child_insert(new Markup('p', [], 'Call '.$c_handler));
                    foreach ($c_results as $c_result) {
                        if (is_null ($c_result)) $report->child_insert(new Markup('p', [], 'null'));
                        if (is_array($c_result)) $report->child_insert(new Markup('p', [], new Text_multiline($c_result, [], BR, false, false)));
                    }
                }
                break;
        }
    }

}
