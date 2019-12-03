document.addEventListener('DOMContentLoaded', function(){

/* rearrangeable */

  document._select_all('x-tree[data-visualization-mode="decorated-rearrangeable"]').forEach(function(c_has_rearrangeable){
    c_has_rearrangeable.setAttribute('data-js-is-processed', 'true');
    c_has_rearrangeable._select_all('x-item[role="treeitem"]').forEach(function(c_rearrangeable){

      var draggable_icon = document.createElement('x-draggable-icon');
          draggable_icon.setAttribute('draggable', 'true');
          draggable_icon.addEventListener('dragstart', function(event){ window._dataTransferNode = this; c_has_rearrangeable.   setAttribute('data-has-rearrangeable-is-active', 'true'); c_rearrangeable.   setAttribute('data-rearrangeable-is-active', 'true'); });
          draggable_icon.addEventListener('dragend',   function(event){ window._dataTransferNode = null; c_has_rearrangeable.removeAttribute('data-has-rearrangeable-is-active'        ); c_rearrangeable.removeAttribute('data-rearrangeable-is-active'        ); });
      c_rearrangeable.prepend(draggable_icon);

    });
  });

});