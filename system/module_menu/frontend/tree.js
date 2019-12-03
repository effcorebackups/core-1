document.addEventListener('DOMContentLoaded', function(){

/* rearrangeable */

  document.querySelectorAllEff('x-tree[data-visualization-mode="decorated-rearrangeable"]').forEach(function(c_has_rearrangeable){
    c_has_rearrangeable.setAttribute('data-js-is-processed', 'true');
    c_has_rearrangeable.querySelectorAllEff('x-item[role="treeitem"]').forEach(function(c_rearrangeable){

      var draggable_icon = document.createElement('x-draggable-icon');
          draggable_icon.setAttribute('draggable', 'true');
          draggable_icon.addEventListener('dragstart', function(event){ window._dataTransferNode = this; c_has_rearrangeable.   setAttribute('data-has-rearrangeable-is-active', 'true'); c_rearrangeable.   setAttribute('data-rearrangeable-is-active', 'true'); });
          draggable_icon.addEventListener('dragend',   function(event){ window._dataTransferNode = null; c_has_rearrangeable.removeAttribute('data-has-rearrangeable-is-active'        ); c_rearrangeable.removeAttribute('data-rearrangeable-is-active'        ); });
      c_rearrangeable.prepend(draggable_icon);

      var handler_on_dragover  = function(event){ event.preventDefault();                                   },
          handler_on_dragenter = function(event){ this.   setAttribute('data-droppable-is-active', 'true'); },
          handler_on_dragleave = function(event){ this.removeAttribute('data-droppable-is-active'        ); },
          handler_on_drop      = function(event){
            this.removeAttribute('data-droppable-is-active');
          };

      var droppable_area_0 = document.createElement('x-droppable-area'),
          droppable_area_N = document.createElement('x-droppable-area');
          droppable_area_0.setAttribute('data-position', 'before');
          droppable_area_N.setAttribute('data-position', 'after' );
          droppable_area_0.addEventListener('dragover',  handler_on_dragover );
          droppable_area_0.addEventListener('dragenter', handler_on_dragenter);
          droppable_area_0.addEventListener('dragleave', handler_on_dragleave);
          droppable_area_0.addEventListener('drop',      handler_on_drop     );
          droppable_area_N.addEventListener('dragover',  handler_on_dragover );
          droppable_area_N.addEventListener('dragenter', handler_on_dragenter);
          droppable_area_N.addEventListener('dragleave', handler_on_dragleave);
          droppable_area_N.addEventListener('drop',      handler_on_drop     );
      c_rearrangeable.parentNode.prepend(droppable_area_0);
      c_rearrangeable.parentNode.append (droppable_area_N);

    });
  });

});