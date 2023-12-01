<?php

##################################################################
### Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ###
##################################################################

namespace effcore\modules\poll;

use effcore\Core;
use effcore\Diagram;
use effcore\Markup;
use effcore\Module;
use effcore\Node;
use effcore\Poll;
use effcore\Text;
use effcore\User;

abstract class Events_Selection {

    static function handler__poll__user_type_as_text($c_cell_id, $c_row, $c_instance, $origin) {
        if (array_key_exists('user_type', $c_instance->values_get())) {
            if ($c_instance->user_type === 1) return 'Registered users';
            if ($c_instance->user_type === 0) return 'Anonymous users + Registered users';
        } else return new Text('FIELD "%%_name" IS REQUIRED', ['name' => 'user_type']);
    }

    static function handler__poll__statistics($c_cell_id, $c_row, $c_instance, $origin) {
        if ($c_instance && $c_instance->select()) {
            $settings         = Module::settings_get('poll');
            $diagram_colors   = Core::deep_clone($settings->diagram_colors);
            $answers          = Poll::answers_by_poll_id_select($c_instance->id);
            $votes            = Poll::votes_id_by_user_id_select(User::get_current()->id, array_keys($answers));
            $total            = $c_instance->data['cache']['total']            ?? Poll::votes_total_select              (array_keys($answers));
            $total_by_answers = $c_instance->data['cache']['total_by_answers'] ?? Poll::votes_id_total_by_answers_select(array_keys($answers));

            $diagram = new Diagram(null, $c_instance->diagram_type);
            foreach ($answers as $c_answer) {
                $diagram->slice_insert(new Text($c_answer->answer, [], $origin->is_apply_translation),
                    $total ? ($total_by_answers[$c_answer->id] ?? 0) / $total * 100 : 0,
                             ($total_by_answers[$c_answer->id] ?? 0), array_shift($diagram_colors), ['data-id' => $c_answer->id, 'aria-selected' => isset($votes[$c_answer->id]) ? 'true' : null],
                                                $c_answer->weight
                );
            }

            return new Node([], [
                $diagram,
                new Markup('x-diagram-total', [], [
                    new Markup('x-title', [], new Text('Total', [], $origin->is_apply_translation)),
                    new Markup('x-value', [], $total)
                ])
            ]);
        } else return '';
    }

}
