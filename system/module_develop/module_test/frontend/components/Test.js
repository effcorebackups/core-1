
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import Core from '/system/module_core/frontend/components/Core.jsd';
import Message from '/system/module_core/frontend/components/Message.js';
import Translation from '/system/module_locale/frontend/components/Translation.jsd';

// ─────────────────────────────────────────────────────────────────────
// Class Test
// ─────────────────────────────────────────────────────────────────────

export default class Test {

    static run(Events, output) {
        output.innerHTML = '';
        for (const c_method of Events.methods()) {
            output.innerHTML += Core.argsApply(Translation.get('call "%%_call"'), {'call' : `${c_method.name}`}) + '<br>';
            output.innerHTML += `### ${c_method.name}<br>`;
            for (const c_tick of c_method(c_method.name)) {
                if      (c_tick === true ) { Message.add(Translation.get('The test was successful.'));      return; }
                else if (c_tick === false) { Message.add(Translation.get('The test was failed!'), 'error'); return; }
                else output.innerHTML += `${c_tick}<br>`;
            }
            output.innerHTML += '<br>';
        }
    }

}
