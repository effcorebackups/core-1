document.addEventListener('DOMContentLoaded', function () {

    /* ───────────────────────────────────────────────────────────────────── */
    /* rearrangeable                                                         */
    /* ───────────────────────────────────────────────────────────────────── */

    document.querySelectorAll('[role="tree"][data-visualization-mode="decorated-rearrangeable"]').forEach(function (c_has_rearrangeable) {
        c_has_rearrangeable.setAttribute('data-js-is-processed', '');

        c_has_rearrangeable.querySelectorAll('x-item[role="treeitem"]').forEach(function (c_rearrangeable) {

            var draggable_icon = document.createElement('x-draggable-icon');
                draggable_icon.setAttribute('draggable', 'true');
                draggable_icon.addEventListener('dragstart', function (event) { window._effDataTransferNode = this; c_has_rearrangeable.   setAttribute('data-has-rearrangeable-is-active', ''); c_rearrangeable.parentNode.   setAttribute('data-rearrangeable-is-active', ''); });
                draggable_icon.addEventListener('dragend',   function (event) { window._effDataTransferNode = null; c_has_rearrangeable.removeAttribute('data-has-rearrangeable-is-active'    ); c_rearrangeable.parentNode.removeAttribute('data-rearrangeable-is-active'    ); });
            c_rearrangeable.prepend(draggable_icon);

            var handler_on_dragover  = function (event) { event.preventDefault();                               };
            var handler_on_dragenter = function (event) { this.   setAttribute('data-droppable-is-active', ''); };
            var handler_on_dragleave = function (event) { this.removeAttribute('data-droppable-is-active'    ); };
            var handler_on_drop      = function (event) { this.removeAttribute('data-droppable-is-active'    );
                var position = this.getAttribute('data-position');
                var drop     = this.parentNode;
                var drag     = window._effDataTransferNode.parentNode.parentNode;
                var c_weight = 0;
                if (position === 'before') drop.parentNode.insertBefore(drag, drop            );
                if (position === 'after' ) drop.parentNode.insertBefore(drag, drop.nextSibling);
                if (position === 'in'    ) drop.querySelector('ul').append(drag);
                drag.querySelector('input[data-type="parent"]').value    = drag.parentNode.parentNode.getAttribute('data-real-id');
                drag.parentNode.parentNode.querySelectorAll('[data-id="' + drag.parentNode.parentNode.getAttribute('data-id') + '"] > ul > li > x-item input[data-type="weight"]').forEach(function (c_input) {
                    c_input.value = c_weight--;
                });
            };

            var droppable_area_0 = document.createElement('x-droppable-area');
            var droppable_area_M = document.createElement('x-droppable-area');
            var droppable_area_N = document.createElement('x-droppable-area');
            droppable_area_0.setAttribute('data-position', 'before');
            droppable_area_M.setAttribute('data-position', 'in'    );
            droppable_area_N.setAttribute('data-position', 'after' );
            [droppable_area_0, droppable_area_M, droppable_area_N].forEach(function (droppable_area) {
                droppable_area.addEventListener('dragover',  handler_on_dragover );
                droppable_area.addEventListener('dragenter', handler_on_dragenter);
                droppable_area.addEventListener('dragleave', handler_on_dragleave);
                droppable_area.addEventListener('drop',      handler_on_drop  );
            });
            c_rearrangeable.parentNode.prepend(droppable_area_0, droppable_area_M);
            c_rearrangeable.parentNode.append (droppable_area_N);

        });

    });

});
