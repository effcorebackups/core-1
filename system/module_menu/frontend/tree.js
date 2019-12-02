document.addEventListener('DOMContentLoaded', function(){

/* rearrangeable */

  document._select_all('x-tree[data-visualization-mode="decorated-rearrangeable"]').forEach(function(c_has_rearrangeable){
    c_has_rearrangeable.setAttribute('data-js-is-processed', 'true');

  });

});