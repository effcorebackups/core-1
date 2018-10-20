document.addEventListener('DOMContentLoaded', function(){

  document.querySelectorAll('input[type=range]').forEach(function(range){
    var x_value = range.parentNode.querySelector('x-value');
    range.addEventListener('mousemove', function(){
      x_value.innerText = range.value;
    });
  });

});