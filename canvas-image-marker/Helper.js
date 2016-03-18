var Helper = function() {};

/**
* Make any element draggable.
*
* @param {string} id or class name of html element which is grabable
* @param {string} id or class name of html element to be moved
*/
Helper.prototype.makeDraggable = function(grabable, moveable) {
     $(grabable).on('mousedown', moveable, function() {
         $(moveable).addClass('draggable').parents().on('mousemove', function(e) {
             e.preventDefault();
             $('.draggable').offset({
                 top: e.pageY - $('.draggable').outerHeight() / 2,
                 left: e.pageX - $('.draggable').outerWidth() / 2
             }).on('mouseup', function() {
                 $(moveable).removeClass('draggable');
             });
         });
     }).on('mouseup', function() {
         $('.draggable').removeClass('draggable');
     });
};
