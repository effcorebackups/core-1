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
      var droppable = c_tree.querySelectorAll('x-drop_area');
      if (droppable instanceof NodeList) {
        droppable.forEach(function(c_droppable){
          c_droppable.addEventListener('dragover',  function(event){event.stopPropagation(); c_droppable.setAttribute   ('data-drag-active', 'true');}, false);
          c_droppable.addEventListener('dragleave', function(event){event.stopPropagation(); c_droppable.removeAttribute('data-drag-active'        );}, false);
        });
      }
    });
  }

});