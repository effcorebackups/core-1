document.addEventListener('DOMContentLoaded', function(){

  var trees_managed = document.querySelectorAll('x-tree[data-managed-is-on="true"]');
  if (trees_managed instanceof NodeList) {
    trees_managed.forEach(function(c_tree){
      var draggable = c_tree.querySelectorAll('*[draggable="true"]');
      if (draggable instanceof NodeList) {
        draggable.forEach(function(c_draggable){
          c_draggable.addEventListener('dragstart', function(event){
            event.stopPropagation();
            /* alert( c_draggable.getAttribute('data-id') ); */
          }, false);
        });
      }
    });
  }

});