document.addEventListener('DOMContentLoaded', function(){

/* range */

  document._select_all('input[type="range"]').forEach(function(c_range){
    c_range.parentNode._select('x-value').forFirstItem(function(x_value){
      c_range.addEventListener('mousemove', function(){
        x_value.innerText = c_range.title = c_range.value;
      });
    });
  });

/* timezone */

  document._select_all('select[data-source="uagent-timezone"]').forEach(function(c_timezone){
    if (c_timezone.value == '' && window.Intl)
        c_timezone.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
  });

/* palette */

  document._select_all('x-group[data-type="palette"]').forEach(function(c_palette){
    c_palette._select('input[data-opener-type="palette"]').forFirstItem(function(opener){
      c_palette._select_all('x-field input').forEach(function(c_input){
        c_input.addEventListener('click', function(){
          opener.style.backgroundColor = c_input.style.backgroundColor;
          opener.value                 = c_input.value;
        });
      });
    });
  });

/* table-adaptive + check all */

  document._select_all('x-selection').forEach(function(c_selection){
    c_selection._select('x-decorator[data-view-type="table-adaptive"]').forFirstItem(function(decorator){
      var head_cell       = decorator._select    ('x-head x-cell[data-cellid="checkbox"]'                       ),
          body_checkboxes = decorator._select_all('x-body x-cell[data-cellid="checkbox"] input[type="checkbox"]');
      if (head_cell.length == 1 && body_checkboxes.length) {
        var checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.title = effcore.tokens['text_select_all_rows'];
        head_cell[0].appendChild(checkbox);
        checkbox.addEventListener('change', function(){
          body_checkboxes.forEach(function(c_checkbox){
            c_checkbox.checked = checkbox.checked;
          });
        });
      }
    });
  });

/* rearrangeable */

  document._select_all('[data-has-rearrangeable="true"]').forEach(function(c_has_rearrangeable){
    c_has_rearrangeable._select_all('[data-rearrangeable="true"]').forEach(function(c_rearrangeable){
      var draggable_icon = document.createElement('x-draggable-icon');
          draggable_icon.setAttribute('draggable', 'true');
          draggable_icon.addEventListener('dragstart', function(event){ c_has_rearrangeable.   setAttribute('data-has-rearrangeable-is-active', 'true'); c_rearrangeable.   setAttribute('data-draggable-is-active', 'true'); });
          draggable_icon.addEventListener('dragend',   function(event){ c_has_rearrangeable.removeAttribute('data-has-rearrangeable-is-active'        ); c_rearrangeable.removeAttribute('data-draggable-is-active'        ); });
      c_rearrangeable.prepend(draggable_icon);
      var handler_on_dragover  = function(event){ event.preventDefault();                                },
          handler_on_dragenter = function(event){ this.   setAttribute('data-droppable-active', 'true'); },
          handler_on_dragleave = function(event){ this.removeAttribute('data-droppable-active'        ); };
          handler_on_drop      = function(event){ this.removeAttribute('data-droppable-active'        ); };
      var droppable_area_0 = document.createElement('x-droppable-area'),
          droppable_area_N = document.createElement('x-droppable-area');
          droppable_area_0.setAttribute('data-position', 'before');
          droppable_area_N.setAttribute('data-position', 'after' );
          droppable_area_0.addEventListener('dragover',  handler_on_dragover );
          droppable_area_0.addEventListener('dragenter', handler_on_dragenter);
          droppable_area_0.addEventListener('dragleave', handler_on_dragleave);
          droppable_area_0.addEventListener('drop',      handler_on_drop     );
          droppable_area_N.addEventListener('dragover',  handler_on_dragover );
          droppable_area_N.addEventListener('dragenter', handler_on_dragenter);
          droppable_area_N.addEventListener('dragleave', handler_on_dragleave);
          droppable_area_N.addEventListener('drop',      handler_on_drop     );
      c_rearrangeable.prepend(droppable_area_0);
      c_rearrangeable.append (droppable_area_N);
    });
  });

});