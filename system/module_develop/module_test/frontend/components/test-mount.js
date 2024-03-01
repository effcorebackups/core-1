
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import Core from '/system/module_core/frontend/components/Core.jsd';
import Test from './Test.js';

document.addEventListener('DOMContentLoaded', () => {

    import(window.test_current_path)
        .then((module) => {
            document.querySelector('form[id="test"]')?.addEventListener('submit', () => {
                event.preventDefault();
                Test.run(module.default, document.querySelector('x-document[data-style="report-test"]'));
            });
        })
        .catch((err) => {
            console.log(err);
        });

});
