document.addEventListener('DOMContentLoaded', function(){

/* polyfils */

  NodeList.prototype.forEach = Array.prototype.forEach;

/* this code activate hover state on ios devices */

  document.addEventListener('touchstart', function(){}, false);

/* base functions */

  function select(query, root, multiple = false) {
    this.query = query;
    if (multiple) this.doSome = function(user_func) {var result = root.querySelectorAll(query); if (result instanceof NodeList) for (var i = 0; i < result.length; i++) user_func.apply(this, [result[i]]); }
    else          this.doSome = function(user_func) {var result = root.querySelector   (query); if (result instanceof Node    )                                         user_func.apply(this, [result   ]); }
  }

/* form support */

  (new select('input[type=range]', document, true)).doSome(function(element){
    (new select('x-value', element.parentNode)).doSome(function(value){
      element.addEventListener('mousemove', function(){
        value.innerText = element.value;
      });
    });
  });

  (new select('select[data-source=uagent-timezone]', document, true)).doSome(function(element){
    var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    if (timezone) element.value = timezone;
  });

});