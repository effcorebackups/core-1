document.addEventListener('DOMContentLoaded', function(){

/* drag-and-drop functionality */

  var trees_managed = document.querySelectorAll('x-tree[data-managed-is-on="true"]');
  if (trees_managed instanceof NodeList) {
    trees_managed.forEach(function(c_tree){

      var draggable = c_tree.querySelectorAll('[draggable="true"]');
      if (draggable instanceof NodeList) {
        draggable.forEach(function(c_draggable){
          c_draggable.addEventListener('dragstart', function(event){event.stopPropagation(); c_tree.setAttribute   ('data-drag-active', 'true'); c_draggable.setAttribute   ('data-drag-active', 'true'); event.dataTransfer.setData('text/plain', c_draggable.getAttribute('data-id'));}, false);
          c_draggable.addEventListener('dragend',   function(event){event.stopPropagation(); c_tree.removeAttribute('data-drag-active'        ); c_draggable.removeAttribute('data-drag-active'        );                                                                               }, false);
        });
      }

      var droppable = c_tree.querySelectorAll('x-drop_area');
      if (droppable instanceof NodeList) {
        droppable.forEach(function(c_droppable){
          c_droppable.addEventListener('dragover',  function(event){event.stopPropagation(); event.preventDefault(); c_droppable.setAttribute   ('data-drop-hover', 'true');}, false);
          c_droppable.addEventListener('dragleave', function(event){event.stopPropagation(); event.preventDefault(); c_droppable.removeAttribute('data-drop-hover'        );}, false);
          c_droppable.addEventListener('drop',      function(event){event.stopPropagation(); event.preventDefault();
            var draggable_id = event.dataTransfer.getData('text/plain'),
                draggable = c_tree.querySelector('[data-id="'+draggable_id+'"]');
            switch (c_droppable.getAttribute('data-type')) {
              case 'before':
                c_droppable.removeAttribute('data-drop-hover');
                c_droppable.parentNode.parentNode.insertBefore(
                  c_tree.querySelector('[data-id="'+draggable_id+'"]'),
                  c_droppable.parentNode
                );
                break;
              case 'in':
                c_droppable.removeAttribute('data-drop-hover');
                c_droppable.parentNode.querySelector('ul').appendChild(
                  c_tree.querySelector('[data-id="'+draggable_id+'"]')
                );
                break;
              case 'after':
                c_droppable.removeAttribute('data-drop-hover');
                if (c_droppable.parentNode.nextSibling) {
                  c_droppable.parentNode.parentNode.insertBefore(
                    c_tree.querySelector('[data-id="'+draggable_id+'"]'),
                    c_droppable.parentNode.nextSibling
                  );
                } else {
                  c_droppable.parentNode.parentNode.appendChild(
                    c_tree.querySelector('[data-id="'+draggable_id+'"]')
                  );
                }
                break;
            }
          }, false);
        });
      }

    });
  }

});