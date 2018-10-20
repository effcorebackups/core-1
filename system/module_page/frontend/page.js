document.addEventListener('DOMContentLoaded', function(){

  html = document.documentElement;
  uagent = html.getAttribute('data-uagent') ? html.getAttribute('data-uagent') : '';
  uacore = html.getAttribute('data-uacore') ? html.getAttribute('data-uacore') : '';

/* polyfils for addition new functionality in older browsers */
  if (NodeList.prototype.forEach === undefined) {
    NodeList.prototype.forEach = Array.prototype.forEach;
  }

/* this code activate hover state on ios devices */
  document.addEventListener('touchstart', function(){}, false);

});