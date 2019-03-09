document.addEventListener('DOMContentLoaded', function(){

  effcore.get_elements('input[type=range]').for_each(function(element){
    var x_value = element.parentNode.querySelector('x-value');
    if (x_value) {
      element.addEventListener('mousemove', function(){
        x_value.innerText = this.title = this.value;
      });
    }
  });

  effcore.get_elements('select[data-source=uagent-timezone]').for_each(function(element){
    if (element.value == '') {
      var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
      if (timezone) element.value = timezone;
    }
  });

  effcore.get_elements('x-group[data-type=palette]').for_each(function(wrapper){
    var opener = wrapper.querySelector('input[data-opener-type=palette]');
    if (opener) {
      effcore.get_elements('x-field input', wrapper).for_each(function(element){
        element.addEventListener('click', function(){
          opener.style.backgroundColor = element.style.backgroundColor;
          opener.value                 = element.value;
        });
      });
    }
  });

});