var Helper = function() {};

/**
* Make any element draggable.
*
* @param {string} id or class name of html element which is grabable
* @param {string} id or class name of html element to be moved
*/
Helper.prototype.makeDraggable = function(grabable, moveable) {
     var grabable = $(grabable);

     grabable.on('mousedown', moveable, function() {
         grabable.addClass('draggable').parents().on('mousemove', function(e) {
             e.preventDefault();

             var draggable = $('.draggable');
             var y = e.pageY - draggable.outerHeight() / 2;
             var x = e.pageX - draggable.outerWidth() / 2;

             /* put the mouse at the center of the moveable object and keep
              * it there as we move the mouse, until we release the mouse button */
             draggable.offset({ top: y, left: x }).on('mouseup', function() {
                 $(grabable).removeClass('draggable');
             });
         });
     }).on('mouseup', function() {
         $('.draggable').removeClass('draggable');
     });
};
