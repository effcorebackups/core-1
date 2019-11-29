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

/* draggable */

  document._select_all('[data-has-draggable="true"]').forEach(function(c_has_draggable){
    c_has_draggable._select_all('[data-draggable="true"]').forEach(function(c_draggable){
      var draggable_icon = document.createElement('x-draggable-icon');
          draggable_icon.setAttribute('draggable', 'true');
          draggable_icon.addEventListener('dragstart', function(event){ c_draggable.   setAttribute('data-draggable-active', 'true'); });
          draggable_icon.addEventListener('dragend',   function(event){ c_draggable.removeAttribute('data-draggable-active'        ); });
      c_draggable.prepend(draggable_icon);
    });
  });

});