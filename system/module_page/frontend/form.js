document.addEventListener('DOMContentLoaded', function(){

  var ranges = document.querySelectorAll('input[type=range]');
  if (ranges instanceof NodeList) {
    ranges.forEach(function(c_range){
      var x_value = c_range.parentNode.querySelector('x-value');
      if (x_value) {
        c_range.addEventListener('mousemove', function(){
          x_value.innerText = c_range.title = c_range.value;
        });
      }
    });
  }

  var timezones = document.querySelectorAll('select[data-source=uagent-timezone]');
  if (timezones instanceof NodeList) {
    timezones.forEach(function(c_timezone){
      if (c_timezone.value == '') {
        var uagent_timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        if (uagent_timezone) c_timezone.value = uagent_timezone;
      }
    });
  }

  var palettes = document.querySelectorAll('x-group[data-type=palette]');
  if (palettes instanceof NodeList) {
    palettes.forEach(function(c_palette){
      var opener = c_palette.querySelector('input[data-opener-type=palette]'),
          inputs = c_palette.querySelectorAll('x-field input');
      if (opener && inputs instanceof NodeList) {
        inputs.forEach(function(c_input){
          c_input.addEventListener('click', function(){
            opener.style.backgroundColor = c_input.style.backgroundColor;
            opener.value                 = c_input.value;
          });
        });
      }
    });
  }

  var selections = document.querySelectorAll('x-selection');
  if (selections instanceof NodeList) {
    selections.forEach(function(c_selection){
      var th_for_checkbox = c_selection.querySelector('x-decorator[data-view-type=table] th[data-cellid=checkbox]'),
          checkboxes      = th_for_checkbox ? th_for_checkbox.parentNode.parentNode.parentNode.querySelectorAll('td[data-cellid=checkbox] input[type=checkbox]') : null;
      if (th_for_checkbox && checkboxes instanceof NodeList) {
        var check_all = document.createElement('input');
            check_all.type = 'checkbox';
        th_for_checkbox.appendChild(check_all);
        check_all.addEventListener('change', function(){
          checkboxes.forEach(function(c_checkbox){
            c_checkbox.checked = check_all.checked;
          });
        })
      }
    });
  }

});