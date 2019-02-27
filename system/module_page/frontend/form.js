document.addEventListener('DOMContentLoaded', function(){

  effcore.get_elements('input[type=range]').for_each(function(element){
    effcore.get_element('x-value', element.parentNode).for_each(function(value){
      element.addEventListener('mousemove', function(){
        value.innerText = element.title = element.value;
      });
    });
  });

  effcore.get_elements('select[data-source=uagent-timezone]').for_each(function(element){
    if (element.value == '') {
      var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (timezone) element.value = timezone;
    }
  });

  effcore.get_elements('x-group[data-type=palette]').for_each(function(wrapper){
    var opener = effcore.get_element('input[data-opener-type=palette]', wrapper).select(0);
    effcore.get_elements('x-field input', wrapper).for_each(function(element){
      element.addEventListener('click', function(){
        opener.style.backgroundColor = element.style.backgroundColor;
      });
    });
  });

});