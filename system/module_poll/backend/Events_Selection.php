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
use effcore\User;

abstract class Events_Selection {

    static function handler__poll__user_type($c_row_id, $c_row, $c_instance, $settings = []) {
        if ($c_row['user_type']['value'] === '1') return 'Registered users';
        if ($c_row['user_type']['value'] === '0') return 'Anonymous users + Registered users';
    }

    static function handler__poll__statistics($c_row_id, $c_row, $c_instance, $settings = []) {
        $c_instance->select();

        $settings         = Module::settings_get('poll');
        $diagram_colors   = Core::deep_clone($settings->diagram_colors);
        $answers          = Poll::answers_by_poll_id_select($c_instance->id);
        $votes            = Poll::votes_id_by_user_id_select(User::get_current()->id, array_keys($answers));
        $total            = $c_instance->data['cache']['total']            ?? Poll::votes_total_select              (array_keys($answers));
        $total_by_answers = $c_instance->data['cache']['total_by_answers'] ?? Poll::votes_id_total_by_answers_select(array_keys($answers));

        $diagram = new Diagram(null, $c_instance->diagram_type);
        foreach ($answers as $c_answer) {
            $diagram->slice_insert(         $c_answer->answer,
                $total ? ($total_by_answers[$c_answer->id] ?? 0) / $total * 100 : 0,
                         ($total_by_answers[$c_answer->id] ?? 0), array_shift($diagram_colors), ['data-id' => $c_answer->id, 'aria-selected' => isset($votes[$c_answer->id]) ? 'true' : null],
                                            $c_answer->weight
            );
        }

        return new Node([], [
            $diagram,
            new Markup('x-diagram-total', [], [
                new Markup('x-title', [], 'Total'),
                new Markup('x-value', [], $total)
            ])
        ]);
    }

}
