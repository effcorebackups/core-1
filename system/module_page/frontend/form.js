document.addEventListener('DOMContentLoaded', function(){

  effcore.select_multiple('input[type=range]').forEach(function(element){
    effcore.select('x-value', element.parentNode).forEach(function(value){
      element.addEventListener('mousemove', function(){
        value.innerText = element.title = element.value;
      });
    });
  });

  effcore.select_multiple('select[data-source=uagent-timezone]').forEach(function(element){
    if (element.value == '') {
      var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (timezone) element.value = timezone;
    }
  });

});