
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2023 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

document.addEventListener('DOMContentLoaded', function () {
    document.querySelector__withHandler('[data-css-path="develop-tests-class__url__js"] form[id="test"]', (c_form) => {
        c_form.addEventListener('submit', (event) => {
            event.preventDefault();

            let tests = [
                {testClass : Events_Test__Class_Url, 'method' : 'test_step_code__construct'},
                {testClass : Events_Test__Class_Url, 'method' : 'test_step_code__has_error'},
                {testClass : Events_Test__Class_Url, 'method' : 'test_step_code__absolute_get'},
                {testClass : Events_Test__Class_Url, 'method' : 'test_step_code__relative_get'},
                {testClass : Events_Test__Class_Url, 'method' : 'test_step_code__query_args'}
            ];

            let has_error = false;

            for (let c_test of tests) {
                c_test.obj = new EffcoreTest(c_test.testClass, c_test.method);
                c_test.result = c_test.obj.run();
                if (c_test.result === 0) {
                    has_error = true;
                    Effcore.messageAdd(
                        Effcore.getTranslation('The test was failed!'), 'error'
                    );
                    break;
                }
            }

            if (has_error === false) {
                Effcore.messageAdd(
                    Effcore.getTranslation('The test was successful.')
                );
            }

            let report_place = document.querySelector('x-document[data-style=report]');
            report_place.innerHTML = '';
            for (let c_test of tests) {
                for (let c_line of c_test.obj.reportGet()) {
                    report_place.innerHTML += c_line + '</br>';
                }
            }

        });
    });
});
