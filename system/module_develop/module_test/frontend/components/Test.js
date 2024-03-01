
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import Core from '/system/module_core/frontend/components/Core.jsd';

// ─────────────────────────────────────────────────────────────────────
// Class Test
// ─────────────────────────────────────────────────────────────────────

export default class Test {

    static run(Events, output) {
        output.innerHTML = '';
        for (const c_method of Events.methods()) {
            output.innerHTML += Core.argsApply(Core.getTranslation('call "@@_call"'), {'call' : `${c_method.name}`}) + '<br>';
            output.innerHTML += `### ${c_method.name}`;
            for (const c_tick of c_method(c_method.name)) {
                if      (c_tick === true ) { Core.messageAdd(Core.getTranslation('The test was successful.'));      return; }
                else if (c_tick === false) { Core.messageAdd(Core.getTranslation('The test was failed!'), 'error'); return; }
                else output.innerHTML += `${c_tick}<br>`;
            }
            output.innerHTML += '<br>';
        }
    }

}
