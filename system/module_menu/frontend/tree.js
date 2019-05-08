document.addEventListener('DOMContentLoaded', function(){

/* drag-and-drop functionality */

  var trees_managed = document.querySelectorAll('x-tree[data-managed-is-on="true"]');
  if (trees_managed instanceof NodeList) {
    trees_managed.forEach(function(c_tree){
      var draggable = c_tree.querySelectorAll('*[draggable="true"]');
      if (draggable instanceof NodeList) {
        draggable.forEach(function(c_draggable){
          c_draggable.addEventListener('dragstart', function(event){
            event.stopPropagation();
            event.dataTransfer.setData('id',
              c_draggable.getAttribute('data-id')
            );
          }, false);
        });
      }
      var droppable = c_tree.querySelectorAll('x-drop_area');
      if (droppable instanceof NodeList) {
        droppable.forEach(function(c_droppable){
          c_droppable.addEventListener('dragover',  function(event){event.stopPropagation(); c_droppable.setAttribute   ('data-drag-active', 'true'); event.preventDefault();}, false);
          c_droppable.addEventListener('dragleave', function(event){event.stopPropagation(); c_droppable.removeAttribute('data-drag-active'        ); event.preventDefault();}, false);
          c_droppable.addEventListener('drop',      function(event){event.stopPropagation();
            var draggable_id = event.dataTransfer.getData('id'),
                draggable = c_tree.querySelector('[data-id="'+draggable_id+'"]');
            switch (c_droppable.getAttribute('data-type')) {
              case 'before':
                break;
              case 'in':
                c_droppable.removeAttribute('data-drag-active');
                c_droppable.parentNode.querySelector('ul').appendChild(
                  c_tree.querySelector('[data-id="'+draggable_id+'"]')
                );
                break;
              case 'after':
                break;
            }
          }, false);
        });
      }
    });
  }

});