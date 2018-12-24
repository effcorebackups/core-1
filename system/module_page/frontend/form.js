document.addEventListener('DOMContentLoaded', function(){

  effcore.select_multiple('input[type=range]').for_each(function(element){
    effcore.select('x-value', element.parentNode).for_each(function(value){
      element.addEventListener('mousemove', function(){
        value.innerText = element.title = element.value;
      });
    });
  });

  effcore.select_multiple('select[data-source=uagent-timezone]').for_each(function(element){
    if (element.value == '') {
      var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (timezone) element.value = timezone;
    }
  });

});