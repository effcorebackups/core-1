document.addEventListener('DOMContentLoaded', function(){

/* range */

  document.effSelectAll('input[type="range"]').forEach(function(c_range){
    c_range.parentNode.effSelect('x-value').forOne(function(x_value){
      c_range.addEventListener('mousemove', function(){
        x_value.innerText = c_range.title = c_range.value;
      });
    });
  });

/* timezone */

  document.effSelectAll('select[data-source="uagent-timezone"]').forEach(function(c_timezone){
    if (c_timezone.value == '' && window.Intl)
        c_timezone.value = Intl.DateTimeFormat().resolvedOptions().timeZone;
  });

/* palette */

  document.effSelectAll('x-group[data-type="palette"]').forEach(function(c_palette){
    var opener = c_palette.querySelector('input[data-opener-type="palette"]');
    if (opener) {
      c_palette.effSelectAll('x-field input').forEach(function(c_input){
        c_input.addEventListener('click', function(){
          opener.style.backgroundColor = c_input.style.backgroundColor;
          opener.value                 = c_input.value;
        });
      });
    }
  });

/* table-adaptive */

  var selections = document.querySelectorAll('x-selection');
  if (selections instanceof NodeList) {
    selections.forEach(function(c_selection){
      var x_head_cell = c_selection.querySelector('x-decorator[data-view-type="table-adaptive"]      x-head x-cell[data-cellid="checkbox"]'),
          checkboxes  = x_head_cell ? x_head_cell.parentNode.parentNode.parentNode.querySelectorAll('x-body x-cell[data-cellid="checkbox"] input[type="checkbox"]') : null;
      if (x_head_cell && checkboxes instanceof NodeList) {
        var check_all = document.createElement('input');
            check_all.type = 'checkbox';
            check_all.title = effcore.tokens['text_select_all_rows'];
        x_head_cell.appendChild(check_all);
        check_all.addEventListener('change', function(){
          checkboxes.forEach(function(c_checkbox){
            c_checkbox.checked = check_all.checked;
          });
        })
      }
    });
  }

/* draggable */

  var has_draggable_list = document.querySelectorAll('[data-has-draggable="true"]');
  if (has_draggable_list instanceof NodeList) {
    has_draggable_list.forEach(function(c_has_draggable){

      var draggable_list = c_has_draggable.querySelectorAll('[data-draggable="true"]');
      if (draggable_list instanceof NodeList) {
        draggable_list.forEach(function(c_draggable){
          var c_draggable_icon = document.createElement('x-draggable-icon');
          c_draggable_icon.setAttribute('draggable', 'true');
          c_draggable.prepend(c_draggable_icon);

        });
      }

    });
  }

});