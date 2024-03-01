
//////////////////////////////////////////////////////////////////
/// Copyright © 2017—2024 Maxim Rysevets. All rights reserved. ///
//////////////////////////////////////////////////////////////////

'use strict';

import Core from '/system/module_core/frontend/components/Core.jsd';

document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────────────────────────────────
    // range
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('x-field[data-type="range"] input[type="range"]').forEach((c_input) => {
        c_input.parentNode.querySelectorWithProcessing('x-value', (x_value) => {
            c_input.addEventListener('mousemove', () => {
                x_value.innerText = c_input.title = c_input.value;
            });
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // color
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('x-field[data-type="color"] input[type="color"]').forEach((c_input) => {
        c_input.parentNode.querySelectorWithProcessing('x-value', (x_value) => {
            c_input.addEventListener('change', () => {
                x_value.innerText = c_input.title = c_input.value;
            });
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // timezone
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('select[data-source="uagent-timezone"]').forEach((c_timezone) => {
        if (c_timezone.value === '' && window.Intl) {
            c_timezone.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
        }
    });

    // ─────────────────────────────────────────────────────────────────────
    // operator 'is null' + 'is not null' on Selection edit page
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('x-widget[data-type="items-query-conditions"] [data-type="manage"] x-widget[data-row-id]').forEach((c_row_widget) => {
        let c_field_operator = c_row_widget.querySelector('x-field[data-role="operator"] select');
        let c_field_value    = c_row_widget.querySelector('x-field[data-role="value"] input');
        if (c_field_operator && c_field_value) {
            c_field_operator.addEventListener('change', () => {
                if (c_field_operator.value === 'is null' ||
                    c_field_operator.value === 'is not null') {
                    c_field_value.value = 'n/a';
                }
            });
        }
    });

    // ─────────────────────────────────────────────────────────────────────
    // palette
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('x-group[data-type="palette"]').forEach((c_palette) => {
        c_palette.querySelectorWithProcessing('input[data-opener-type="palette"]', (opener) => {
            c_palette.querySelectorAll('x-field input').forEach((c_input) => {
                c_input.addEventListener('click', () => {
                    opener.style.backgroundColor = c_input.style.backgroundColor;
                    opener.value                 = c_input.value;
                });
            });
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // table-adaptive + check all
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('[data-selection]').forEach((c_selection) => {
        c_selection.querySelectorWithProcessing('[data-decorator][data-view-type="table-adaptive"]', (decorator) => {
            let head_cell       = decorator.querySelector   ('x-head x-cell[data-cell-id$="checkbox_select"]');
            let body_checkboxes = decorator.querySelectorAll('x-body x-cell[data-cell-id$="checkbox_select"] input[type="checkbox"]');
            let query_checker   = 'x-body x-cell[data-cell-id$="checkbox_select"] input[type="checkbox"]:not(:checked)';
            if (head_cell && body_checkboxes.length) {
                // insert checkbox "check_all"
                let check_all = document.createElement('input');
                    check_all.type = 'checkbox';
                    check_all.title = Core.getTranslation('select all rows');
                    check_all.checked = decorator.querySelectorAll(query_checker).length === 0;
                head_cell.append(check_all);
                // when clicking on "check_all"
                check_all.addEventListener('change', () => {
                    body_checkboxes.forEach((c_checkbox) => {
                        c_checkbox.checked = check_all.checked;
                    });
                });
                // when clicking on single checkbox
                body_checkboxes.forEach((c_checkbox) => {
                    c_checkbox.addEventListener('change', () => {
                        check_all.checked = decorator.querySelectorAll(
                            query_checker
                        ).length === 0;
                    });
                });
            }
        });
    });

    // ─────────────────────────────────────────────────────────────────────
    // rearrangeable
    // ─────────────────────────────────────────────────────────────────────

    document.querySelectorAll('x-widgets-group[data-rearrangeable]').forEach(function (c_rearrangeable_group) {
        c_rearrangeable_group.setAttribute('data-js-is-processed', '');
        c_rearrangeable_group.querySelectorAll('[data-rearrangeable-item]').forEach(function (c_rearrangeable) {

            let draggable_icon = c_rearrangeable.querySelector('x-icon');
                draggable_icon.setAttribute('draggable', 'true');
                draggable_icon.addEventListener('dragstart', function (event) { window._effDataTransferNode = this; c_rearrangeable_group.   setAttribute('data-rearrange-is-active', ''); c_rearrangeable.   setAttribute('data-rearrange-is-active', ''); });
                draggable_icon.addEventListener('dragend'  , function (event) { window._effDataTransferNode = null; c_rearrangeable_group.removeAttribute('data-rearrange-is-active'    ); c_rearrangeable.removeAttribute('data-rearrange-is-active'    ); });

            let handler_on_dragover  = function (event) { event.preventDefault();                               };
            let handler_on_dragenter = function (event) { this.   setAttribute('data-droppable-is-active', ''); };
            let handler_on_dragleave = function (event) { this.removeAttribute('data-droppable-is-active'    ); };
            let handler_on_drop      = function (event) { this.removeAttribute('data-droppable-is-active'    );
                let position = this.getAttribute('data-position');
                let drop     = this.parentNode;
                let drag     = window._effDataTransferNode.parentNode;
                let c_weight = 0;
                if (position === 'before') drop.parentNode.insertBefore(drag, drop            );
                if (position === 'after' ) drop.parentNode.insertBefore(drag, drop.nextSibling);
                c_rearrangeable_group.querySelectorAll('x-field[data-type="weight"] input').forEach(function (c_input) {
                    c_input.value = c_weight;
                    c_weight -= 5;
                });
            };

            let droppable_area_0 = document.createElement('x-droppable-area');
            let droppable_area_N = document.createElement('x-droppable-area');
                droppable_area_0.setAttribute('data-position', 'before');
                droppable_area_N.setAttribute('data-position', 'after' );
            [droppable_area_0, droppable_area_N].forEach(function (droppable_area) {
                droppable_area.addEventListener('dragover' , handler_on_dragover );
                droppable_area.addEventListener('dragenter', handler_on_dragenter);
                droppable_area.addEventListener('dragleave', handler_on_dragleave);
                droppable_area.addEventListener('drop'     , handler_on_drop     );
            });
            c_rearrangeable.prepend(droppable_area_0);
            c_rearrangeable.append (droppable_area_N);

        });
    });

});
